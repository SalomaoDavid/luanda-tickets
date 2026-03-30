<?php

namespace App\Http\Controllers;

use App\Models\Postagem;
use App\Models\PostagemReacao;
use App\Models\PostagemComentario;
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

        // ✅ Select específico — só colunas necessárias
        $reacao = PostagemReacao::select('id', 'tipo', 'user_id', 'postagem_id')
            ->where('user_id', auth()->id())
            ->where('postagem_id', $id)
            ->first();

        if ($reacao) {
            if ($reacao->tipo === $tipo) {
                // Mesmo tipo — remove (toggle off)
                $reacao->delete();
                $ativo     = false;
                $tipoAtivo = null;
            } else {
                // Tipo diferente — troca a reação usando updateQuietly
                $reacao->updateQuietly(['tipo' => $tipo]); // ✅ sem disparar eventos
                $ativo     = true;
                $tipoAtivo = $tipo;
            }
        } else {
            // Sem reação — cria nova
            PostagemReacao::create([
                'user_id'     => auth()->id(),
                'postagem_id' => $id,
                'tipo'        => $tipo,
            ]);
            $ativo     = true;
            $tipoAtivo = $tipo;
        }

        // ✅ Busca contagens direto na BD em vez de carregar o modelo completo
        $totalCurtidas = PostagemReacao::where('postagem_id', $id)->where('tipo', 'curtida')->count();
        $totalAdoros   = PostagemReacao::where('postagem_id', $id)->where('tipo', 'adoro')->count();

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

        $comentario = PostagemComentario::create([
            'user_id'     => auth()->id(),
            'postagem_id' => $id,
            'parent_id'   => $request->parent_id ?? null,
            'corpo'       => $request->corpo,
        ]);

        if (request()->ajax()) {
            return response()->json(['success' => true, 'comentario_id' => $comentario->id]);
        }

        return back();
    }

    public function eliminarComentario($id)
    {
        // ✅ Select específico — só colunas necessárias para verificar permissão
        $comentario = PostagemComentario::select('id', 'user_id')->findOrFail($id);

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