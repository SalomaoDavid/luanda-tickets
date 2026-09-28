<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Subcategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoriaController extends Controller
{
    /**
     * ✅ Única fonte de verdade do cache de categorias, para nunca
     * ficar dessincronizado do que o evento-form/AdminEventoController leem.
     */
    private function limparCache(): void
    {
        Cache::forget('categorias_com_subcategorias'); // AdminEventoController::create()
        Cache::forget('categorias_lista_lw');           // EventoForm.php (Livewire)
    }

    public function index(Request $request)
    {
        $tipo = $request->get('tipo', 'evento');

        // ✅ Só as colunas necessárias + contagem de eventos (sem N+1)
        $categorias = Categoria::query()
            ->select('id', 'nome', 'slug', 'tipo', 'ativo')
            ->where('tipo', $tipo)
            ->withCount('eventos')
            ->with(['subcategorias' => function ($q) {
                $q->select('id', 'categoria_id', 'nome', 'slug', 'ativo')
                    ->withCount('eventos')
                    ->orderBy('nome');
            }])
            ->orderBy('nome')
            ->get();

        return view('admin-categorias', compact('categorias', 'tipo'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:100|unique:categorias,nome',
            'tipo' => ['required', Rule::in(['evento', 'noticia'])],
        ]);

        Categoria::create([
            'nome'  => $dados['nome'],
            'slug'  => Str::slug($dados['nome']),
            'tipo'  => $dados['tipo'],
            'ativo' => true,
        ]);

        $this->limparCache();

        return back()->with('sucesso', 'Categoria criada com sucesso.');
    }

    public function update(Request $request, Categoria $categoria)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:100', Rule::unique('categorias', 'nome')->ignore($categoria->id)],
            'tipo' => ['required', Rule::in(['evento', 'noticia'])],
        ]);

        $categoria->update([
            'nome' => $dados['nome'],
            'slug' => Str::slug($dados['nome']),
            'tipo' => $dados['tipo'],
        ]);

        $this->limparCache();

        return back()->with('sucesso', 'Categoria atualizada com sucesso.');
    }

    public function toggleAtivo(Categoria $categoria)
    {
        $categoria->update(['ativo' => !$categoria->ativo]);
        $this->limparCache();

        return back()->with(
            'sucesso',
            $categoria->ativo
                ? 'Categoria reativada — volta a aparecer na criação de novos eventos.'
                : 'Categoria desativada — deixa de aparecer para novos eventos, mas os eventos existentes continuam normais.'
        );
    }

    public function destroy(Categoria $categoria)
    {
        // ✅ Regra combinada: só apaga de vez quando já não houver
        // NENHUM evento ligado a esta categoria (mesmo antigos/expirados) —
        // evita qualquer risco de partir a foreign key de eventos existentes.
        // Até lá, o admin usa "desativar" para tirá-la da lista de criação.
        if ($categoria->eventos()->exists()) {
            return back()->with(
                'erro',
                'Não é possível eliminar: ainda existem eventos ligados a esta categoria. Desative-a por agora — a eliminação definitiva fica disponível quando esses eventos deixarem de existir.'
            );
        }

        $categoria->delete();
        $this->limparCache();

        return back()->with('sucesso', 'Categoria eliminada definitivamente.');
    }

    // ─────────────────────────────────────────────
    // Subcategorias
    // ─────────────────────────────────────────────

    public function storeSubcategoria(Request $request, Categoria $categoria)
    {
        $dados = $request->validate([
            'nome' => [
                'required', 'string', 'max:100',
                Rule::unique('subcategorias', 'nome')->where('categoria_id', $categoria->id),
            ],
        ]);

        Subcategoria::create([
            'categoria_id' => $categoria->id,
            'nome'         => $dados['nome'],
            'slug'         => Str::slug($dados['nome']),
            'ativo'        => true,
        ]);

        $this->limparCache();

        return back()->with('sucesso', 'Subcategoria criada com sucesso.');
    }

    public function updateSubcategoria(Request $request, Subcategoria $subcategoria)
    {
        $dados = $request->validate([
            'nome' => [
                'required', 'string', 'max:100',
                Rule::unique('subcategorias', 'nome')
                    ->where('categoria_id', $subcategoria->categoria_id)
                    ->ignore($subcategoria->id),
            ],
        ]);

        $subcategoria->update([
            'nome' => $dados['nome'],
            'slug' => Str::slug($dados['nome']),
        ]);

        $this->limparCache();

        return back()->with('sucesso', 'Subcategoria atualizada com sucesso.');
    }

    public function toggleAtivoSubcategoria(Subcategoria $subcategoria)
    {
        $subcategoria->update(['ativo' => !$subcategoria->ativo]);
        $this->limparCache();

        return back()->with(
            'sucesso',
            $subcategoria->ativo ? 'Subcategoria reativada.' : 'Subcategoria desativada.'
        );
    }

    public function destroySubcategoria(Subcategoria $subcategoria)
    {
        if ($subcategoria->eventos()->exists()) {
            return back()->with(
                'erro',
                'Não é possível eliminar: ainda existem eventos ligados a esta subcategoria. Desative-a por agora.'
            );
        }

        $subcategoria->delete();
        $this->limparCache();

        return back()->with('sucesso', 'Subcategoria eliminada definitivamente.');
    }
}