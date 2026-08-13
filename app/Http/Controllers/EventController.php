<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Noticia;
use App\Models\ContaBancaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EventController extends Controller
{
    public function index()
    {
        $eventos = Evento::with([
                'categoria:id,nome',
                'fotos:id,evento_id,caminho',
                'curtidas:id,evento_id,user_id',
                'usuariosQueCurtiram:id,name,avatar',
                'tiposIngresso:id,evento_id,nome,preco,quantidade_disponivel',
            ])
            ->where('status', 'publicado')
            ->select('id','user_id','categoria_id','titulo','descricao','localizacao',
                     'data_evento','hora_inicio','imagem_capa','video_preview',
                     'status','created_at','provincia','lotacao_maxima','online')
            ->latest()
            ->paginate(10, ['*'], 'pag_eventos');

        $ultimasNoticias = Cache::remember('ultimas_noticias', 300, function () {
            return Noticia::select('id','titulo','slug','created_at')
                ->latest()->take(3)->get();
        });

        $postagens = \App\Models\Postagem::with([
                'user:id,name,avatar',
                'reacoes:id,postagem_id,user_id,tipo',
                'comentarios:id,postagem_id,user_id,corpo,created_at',
                'comentarios.user:id,name,avatar',
            ])
            ->select('id','user_id','conteudo','created_at')
            ->latest()
            ->paginate(10, ['*'], 'pag_posts');

        $feed = collect();
        foreach ($eventos as $evento) {
            $feed->push(['tipo' => 'evento', 'data' => $evento->created_at, 'item' => $evento]);
        }
        foreach ($postagens as $post) {
            $feed->push(['tipo' => 'post', 'data' => $post->created_at, 'item' => $post]);
        }
        $feed = $feed->sortByDesc('data');

        return view('welcome', compact('feed', 'eventos', 'ultimasNoticias'));
    }

    public function show($id)
    {
        $evento = Evento::with([
                'categoria:id,nome',
                'subcategoria:id,nome',
                'tiposIngresso:id,evento_id,nome,preco,quantidade_disponivel,quantidade_total',
                'fotos:id,evento_id,caminho',
                'user:id,name,avatar,role,created_at',
            ])
            ->select('id','user_id','categoria_id','subcategoria_id','titulo','descricao',
                     'localizacao','municipio','provincia','data_evento','hora_inicio',
                     'hora_fim','online','link_externo','imagem_capa','video_preview',
                     'lotacao_maxima','status','meta','created_at')
            ->findOrFail($id);

        $meta     = $evento->meta ?? [];
        $temMeta  = !empty($meta);
        $catNome  = isset($evento->categoria) ? strtolower($evento->categoria->nome) : '';
        $tParagens = isset($meta['paragens'])
            ? (is_array($meta['paragens']) ? $meta['paragens'] : json_decode($meta['paragens'], true))
            : [];

        $contasBancarias = Cache::remember('contas_bancarias_activas', 600, function () {
            return ContaBancaria::activas()->get();
        });

        $preco    = $evento->tiposIngresso->min('preco') ?? 0;
        $totalDisp = $evento->tiposIngresso->sum('quantidade_disponivel');

        return view('evento-detalhes', compact(
            'evento', 'meta', 'temMeta', 'catNome', 'tParagens',
            'contasBancarias', 'preco', 'totalDisp'
        ));
    }

    public function todosEventos(Request $request)
    {
        $query = Evento::with([
                'categoria:id,nome',
                'subcategoria:id,nome',
                'tiposIngresso:id,evento_id,nome,preco,quantidade_disponivel,quantidade_total',
                'curtidas:id,evento_id,user_id',
                'usuariosQueCurtiram:id,name,avatar',
                'usuariosQueComentaram:id,name,avatar',
                'comentarios' => fn($q) => $q->with([
                    'user:id,name,avatar',
                    'respostas.user:id,name,avatar',
                ])->latest(),
            ])
            ->select('id','user_id','categoria_id','subcategoria_id','titulo',
                     'localizacao','data_evento','hora_inicio','imagem_capa',
                     'video_preview','status','created_at','online','lotacao_maxima')
            ->where('status', 'publicado');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('localizacao', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->filled('subcategoria')) {
            $query->where('subcategoria_id', $request->subcategoria);
        }

        $filter = $request->filter;
        $query = match ($filter) {
            'hoje'      => $query->whereDate('data_evento', today()),
            'amanha'    => $query->whereDate('data_evento', today()->addDay()),
            'fds'       => $query->whereBetween('data_evento', [now()->startOfWeek()->addDays(4), now()->startOfWeek()->addDays(6)]),
            'semana'    => $query->whereBetween('data_evento', [now()->startOfWeek(), now()->endOfWeek()]),
            'populares' => $query->withCount('curtidas')->orderBy('curtidas_count', 'desc'),
            'novos'     => $query->orderBy('created_at', 'desc'),
            default     => $query->orderBy('data_evento', 'asc'),
        };

        $eventos = $query->paginate(12);

        $categorias = Cache::remember('categorias_com_subcategorias', 600, function () {
            return \App\Models\Categoria::with('subcategorias')->orderBy('nome')->get();
        });

        return view('todos-eventos', compact('eventos', 'categorias'));
    }
}