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

        $eventos = Evento::with(['categoria', 'fotos', 'curtidas'])

            ->where('status', 'publicado')

            ->get();



        // Mantendo a lógica de carregar as notícias para a welcome

        $ultimasNoticias = Noticia::latest()->take(3)->get();



        return view('welcome', compact('eventos', 'ultimasNoticias'));

    }



    // Detalhes do Evento: Exibe informações completas, ingressos e galeria

    public function show($id)

    {

        $evento = Evento::with(['categoria', 'tiposIngresso', 'fotos'])->findOrFail($id);

        return view('evento-detalhes', compact('evento'));

    }
    // Listagem Completa: Filtros de pesquisa e ordenação (Lógica original preservada)

    public function todosEventos(Request $request)

    {

        $query = Evento::with(['categoria', 'fotos', 'usuariosQueCurtiram'])

            ->where('status', 'publicado');

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function($q) use ($search) {

                $q->where('titulo', 'like', "%{$search}%")

                  ->orWhere('localizacao', 'like', "%{$search}%")

                  ->orWhere('descricao', 'like', "%{$search}%");

            });
        }

        $eventos = $query->orderBy('data_evento', 'asc')->get();
        return view('todos-eventos', compact('eventos'));
    }

}
