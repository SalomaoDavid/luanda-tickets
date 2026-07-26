<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Models\Bilhete;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    // ── SHOW ──────────────────────────────────────────────────
    public function show($id): View
    {
        $user = User::select(
                'id','name','email','avatar','cover','bio',
                'role','is_verified','last_seen','created_at'
            )
            ->withCount(['postagens','seguidores','seguindo'])
            ->findOrFail($id);

        $isOwner = auth()->id() === $user->id;

        // Postagens paginadas — máx 10 por vez
        $postagens = $user->postagens()
            ->select('id','user_id','conteudo','imagem','created_at')
            ->latest()
            ->paginate(10, ['*'], 'pag_posts');

        // Bilhetes paginados — só confirmados
        $bilhetes = Bilhete::whereHas('pedido', fn($q) =>
                $q->where('user_id', $id)->where('status','pago')
            )
            ->with([
                'evento:id,titulo,localizacao,data_evento,hora_inicio',
                'tipoIngresso:id,nome,preco',
            ])
            ->select('id','pedido_id','evento_id','tipo_ingressos_id','codigo_unico','validado_em','created_at')
            ->latest()
            ->paginate(8, ['*'], 'pag_bilhetes');

        // Eventos paginados por role
        [$eventos, $statsLabel, $statsCount, $statsLabel2, $statsCount2]
            = $this->carregarEventos($user);

        return view('profile.show', compact(
            'user','isOwner','postagens','eventos','bilhetes',
            'statsLabel','statsCount','statsLabel2','statsCount2'
        ));
    }

    // ── EDIT ──────────────────────────────────────────────────
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    // ── UPDATE ────────────────────────────────────────────────
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Campos de texto
        $user->fill($request->only(['name','email','bio']));

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Ficheiros
        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $user->avatar = $request->file('avatar')->store('avatars','public');
        }

        if ($request->hasFile('cover')) {
            if ($user->cover) Storage::disk('public')->delete($user->cover);
            $user->cover = $request->file('cover')->store('covers','public');
        }

        // ── Privacidade ──────────────────────────────────────
        $user->visibilidade_perfil = $request->input('visibilidade_perfil', 'publico');
        $user->quem_mensagens      = $request->input('quem_mensagens', 'todos');
        $user->mostrar_bilhetes    = $request->boolean('mostrar_bilhetes');
        $user->mostrar_seguidores  = $request->boolean('mostrar_seguidores');
        $user->pesquisavel         = $request->boolean('pesquisavel');

        // ── Notificações ─────────────────────────────────────
        $user->notif_eventos    = $request->boolean('notif_eventos');
        $user->notif_bilhetes   = $request->boolean('notif_bilhetes');
        $user->notif_mensagens  = $request->boolean('notif_mensagens');
        $user->notif_seguidores = $request->boolean('notif_seguidores');

        $user->save();

        return Redirect::route('profile.edit')->with('status','profile-updated');
    }

    // ── DESTROY ───────────────────────────────────────────────
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required','current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    // ── SEGUIR / DEIXAR DE SEGUIR ─────────────────────────────
    public function toggleSeguir($id)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Não autenticado'], 401);
        }

        $alvo = User::select('id','name')->findOrFail($id);

        if ($alvo->id === auth()->id()) {
            return response()->json(['error' => 'Não podes seguir-te a ti mesmo'], 400);
        }

        $user = auth()->user();

        if ($user->estaSeguindo($alvo->id)) {
            $user->seguindo()->detach($alvo->id);
            $seguindo = false;
        } else {
            $user->seguindo()->attach($alvo->id);
            $seguindo = true;
        }

        $totalSeguidores = $alvo->seguidores()->count();

        return response()->json([
            'seguindo'         => $seguindo,
            'total_seguidores' => $totalSeguidores,
            'texto'            => $seguindo ? 'A seguir' : 'Seguir',
        ]);
    }

    // ── BLOQUEAR / DESBLOQUEAR ────────────────────────────────
    public function toggleBloquear($id)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Não autenticado'], 401);
        }

        $alvo = User::select('id','name')->findOrFail($id);

        if ($alvo->id === auth()->id()) {
            return response()->json(['error' => 'Não podes bloquear-te a ti mesmo'], 400);
        }

        $user = auth()->user();

        if ($user->estaBloqueado($alvo->id)) {
            $user->bloqueados()->detach($alvo->id);
            $bloqueado = false;
        } else {
            $user->bloqueados()->syncWithoutDetaching([$alvo->id]);
            // Remove o seguimento em ambas as direcções
            $user->seguindo()->detach($alvo->id);
            $alvo->seguindo()->detach($user->id);
            $bloqueado = true;
        }

        return response()->json([
            'bloqueado' => $bloqueado,
            'texto'     => $bloqueado ? 'Desbloquear' : 'Bloquear',
        ]);
    }

    // ── DENUNCIAR ─────────────────────────────────────────────
    public function denunciar(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Não autenticado'], 401);
        }

        $request->validate([
            'motivo' => 'required|in:spam,conteudo_inapropriado,assedio,perfil_falso,outro',
        ]);

        $alvo = User::select('id','name','avatar')->findOrFail($id);

        DB::table('denuncias_perfil')->insertOrIgnore([
            'denunciante_id' => auth()->id(),
            'denunciado_id'  => $alvo->id,
            'motivo'         => $request->motivo,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // Notifica admins
        User::where('role','admin')->each(function($admin) use ($alvo, $request) {
            $admin->notify(new \App\Notifications\PerfilDenunciadoNotification(
                $alvo, auth()->user(), $request->motivo
            ));
        });

        return response()->json(['success' => true, 'message' => 'Denúncia enviada. Vamos analisar.']);
    }

    // ── SEGUIDORES (AJAX) ─────────────────────────────────────
    public function seguidores($id)
    {
        $seguidores = User::find($id)
            ?->seguidores()
            ->select('users.id','users.name','users.avatar','users.role','users.is_verified')
            ->paginate(20);

        return response()->json($seguidores);
    }

    // ── SEGUINDO (AJAX) ───────────────────────────────────────
    public function seguindo($id)
    {
        $seguindo = User::find($id)
            ?->seguindo()
            ->select('users.id','users.name','users.avatar','users.role','users.is_verified')
            ->paginate(20);

        return response()->json($seguindo);
    }

    // ── HELPER: carregar eventos por role ─────────────────────
    private function carregarEventos(User $user): array
    {
        $select = [
            'eventos.id','eventos.user_id','eventos.categoria_id',
            'eventos.titulo','eventos.localizacao','eventos.data_evento',
            'eventos.hora_inicio','eventos.imagem_capa',
            'eventos.lotacao_maxima','eventos.status','eventos.created_at',
        ];

        $with = [
            'categoria:id,nome',
            'tiposIngresso:id,evento_id,nome,preco,quantidade_disponivel,quantidade_total',
        ];

        if ($user->role === 'admin') {
            $eventos = \App\Models\Evento::with($with)
                ->select($select)
                ->latest('eventos.created_at')
                ->paginate(6, ['*'], 'pag_eventos');

            return [
                $eventos,
                'Total Eventos', $eventos->total(),
                'Utilizadores',
                Cache::remember('total_users_count', 300, fn() => User::count()),
            ];
        }

        if ($user->role === 'creator') {
            $eventos = $user->eventos()
                ->with($with)
                ->select($select)
                ->latest('eventos.created_at')
                ->paginate(6, ['*'], 'pag_eventos');

            return [
                $eventos,
                'Eventos', $eventos->total(),
                'Seguidores', $user->seguidores_count ?? 0,
            ];
        }

        // user normal — eventos curtidos
        $eventos = $user->eventosCurtidos()
            ->with($with)
            ->select($select)
            ->latest('eventos.created_at')
            ->paginate(6, ['*'], 'pag_eventos');

        return [
            $eventos,
            'Curtidos', $eventos->total(),
            'Seguidores', $user->seguidores_count ?? 0,
        ];
    }
}