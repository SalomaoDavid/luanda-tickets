<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\EventoFoto;
use App\Models\TipoIngresso;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AdminEventoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // ✅ Select específico — só colunas necessárias para a listagem
        $eventos = Evento::with([
                'tiposIngresso:id,evento_id,nome,preco,quantidade_disponivel,quantidade_total',
            ])
            ->select('id','user_id','titulo','status','data_evento','localizacao','imagem_capa','created_at')
            ->when($user->role !== 'admin', fn($q) => $q->where('user_id', $user->id))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin-eventos', compact('eventos'));
    }

    public function create()
    {
        // ✅ Cache para categorias — mudam raramente
        $categorias = Cache::remember('categorias_com_subcategorias', 600, function () {
            return Categoria::with('subcategorias')->orderBy('nome')->get();
        });

        return view('admin-eventos-criar', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'          => 'required|max:255',
            'descricao'       => 'required',
            'categoria_id'    => 'required|exists:categorias,id',
            'subcategoria_id' => 'nullable|exists:subcategorias,id',
            'localizacao'     => 'required',
            'municipio'       => 'nullable|string|max:100',
            'provincia'       => 'nullable|string|max:100',
            'data_evento'     => 'required|date',
            'data_fim'        => 'nullable|date|after:data_evento',
            'hora_inicio'     => 'required',
            'hora_fim'        => 'nullable',
            'multiplos_dias'  => 'nullable|boolean',
            'online'          => 'nullable|boolean',
            'link_externo'    => 'nullable|url',
            'lotacao_maxima'  => 'required|integer|min:1',
            'imagem_capa'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'galeria'         => 'nullable|array',
            'galeria.*'       => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'ingressos'                => 'nullable|array',
            'ingressos.*.nome'         => 'required_with:ingressos|string|max:100',
            'ingressos.*.preco'        => 'required_with:ingressos|numeric|min:0',
            'ingressos.*.quantidade'   => 'required_with:ingressos|integer|min:1',
            'ingressos_por_pessoa'     => 'nullable|integer|min:1|max:10',
            'lista_espera'             => 'nullable|boolean',
            'privado'                  => 'nullable|boolean',
            'aprovacao_manual'         => 'nullable|boolean',
            'permitir_comentarios'     => 'nullable|boolean',
            'participantes_publicos'   => 'nullable|boolean',
            'notif_nova_inscricao'     => 'nullable|boolean',
            'notif_lembrete_24h'       => 'nullable|boolean',
            'notif_resumo_semanal'     => 'nullable|boolean',
            'status'                   => 'required|in:rascunho,publicado,encerrado',
            'termos'                   => 'accepted',
        ], [
            'titulo.required'         => 'O nome do evento é obrigatório.',
            'descricao.required'      => 'A descrição é obrigatória.',
            'categoria_id.required'   => 'Seleciona uma categoria.',
            'localizacao.required'    => 'O local do evento é obrigatório.',
            'data_evento.required'    => 'A data de início é obrigatória.',
            'hora_inicio.required'    => 'A hora de início é obrigatória.',
            'lotacao_maxima.required' => 'A lotação máxima é obrigatória.',
            'imagem_capa.max'         => 'A imagem não pode ter mais de 5 MB.',
            'termos.accepted'         => 'Tens de aceitar os termos de publicação.',
        ]);

        // Upload da imagem de capa — lógica intacta
        $caminhoImagem = null;
        if ($request->hasFile('imagem_capa')) {
            $caminhoImagem = $request->file('imagem_capa')->store('capas_eventos', 'public');
        }

        // Criar o evento — lógica intacta
        $evento = Evento::create([
            'user_id'                => auth()->id(),
            'titulo'                 => $request->titulo,
            'descricao'              => $request->descricao,
            'categoria_id'           => $request->categoria_id,
            'subcategoria_id'        => $request->subcategoria_id ?? null,
            'localizacao'            => $request->localizacao,
            'municipio'              => $request->municipio,
            'provincia'              => $request->provincia ?? 'Luanda',
            'data_evento'            => $request->data_evento,
            'data_fim'               => $request->data_fim ?? null,
            'hora_inicio'            => $request->hora_inicio,
            'hora_fim'               => $request->hora_fim ?? null,
            'multiplos_dias'         => $request->boolean('multiplos_dias'),
            'online'                 => $request->boolean('online'),
            'link_externo'           => $request->link_externo ?? null,
            'lotacao_maxima'         => $request->lotacao_maxima,
            'ingressos_por_pessoa'   => $request->ingressos_por_pessoa ?? 1,
            'lista_espera'           => $request->boolean('lista_espera'),
            'privado'                => $request->boolean('privado'),
            'aprovacao_manual'       => $request->boolean('aprovacao_manual'),
            'permitir_comentarios'   => $request->boolean('permitir_comentarios'),
            'participantes_publicos' => $request->boolean('participantes_publicos'),
            'notif_nova_inscricao'   => $request->boolean('notif_nova_inscricao'),
            'notif_lembrete_24h'     => $request->boolean('notif_lembrete_24h'),
            'notif_resumo_semanal'   => $request->boolean('notif_resumo_semanal'),
            'imagem_capa'            => $caminhoImagem,
            'status'                 => $request->status ?? 'rascunho',
        ]);

        // ✅ Galeria em batch insert — 1 query em vez de N
        if ($request->hasFile('galeria')) {
            $fotos = [];
            foreach ($request->file('galeria') as $foto) {
                $fotos[] = [
                    'evento_id'  => $evento->id,
                    'caminho'    => $foto->store('galeria_eventos', 'public'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            EventoFoto::insert($fotos);
        }

        // ✅ Ingressos em batch insert — 1 query em vez de N
        if ($request->filled('ingressos')) {
            $ingressos = [];
            foreach ($request->ingressos as $ingresso) {
                if (empty($ingresso['nome'])) continue;

                $precoBase  = floatval($ingresso['preco']);
                $precoFinal = $precoBase + round($precoBase * 0.10);

                $ingressos[] = [
                    'evento_id'             => $evento->id,
                    'nome'                  => $ingresso['nome'],
                    'preco'                 => $precoFinal,
                    'quantidade_disponivel' => intval($ingresso['quantidade']),
                    'quantidade_total'      => intval($ingresso['quantidade']),
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ];
            }
            if (!empty($ingressos)) {
                TipoIngresso::insert($ingressos);
            }
        }

        // ✅ Limpa cache de categorias ao criar evento (boa prática)
        Cache::forget('categorias_com_subcategorias');

        return redirect()->route('admin.eventos')->with('success', 'Evento criado com sucesso!');
    }

    public function edit($id)
    {
        // ✅ Select específico
        $evento = Evento::with([
                'tiposIngresso:id,evento_id,nome,preco,quantidade_disponivel,quantidade_total',
            ])
            ->select('id','user_id','titulo','descricao','localizacao','municipio','provincia',
                     'data_evento','data_fim','hora_inicio','hora_fim','multiplos_dias','online',
                     'link_externo','lotacao_maxima','ingressos_por_pessoa','lista_espera',
                     'privado','aprovacao_manual','permitir_comentarios','participantes_publicos',
                     'notif_nova_inscricao','notif_lembrete_24h','notif_resumo_semanal',
                     'imagem_capa','status','categoria_id','subcategoria_id')
            ->findOrFail($id);

        if (auth()->user()->role !== 'admin' && $evento->user_id !== auth()->id()) {
            abort(403, 'Acesso negado!');
        }

        // ✅ Cache para categorias
        $categorias = Cache::remember('categorias_com_subcategorias', 600, function () {
            return Categoria::with('subcategorias')->orderBy('nome')->get();
        });

        return view('admin-eventos-editar', compact('evento', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        // ✅ Select mínimo para verificar permissão
        $evento = Evento::select('id','user_id')->findOrFail($id);

        if (auth()->user()->role !== 'admin' && $evento->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'titulo'         => 'required|max:255',
            'descricao'      => 'required',
            'localizacao'    => 'required',
            'data_evento'    => 'required|date',
            'hora_inicio'    => 'required',
            'lotacao_maxima' => 'required|integer|min:1',
            'status'         => 'required|in:rascunho,publicado,encerrado',
        ]);

        // ✅ update() mantido (precisa disparar eventos para cache/observers)
        $evento->update([
            'titulo'                 => $request->titulo,
            'descricao'              => $request->descricao,
            'link_externo'           => $request->link_externo,
            'data_evento'            => $request->data_evento,
            'data_fim'               => $request->data_fim,
            'hora_inicio'            => $request->hora_inicio,
            'hora_fim'               => $request->hora_fim,
            'multiplos_dias'         => $request->boolean('multiplos_dias'),
            'localizacao'            => $request->localizacao,
            'municipio'              => $request->municipio,
            'provincia'              => $request->provincia,
            'online'                 => $request->boolean('online'),
            'lotacao_maxima'         => $request->lotacao_maxima,
            'ingressos_por_pessoa'   => $request->ingressos_por_pessoa ?? 1,
            'lista_espera'           => $request->boolean('lista_espera'),
            'privado'                => $request->boolean('privado'),
            'aprovacao_manual'       => $request->boolean('aprovacao_manual'),
            'permitir_comentarios'   => $request->boolean('permitir_comentarios'),
            'participantes_publicos' => $request->boolean('participantes_publicos'),
            'notif_nova_inscricao'   => $request->boolean('notif_nova_inscricao'),
            'notif_lembrete_24h'     => $request->boolean('notif_lembrete_24h'),
            'notif_resumo_semanal'   => $request->boolean('notif_resumo_semanal'),
            'status'                 => $request->status,
        ]);

        return redirect()->route('admin.eventos')->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy($id)
    {
        // ✅ Select mínimo para verificar permissão antes de apagar
        $evento = Evento::select('id','user_id')->findOrFail($id);

        if (auth()->user()->role !== 'admin' && $evento->user_id !== auth()->id()) {
            abort(403);
        }

        $evento->tiposIngresso()->delete();
        $evento->delete();

        return redirect()->back()->with('success', 'Evento removido!');
    }
}