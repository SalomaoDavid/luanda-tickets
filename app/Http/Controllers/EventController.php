<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Noticia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EventController extends Controller
{
    public function index()
    {
        $eventos = Evento::with([
            'categoria:id,nome', // ✅ removido emoji (é accessor, não coluna)
            'fotos:id,evento_id,caminho',
            'curtidas',
            'usuariosQueCurtiram:id,name,avatar',
            'comentarios.user:id,name,avatar',
            'comentarios.likes',
            'comentarios.respostas.user:id,name,avatar',
            'comentarios.respostas.likes'
        ])
            ->where('status', 'publicado')
            ->latest()
            ->get();

        $ultimasNoticias = Cache::remember('ultimas_noticias', 300, function () {
            return Noticia::latest()->take(3)->get();
        });

        $postagens = \App\Models\Postagem::with([
            'user:id,name,avatar',
            'reacoes.user:id,name,avatar',
            'comentarios.user:id,name,avatar',
            'comentarios.respostas.user:id,name,avatar',
        ])->latest()->get();

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
            'categoria:id,nome', // ✅ removido emoji
            'subcategoria:id,nome',
            'tiposIngresso',
            'fotos:id,evento_id,caminho'
        ])->findOrFail($id);

        return view('evento-detalhes', compact('evento'));
    }

    public function todosEventos(Request $request)
    {
        $query = Evento::with([
            'categoria:id,nome', // ✅ removido emoji
            'subcategoria:id,nome',
            'fotos:id,evento_id,caminho',
            'curtidas',
            'tiposIngresso',
            'usuariosQueCurtiram:id,name,avatar',
            'usuariosQueComentaram:id,name,avatar'
        ])->where('status', 'publicado');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                    ->orWhere('localizacao', 'like', "%{$search}%")
                    ->orWhere('descricao', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->whereHas('categoria', fn($q) => $q->where('id', $request->categoria));
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
            default     => $query->orderBy('data_evento', 'asc')
        };

        $eventos = $query->get();

        $categorias = Cache::remember('categorias_com_subcategorias', 600, function () {
            return \App\Models\Categoria::with('subcategorias')->orderBy('nome')->get();
        });

        return view('todos-eventos', compact('eventos', 'categorias'));
    }
}