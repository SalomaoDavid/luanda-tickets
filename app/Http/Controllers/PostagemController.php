<?php

namespace App\Http\Controllers;

use App\Models\Postagem;
use App\Models\PostagemReacao;
use App\Models\PostagemComentario;
use App\Notifications\PostagemLikedNotification;
use App\Notifications\PostagemComentarioNotification;
use Illuminate\Http\Request;

class PostagemController extends Controller
{
    public function toggleReacao($id, $tipo)
    {
        if (!auth()->check()) {
            if (request()->ajax()) return response()->json(['error' => 'Faz login para interagir!'], 401);
            return redirect()->back()->with('error', 'Faz login para interagir!');
        }

        if (!in_array($tipo, ['curtida', 'adoro'])) {
            return response()->json(['error' => 'Tipo inválido'], 400);
        }

        $postagem = Postagem::select('id','user_id','conteudo')->findOrFail($id);

        $reacao = PostagemReacao::select('id','tipo','user_id','postagem_id')
            ->where('user_id', auth()->id())
            ->where('postagem_id', $id)
            ->first();

        if ($reacao) {
            if ($reacao->tipo === $tipo) {
                $reacao->delete();
                $ativo     = false;
                $tipoAtivo = null;
            } else {
                $reacao->updateQuietly(['tipo' => $tipo]);
                $ativo     = true;
                $tipoAtivo = $tipo;

                // Notifica o dono da postagem ao trocar reação
                if ($postagem->user_id !== auth()->id()) {
                    $reacao->load('user:id,name,avatar');
                    $postagem->user->notify(new PostagemLikedNotification($reacao, $postagem));
                }
            }
        } else {
            $novaReacao = PostagemReacao::create([
                'user_id'     => auth()->id(),
                'postagem_id' => $id,
                'tipo'        => $tipo,
            ]);
            $ativo     = true;
            $tipoAtivo = $tipo;

            // ── Notifica o dono da postagem ──
            if ($postagem->user_id !== auth()->id()) {
                $novaReacao->load('user:id,name,avatar');
                $postagem->load('user:id,name');
                $postagem->user->notify(new PostagemLikedNotification($novaReacao, $postagem));
            }
        }

        $totalCurtidas = PostagemReacao::where('postagem_id', $id)->where('tipo','curtida')->count();
        $totalAdoros   = PostagemReacao::where('postagem_id', $id)->where('tipo','adoro')->count();

        return response()->json([
            'ativo'         => $ativo,
            'tipo'          => $tipoAtivo,
            'totalCurtidas' => $totalCurtidas,
            'totalAdoros'   => $totalAdoros,
        ]);
    }

    public function comentar(Request $request, $id)
    {
        $request->validate(['corpo' => 'required|string|max:500']);

        $postagem = Postagem::select('id','user_id','conteudo')->findOrFail($id);

        $comentario = PostagemComentario::create([
            'user_id'     => auth()->id(),
            'postagem_id' => $id,
            'parent_id'   => $request->parent_id ?? null,
            'corpo'       => $request->corpo,
        ]);

        // ── Notifica o dono da postagem ──
        if ($postagem->user_id !== auth()->id()) {
            $comentario->load('user:id,name,avatar');
            $postagem->load('user:id,name');
            $postagem->user->notify(new PostagemComentarioNotification($comentario, $postagem));
        }

        if (request()->ajax()) {
            return response()->json(['success' => true, 'comentario_id' => $comentario->id]);
        }

        return back();
    }

    public function eliminarComentario($id)
    {
        $comentario = PostagemComentario::select('id','user_id')->findOrFail($id);

        if ($comentario->user_id !== auth()->id()) {
            return response()->json(['error' => 'Sem permissão'], 403);
        }

        $comentario->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back();
    }
}