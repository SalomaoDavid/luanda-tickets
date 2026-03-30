<?php

namespace App\Http\Controllers;

use App\Models\Curtida;
use App\Models\Dislike;
use App\Models\Postagem;
use App\Models\Comentario;
use App\Notifications\EventLikedNotification;
use App\Notifications\EventCommentNotification;
use App\Notifications\FollowedUserLikedEventNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SocialController extends Controller
{
    public function toggleCurtida($id)
    {
        if (!Auth::check()) {
            if (request()->ajax()) return response()->json(['error' => 'Faz login para interagir!'], 401);
            return redirect()->back()->with('error', 'Faz login para interagir!');
        }

        // ✅ Select específico — só colunas necessárias
        $curtida = Curtida::select('id', 'user_id', 'evento_id')
            ->where('user_id', Auth::id())
            ->where('evento_id', $id)
            ->first();

        Dislike::where('user_id', Auth::id())->where('evento_id', $id)->delete();

        if ($curtida) {
            $curtida->delete();
            $curtido = false;
        } else {
            $curtida = Curtida::create([
                'user_id'   => Auth::id(),
                'evento_id' => $id,
            ]);

            // ✅ Select específico no evento e user — só colunas necessárias para notificação
            $evento = $curtida->evento()->select('id', 'user_id', 'titulo')->first();

            if ($evento->user_id !== Auth::id()) {
                $evento->user->notify(new EventLikedNotification($curtida));
            }

            // ✅ Select específico nos seguidores — só id
            $curtida->load(['user:id', 'user.seguidores:id']);
            foreach ($curtida->user->seguidores as $seguidor) {
                if ($seguidor->id !== $evento->user_id) {
                    $seguidor->notify(new FollowedUserLikedEventNotification($curtida, $curtida->user));
                }
            }

            $curtido = true;
        }

        // ✅ Count direto sem carregar modelo
        $total = Curtida::where('evento_id', $id)->count();

        if (request()->ajax()) {
            return response()->json(['curtido' => $curtido, 'total' => $total]);
        }

        return redirect()->back();
    }

    public function toggleDislike($id)
    {
        if (!Auth::check()) {
            if (request()->ajax()) return response()->json(['error' => 'Faz login para interagir!'], 401);
            return redirect()->back()->with('error', 'Faz login para interagir!');
        }

        Curtida::where('user_id', Auth::id())->where('evento_id', $id)->delete();

        // ✅ Select específico
        $dislike = Dislike::select('id', 'user_id', 'evento_id')
            ->where('user_id', Auth::id())
            ->where('evento_id', $id)
            ->first();

        if ($dislike) {
            $dislike->delete();
            $dislikado = false;
        } else {
            Dislike::create([
                'user_id'   => Auth::id(),
                'evento_id' => $id,
            ]);
            $dislikado = true;
        }

        if (request()->ajax()) {
            return response()->json(['dislikado' => $dislikado]);
        }

        return redirect()->back();
    }

    public function publicar(Request $request)
    {
        $request->validate([
            'conteudo' => 'required|string|max:5000',
        ]);

        Postagem::create([
            'user_id'  => auth()->id(),
            'conteudo' => $request->conteudo,
        ]);

        return redirect()->back()->with('success', 'Post publicado com sucesso!');
    }

    public function eliminarPost($id)
    {
        // ✅ Select específico — só colunas para verificar permissão
        $post = Postagem::select('id', 'user_id')->findOrFail($id);

        if ($post->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Não tens permissão para eliminar este post.');
        }

        $post->delete();

        return redirect()->back()->with('success', 'Post eliminado com sucesso!');
    }

    public function comentar(Request $request, $eventoId)
    {
        $request->validate(['corpo' => 'required|string|max:500']);

        $comentario = Comentario::create([
            'evento_id' => $eventoId,
            'user_id'   => auth()->id(),
            'parent_id' => $request->parent_id ?? null,
            'corpo'     => $request->corpo,
        ]);

        // ✅ Select específico no evento — só o necessário para notificação
        $evento = $comentario->evento()->select('id', 'user_id')->first();

        if ($evento->user_id !== auth()->id()) {
            // ✅ notify em vez de notifyNow — vai para a queue em vez de bloquear o request
            $evento->user->notify(new EventCommentNotification($comentario));
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'comentario_id' => $comentario->id]);
        }

        return back();
    }

    public function toggleLikeComentario($id)
    {
        // ✅ Select específico — só colunas necessárias para o toggle
        $comentario = Comentario::select('id', 'user_id', 'evento_id')->findOrFail($id);
        $comentario->likes()->toggle(auth()->id());

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    public function eliminarComentario($id)
    {
        // ✅ Select específico — só colunas para verificar permissão
        $comentario = Comentario::select('id', 'user_id')->findOrFail($id);

        if ($comentario->user_id === auth()->id()) {
            $comentario->delete();
        }

        return back();
    }
}