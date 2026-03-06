<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\EventoFoto;
use App\Models\TipoIngresso;
use App\Models\Categoria;
use Illuminate\Http\Request;

class AdminEventoController extends Controller
{
    public function index() {
        $user = auth()->user();
        
        // Admin vê tudo, Criador vê apenas os seus eventos
        $eventos = Evento::with('tiposIngresso')
            ->when($user->role !== 'admin', function($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin-eventos', compact('eventos'));
    }

    public function create() {
        $categorias = Categoria::all();
        return view('admin-eventos-criar', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|max:255',
            'categoria_id' => 'required',
            'localizacao' => 'required',
            'data_evento' => 'required',
            'lotacao_maxima' => 'required|integer',
            'descricao' => 'required', 
            'imagem_capa' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $caminhoImagem = $request->hasFile('imagem_capa') ? $request->file('imagem_capa')->store('capas_eventos', 'public') : null;

        // LINHA ADICIONADA: 'user_id' => auth()->id()
        $evento = Evento::create([
            'titulo' => $request->titulo,
            'user_id' => auth()->id(), 
            'categoria_id' => $request->categoria_id,
            'descricao' => $request->descricao,
            'localizacao' => $request->localizacao,
            'data_evento' => $request->data_evento,
            'lotacao_maxima' => $request->lotacao_maxima,
            'status' => $request->status,
            'imagem_capa' => $caminhoImagem,
        ]);

        // Galeria de fotos
        if ($request->hasFile('galeria')) {
            foreach ($request->file('galeria') as $foto) {
                EventoFoto::create([
                    'evento_id' => $evento->id,
                    'caminho' => $foto->store('galeria_eventos', 'public')
                ]);
            }
        }

        // Ingressos Automáticos
        if ($request->filled('preco_normal')) {
            TipoIngresso::create([
                'evento_id' => $evento->id,
                'nome' => 'Normal',
                'preco' => $request->preco_normal + 750,
                'quantidade_disponivel' => $request->qtd_normal ?? $request->lotacao_maxima,
                'quantidade_total' => $request->qtd_normal ?? $request->lotacao_maxima,
            ]);
        }

        if ($request->filled('preco_vip')) {
            TipoIngresso::create([
                'evento_id' => $evento->id,
                'nome' => 'VIP',
                'preco' => $request->preco_vip + 1000,
                'quantidade_disponivel' => $request->qtd_vip ?? 0,
                'quantidade_total' => $request->qtd_vip ?? 0,
            ]);
        }

        return redirect()->route('admin.eventos')->with('success', 'Evento criado com sucesso!');
    }

    public function edit($id) {
        $evento = Evento::with('tiposIngresso')->findOrFail($id);
        
        // Segurança: Impede que um criador edite evento alheio pela URL
        if (auth()->user()->role !== 'admin' && $evento->user_id !== auth()->id()) {
            abort(403, 'Acesso negado!');
        }

        $categorias = Categoria::all();
        return view('admin-eventos-editar', compact('evento', 'categorias'));
    }

    public function update(Request $request, $id) {
        $evento = Evento::findOrFail($id);
        
        if (auth()->user()->role !== 'admin' && $evento->user_id !== auth()->id()) {
            abort(403);
        }

        $evento->update($request->only(['titulo', 'descricao', 'localizacao', 'data_evento', 'status', 'lotacao_maxima']));
        return redirect()->route('admin.eventos')->with('success', 'Evento atualizado!');
    }

    public function destroy($id) {
        $evento = Evento::findOrFail($id);
        
        if (auth()->user()->role !== 'admin' && $evento->user_id !== auth()->id()) {
            abort(403);
        }

        $evento->tiposIngresso()->delete();
        $evento->delete();
        return redirect()->back()->with('success', 'Evento removido!');
    }
}