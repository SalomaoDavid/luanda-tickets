<?php

namespace App\Http\Controllers;

use App\Models\Sugestao;
use Illuminate\Http\Request;

class SugestaoController extends Controller
{
    /**
     * O usuário envia uma sugestão a partir da página de Definições.
     */
    public function store(Request $request)
    {
        $request->validate([
            'mensagem' => 'required|string|min:5|max:2000',
        ], [
            'mensagem.required' => 'Escreve a tua sugestão antes de enviar.',
            'mensagem.min'      => 'A tua mensagem é muito curta — dá mais um pouco de detalhe.',
        ]);

        Sugestao::create([
            'user_id'  => auth()->id(),
            'mensagem' => strip_tags($request->mensagem),
        ]);

        return redirect()->back()->with('success', 'A tua sugestão foi enviada. Obrigado por ajudares a melhorar a plataforma!');
    }

    /**
     * Lista de sugestões para o admin, com filtro por estado.
     */
    public function index(Request $request)
    {
        $filtro = $request->query('estado', 'todos');

        $sugestoes = Sugestao::with('user:id,name,avatar')
            ->when($filtro !== 'todos', fn ($q) => $q->where('estado', $filtro))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $totalNovas = Sugestao::where('estado', 'novo')->count();

        return view('admin-sugestoes', compact('sugestoes', 'filtro', 'totalNovas'));
    }

    /**
     * Marca uma sugestão como lida — via AJAX, sem recarregar a página
     * (mesmo padrão já usado no dropdown de status dos eventos).
     */
    public function marcarLida(Request $request, $id)
    {
        $sugestao = Sugestao::findOrFail($id);
        $sugestao->estado = 'lido';
        $sugestao->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Sugestão marcada como lida.',
            ]);
        }

        return redirect()->back()->with('success', 'Sugestão marcada como lida.');
    }
}