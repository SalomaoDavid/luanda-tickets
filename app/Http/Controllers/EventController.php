<?php

namespace App\Http\Controllers;



use App\Models\Evento;
use App\Models\Noticia; // Necessário para a index carregar notícias
use Illuminate\Http\Request;



class EventController extends Controller

{

    // Página Inicial: Carrega destaques e notícias recentes (Exatamente como no SiteController)

    public function index()
    {
        $eventos = Evento::with([
            'categoria', 
            'fotos', 
            'curtidas', 
            'usuariosQueCurtiram',
            'comentarios.user',
            'comentarios.likes',
            'comentarios.respostas.user',
            'comentarios.respostas.likes'
            ])
            ->where('status', 'publicado')
            ->latest()
            ->get();

        $ultimasNoticias = Noticia::latest()->take(3)->get();

        $postagens = \App\Models\Postagem::with('user')->latest()->get();

    // Mistura eventos e postagens numa só lista ordenada por data
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



        // Detalhes do Evento: Exibe informações completas, ingressos e galeria

        public function show($id)

        {

            $evento = Evento::with(['categoria', 'tiposIngresso', 'fotos'])->findOrFail($id);

            return view('evento-detalhes', compact('evento'));

        }

        public function todosEventos(Request $request)
        {
            $query = Evento::with(['categoria', 'fotos', 'curtidas', 'tiposIngresso', 'usuariosQueCurtiram', 'usuariosQueComentaram'])
                ->where('status', 'publicado');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('titulo', 'like', "%{$search}%")
                    ->orWhere('localizacao', 'like', "%{$search}%")
                    ->orWhere('descricao', 'like', "%{$search}%");
                });
            }

            if ($request->filled('categoria')) {
                $query->whereHas('categoria', fn($q) => $q->where('nome', 'like', '%'.$request->categoria.'%'));
            }

            $filter = $request->filter;
            $query = match($filter) {
                'hoje'      => $query->whereDate('data_evento', today()),
                'amanha'    => $query->whereDate('data_evento', today()->addDay()),
                'fds'       => $query->whereBetween('data_evento', [now()->startOfWeek()->addDays(4), now()->startOfWeek()->addDays(6)]),
                'semana'    => $query->whereBetween('data_evento', [now()->startOfWeek(), now()->endOfWeek()]),
                'populares' => $query->withCount('curtidas')->orderBy('curtidas_count', 'desc'),
                'novos'     => $query->orderBy('created_at', 'desc'),
                default     => $query->orderBy('data_evento', 'asc')
            };

            $eventos = $query->get();
            return view('todos-eventos', compact('eventos'));
        }

}
