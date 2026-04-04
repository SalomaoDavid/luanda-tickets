<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Models\Bilhete;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function show($id): View
    {
        // ✅ Adicionado 'cover' ao select — estava em falta
        $user = User::select('id','name','email','avatar','cover','bio','role','is_verified','last_seen','created_at')
            ->findOrFail($id);

        $isOwner = auth()->id() === $user->id;

        $postagens = $user->postagens()
            ->select('id','user_id','conteudo','imagem','created_at')
            ->latest()
            ->get();

        $bilhetes = Bilhete::whereHas('pedido', function ($q) use ($id) {
                $q->where('user_id', $id)->where('status', 'pago');
            })
            ->with([
                'evento:id,titulo,localizacao,data_evento',
                'tipoIngresso:id,nome,preco',
            ])
            ->select('id','pedido_id','evento_id','tipo_ingressos_id','codigo_unico','validado_em','created_at')
            ->latest()
            ->get();

        if ($user->role === 'admin') {
            $eventos = \App\Models\Evento::with([
                    'categoria:id,nome',
                    'tiposIngresso:id,evento_id,nome,preco,quantidade_disponivel',
                    'curtidas:id,evento_id,user_id',
                    'usuariosQueCurtiram:id,name,avatar',
                ])
                ->select('id','user_id','categoria_id','titulo','descricao','localizacao','data_evento','imagem_capa','lotacao_maxima','status','created_at')
                ->latest()
                ->get();

            $statsLabel  = 'Total Eventos';
            $statsCount  = $eventos->count();
            $statsLabel2 = 'Utilizadores';
            $statsCount2 = \Illuminate\Support\Facades\Cache::remember('total_users_count', 300, fn() => User::count());

        } elseif ($user->role === 'creator') {
            $eventos = $user->eventos()
                ->with([
                    'categoria:id,nome',
                    'tiposIngresso:id,evento_id,nome,preco,quantidade_disponivel',
                    'curtidas:id,evento_id,user_id',
                    'usuariosQueCurtiram:id,name,avatar',
                ])
                ->select('id','user_id','categoria_id','titulo','descricao','localizacao','data_evento','imagem_capa','lotacao_maxima','status','created_at')
                ->latest()
                ->get();

            $statsLabel  = 'Eventos';
            $statsCount  = $eventos->count();
            $statsLabel2 = 'Seguidores';
            $statsCount2 = 0;

        } else {
            $eventos = $user->eventosCurtidos()
                ->with([
                    'categoria:id,nome',
                    'tiposIngresso:id,evento_id,nome,preco,quantidade_disponivel',
                    'curtidas:id,evento_id,user_id',
                    'usuariosQueCurtiram:id,name,avatar',
                ])
                ->select('eventos.id','eventos.user_id','eventos.categoria_id','eventos.titulo','eventos.descricao','eventos.localizacao','eventos.data_evento','eventos.imagem_capa','eventos.lotacao_maxima','eventos.status','eventos.created_at')
                ->latest('eventos.created_at')
                ->get();

            $statsLabel  = 'Curtidos';
            $statsCount  = $eventos->count();
            $statsLabel2 = 'Seguidores';
            $statsCount2 = 0;
        }

        return view('profile.show', compact(
            'user', 'isOwner', 'postagens', 'eventos', 'bilhetes',
            'statsLabel', 'statsCount', 'statsLabel2', 'statsCount2'
        ));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // ✅ Avatar
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        // ✅ Cover — estava completamente em falta no controller
        if ($request->hasFile('cover')) {
            if ($user->cover) {
                Storage::disk('public')->delete($user->cover);
            }
            $user->cover = $request->file('cover')->store('covers', 'public');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}