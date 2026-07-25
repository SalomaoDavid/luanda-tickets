<?php

namespace App\Livewire;

use App\Models\Categoria;
use App\Models\Evento;
use App\Models\EventoFoto;
use App\Models\TipoIngresso;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class EventoForm extends Component
{
    use WithFileUploads;

    // ── Modo ──────────────────────────────────────────────────
    public ?int  $eventoId      = null;
    public bool  $editando      = false;
    public string $statusOriginal = 'rascunho';

    // ── Step ──────────────────────────────────────────────────
    public int $step       = 1;
    public int $totalSteps = 5;

    // ── Categoria seleccionada no topo ────────────────────────
    public ?int   $categoria_id    = null;
    public ?int   $subcategoria_id = null;
    public string $catNome         = '';
    public string $catEmoji        = '🎟';

    // ── Campos comuns ─────────────────────────────────────────
    public string $titulo       = '';
    public string $descricao    = '';
    public string $link_externo = '';

    public string $data_evento    = '';
    public string $data_fim       = '';
    public string $hora_inicio    = '';
    public string $hora_fim       = '';
    public bool   $multiplos_dias = false;
    public bool   $online         = false;

    // ── Localização em cascata ────────────────────────────────
    public string $provincia          = '';
    public string $municipio          = '';
    public string $bairro             = '';
    public string $localizacao        = '';
    public bool   $novo_bairro        = false;
    public string $novo_bairro_nome   = '';

    // ── Meta (campos específicos por categoria) ───────────────
    public array $meta = [];

    // ── Bilhetes ──────────────────────────────────────────────
    public array $ingressos = [
        ['nome' => '', 'preco' => '', 'quantidade' => '']
    ];
    public int  $lotacao_maxima       = 100;
    public int  $ingressos_por_pessoa = 1;
    public bool $lista_espera         = false;

    // ── Definições ────────────────────────────────────────────
    public bool $privado                = false;
    public bool $aprovacao_manual       = false;
    public bool $permitir_comentarios   = true;
    public bool $participantes_publicos = true;
    public bool $notif_nova_inscricao   = true;
    public bool $notif_lembrete_24h     = true;
    public bool $notif_resumo_semanal   = false;

    // ── Estado / Termos ───────────────────────────────────────
    public string $status = 'rascunho';
    public bool   $termos = false;

    // ── Upload ────────────────────────────────────────────────
    public $imagem_capa = null;
    public array $galeria = [];

    // ── Bilhetes bloqueados (evento publicado) ────────────────
    public array $bilhetesBloquedos = [];

    // ── Dados de localização ──────────────────────────────────
    protected array $localizacoes = [
        'Luanda'         => ['Luanda','Viana','Cacuaco','Belas','Cazenga','Kilamba Kiaxi','Talatona','Icolo e Bengo'],
        'Benguela'       => ['Benguela','Lobito','Baía Farta','Balombo','Bocoio','Chongoroi','Cubal','Ganda'],
        'Huíla'          => ['Lubango','Caluquembe','Chibia','Humpata','Matala','Quilengues'],
        'Huambo'         => ['Huambo','Bailundo','Caála','Longonjo','Mungo'],
        'Cabinda'        => ['Cabinda','Belize','Buco-Zau','Cacongo'],
        'Kwanza Norte'   => ['Ndalatando','Ambaca','Cambambe','Cazengo','Golungo Alto'],
        'Malanje'        => ['Malanje','Cacuso','Calandula','Cangandala'],
        'Namibe'         => ['Namibe','Bibala','Camucuio','Tômbua','Virei'],
        'Uíge'           => ['Uíge','Damba','Maquela do Zombo','Negage','Songo','Zombo'],
        'Zaire'          => ['Mbanza Kongo','Cuimba','Noqui','Nzeto','Soyo'],
        'Bié'            => ['Kuito','Andulo','Camacupa','Catabola','Chinguar'],
        'Kwanza Sul'     => ['Sumbe','Amboim','Cassongue','Cela','Porto Amboim'],
        'Lunda Norte'    => ['Dundo','Alto Chicapa','Cambulo','Chitato','Cuango'],
        'Lunda Sul'      => ['Saurimo','Cacolo','Dala','Muconda'],
        'Moxico'         => ['Luena','Alto Zambeze','Luacano','Luau'],
        'Cunene'         => ['Ondjiva','Cahama','Cuanhama','Namacunde'],
        'Cuando Cubango' => ['Menongue','Calai','Cuangar','Dirico','Mavinga'],
    ];

    protected array $bairrosPorMunicipio = [
        'Luanda'       => ['Alvalade','Bairro Operário','Benfica','Ingombota','KK','Kilamba','Maianga','Miramar','Mutamba','Patriota','Rangel','Samba','Talatona'],
        'Viana'        => ['Viana Sede','Calumbo','Km 30','Mulenvos','Petrangol'],
        'Belas'        => ['Talatona','Camama','Sequele','Morro Bento','Vila Alice'],
        'Cacuaco'      => ['Cacuaco Sede','Funda','São Pedro da Barra'],
        'Benguela'     => ['Bairro Operário','Centro','Marçal','Praia Morena','Salinas'],
        'Lobito'       => ['Centro','Restinga','Bairro Popular','Catumbela'],
        'Lubango'      => ['Bairro Académico','Comandante Bula','Lubango Centro'],
        'Huambo'       => ['Huambo Centro','São Pedro','Terra Prometida'],
        'Cabinda'      => ['Cabinda Centro','Malembo','Tchiowa'],
        'Malanje'      => ['Malanje Centro','Bairro Popular','Kalandula'],
        'Namibe'       => ['Namibe Centro','Bairro Popular'],
        'Soyo'         => ['Soyo Centro','Bairro Petrolífero','Nzeto'],
        'Kuito'        => ['Kuito Centro','Bairro Popular'],
        'Dundo'        => ['Dundo Centro','Chitato'],
        'Saurimo'      => ['Saurimo Centro','Bairro da Missão'],
        'Luena'        => ['Luena Centro','Bairro Popular'],
        'Menongue'     => ['Menongue Centro','Bairro Popular'],
        'Ondjiva'      => ['Ondjiva Centro','Bairro Popular'],
        'Mbanza Kongo' => ['Mbanza Kongo Centro','São Salvador'],
        'Ndalatando'   => ['Ndalatando Centro','Bairro Popular'],
        'Sumbe'        => ['Sumbe Centro','Porto Amboim'],
    ];

    // ─────────────────────────────────────────────────────────
    // MOUNT
    // ─────────────────────────────────────────────────────────
    public function mount(?int $eventoId = null): void
    {
        if ($eventoId) {
            $this->eventoId = $eventoId;
            $this->editando = true;
            $this->carregarEvento($eventoId);
        } else {
            // Selecciona a primeira categoria por defeito
            $cat = Categoria::orderBy('nome')->first();
            if ($cat) {
                $this->categoria_id = $cat->id;
                $this->catNome      = strtolower($cat->nome);
                $this->catEmoji     = $cat->emoji ?? '🎟';
            }
        }
    }

    protected function carregarEvento(int $id): void
    {
        $evento = Evento::with(['tiposIngresso','categoria'])->findOrFail($id);

        if (auth()->user()->role !== 'admin' && $evento->user_id !== auth()->id()) {
            abort(403);
        }

        $this->statusOriginal       = $evento->status;
        $this->titulo               = $evento->titulo ?? '';
        $this->descricao            = $evento->descricao ?? '';
        $this->link_externo         = $evento->link_externo ?? '';
        $this->data_evento          = $evento->data_evento ? substr($evento->data_evento, 0, 10) : '';
        $this->data_fim             = $evento->data_fim    ? substr($evento->data_fim, 0, 10) : '';
        $this->hora_inicio          = $evento->hora_inicio ? substr($evento->hora_inicio, 0, 5) : '';
        $this->hora_fim             = $evento->hora_fim    ? substr($evento->hora_fim, 0, 5) : '';
        $this->multiplos_dias       = (bool) $evento->multiplos_dias;
        $this->online               = (bool) $evento->online;
        $this->provincia            = $evento->provincia ?? '';
        $this->municipio            = $evento->municipio ?? '';
        $this->localizacao          = $evento->localizacao ?? '';
        $this->categoria_id         = $evento->categoria_id;
        $this->subcategoria_id      = $evento->subcategoria_id;
        $this->catNome              = strtolower(optional($evento->categoria)->nome ?? '');
        $this->catEmoji             = optional($evento->categoria)->emoji ?? '🎟';
        $this->meta                 = is_array($evento->meta) ? $evento->meta : (json_decode($evento->meta ?? '{}', true) ?? []);
        $this->lotacao_maxima       = $evento->lotacao_maxima ?? 100;
        $this->ingressos_por_pessoa = $evento->ingressos_por_pessoa ?? 1;
        $this->lista_espera         = (bool) $evento->lista_espera;
        $this->privado              = (bool) $evento->privado;
        $this->aprovacao_manual     = (bool) $evento->aprovacao_manual;
        $this->permitir_comentarios   = (bool) $evento->permitir_comentarios;
        $this->participantes_publicos = (bool) $evento->participantes_publicos;
        $this->notif_nova_inscricao   = (bool) $evento->notif_nova_inscricao;
        $this->notif_lembrete_24h     = (bool) $evento->notif_lembrete_24h;
        $this->notif_resumo_semanal   = (bool) $evento->notif_resumo_semanal;
        $this->status               = $evento->status ?? 'rascunho';

        if ($evento->status === 'publicado') {
            $this->bilhetesBloquedos = $evento->tiposIngresso->pluck('id')->toArray();
        }

        if ($evento->tiposIngresso->count() > 0) {
            $this->ingressos = $evento->tiposIngresso->map(fn($t) => [
                'id'         => $t->id,
                'nome'       => $t->nome,
                'preco'      => $t->preco,
                'quantidade' => $t->quantidade_total,
                'bloqueado'  => $evento->status === 'publicado',
            ])->toArray();
        }
    }

    // ─────────────────────────────────────────────────────────
    // COMPUTED PROPERTIES
    // ─────────────────────────────────────────────────────────
    public function getCategoriasProperty()
    {
        return Cache::remember('categorias_lista_lw', 600, fn() =>
            Categoria::orderBy('nome')->get()
        );
    }

    public function getMunicipiosProperty(): array
    {
        return $this->localizacoes[$this->provincia] ?? [];
    }

    public function getBairrosProperty(): array
    {
        return $this->bairrosPorMunicipio[$this->municipio] ?? [];
    }

    public function getSubcategoriasProperty(): array
    {
        if (!$this->categoria_id) return [];
        $cat = Categoria::with('subcategorias')->find($this->categoria_id);
        return $cat ? $cat->subcategorias->toArray() : [];
    }

    // ─────────────────────────────────────────────────────────
    // SELECTORES DE CATEGORIA (no topo)
    // ─────────────────────────────────────────────────────────
    public function selectCategoria(int $id, string $nome, string $emoji): void
    {
        $this->categoria_id    = $id;
        $this->catNome         = strtolower($nome);
        $this->catEmoji        = $emoji;
        $this->subcategoria_id = null;
        $this->meta            = []; // limpa campos específicos ao mudar categoria
    }

    // ─────────────────────────────────────────────────────────
    // LOCALIZAÇÃO
    // ─────────────────────────────────────────────────────────
    public function updatedProvincia(): void
    {
        $this->municipio   = '';
        $this->bairro      = '';
        $this->localizacao = $this->provincia;
    }

    public function updatedMunicipio(): void
    {
        $this->bairro      = '';
        $this->localizacao = $this->municipio
            ? "{$this->municipio}, {$this->provincia}"
            : $this->provincia;
    }

    public function updatedBairro(): void
    {
        if ($this->bairro) {
            $this->localizacao = "{$this->bairro}, {$this->municipio}, {$this->provincia}";
        }
    }

    public function adicionarNovoBairro(): void
    {
        if (empty(trim($this->novo_bairro_nome))) return;
        $this->bairro          = $this->novo_bairro_nome;
        $this->localizacao     = "{$this->bairro}, {$this->municipio}, {$this->provincia}";
        $this->novo_bairro     = false;
        $this->novo_bairro_nome = '';
    }

    // ─────────────────────────────────────────────────────────
    // BILHETES
    // ─────────────────────────────────────────────────────────
    public function adicionarIngresso(): void
    {
        $this->ingressos[] = ['nome' => '', 'preco' => '', 'quantidade' => ''];
    }

    public function removerIngresso(int $index): void
    {
        if (isset($this->ingressos[$index]['bloqueado']) && $this->ingressos[$index]['bloqueado']) {
            $this->addError('ingressos', 'Não podes remover bilhetes de um evento publicado.');
            return;
        }
        unset($this->ingressos[$index]);
        $this->ingressos = array_values($this->ingressos);
    }

    // ─────────────────────────────────────────────────────────
    // STEPS
    // ─────────────────────────────────────────────────────────
    public function proximoStep(): void
    {
        $this->validateStep($this->step);
        if ($this->step < $this->totalSteps) $this->step++;
    }

    public function anteriorStep(): void
    {
        if ($this->step > 1) $this->step--;
    }

    public function irParaStep(int $n): void
    {
        if ($n <= $this->step) $this->step = $n;
    }

    protected function validateStep(int $step): void
    {
        match ($step) {
            1 => $this->validate([
                'titulo'      => 'required|max:255',
                'descricao'   => 'required',
                'data_evento' => 'required|date',
                'hora_inicio' => 'required',
            ], [
                'titulo.required'      => 'O nome do evento é obrigatório.',
                'descricao.required'   => 'A descrição é obrigatória.',
                'data_evento.required' => 'A data de início é obrigatória.',
                'hora_inicio.required' => 'A hora de início é obrigatória.',
            ]),
            2 => $this->validate([
                'localizacao'  => 'required',
                'categoria_id' => 'required|exists:categorias,id',
            ], [
                'localizacao.required'  => 'O local do evento é obrigatório.',
                'categoria_id.required' => 'Selecciona uma categoria.',
            ]),
            3 => $this->validate([
                'lotacao_maxima' => 'required|integer|min:1',
            ], [
                'lotacao_maxima.required' => 'A lotação máxima é obrigatória.',
            ]),
            default => null,
        };
    }

    // ─────────────────────────────────────────────────────────
    // SAVE
    // ─────────────────────────────────────────────────────────
    public function salvar(): void
    {
        if (!$this->termos) {
            $this->addError('termos', 'Tens de aceitar os termos de publicação.');
            return;
        }

        $caminhoImagem = null;
        if ($this->imagem_capa) {
            $caminhoImagem = $this->imagem_capa->store('capas_eventos', 'public');
        }

        $dados = [
            'user_id'                => auth()->id(),
            'titulo'                 => strip_tags($this->titulo),
            'descricao'              => strip_tags($this->descricao, '<b><i><p><strong>'),
            'categoria_id'           => $this->categoria_id,
            'subcategoria_id'        => $this->subcategoria_id,
            'localizacao'            => strip_tags($this->localizacao),
            'municipio'              => strip_tags($this->municipio),
            'provincia'              => $this->provincia ?: 'Luanda',
            'data_evento'            => $this->data_evento,
            'data_fim'               => $this->data_fim ?: null,
            'hora_inicio'            => $this->hora_inicio,
            'hora_fim'               => $this->hora_fim ?: null,
            'multiplos_dias'         => $this->multiplos_dias,
            'online'                 => $this->online,
            'link_externo'           => $this->link_externo ?: null,
            'lotacao_maxima'         => $this->lotacao_maxima,
            'ingressos_por_pessoa'   => $this->ingressos_por_pessoa,
            'lista_espera'           => $this->lista_espera,
            'privado'                => $this->privado,
            'aprovacao_manual'       => $this->aprovacao_manual,
            'permitir_comentarios'   => $this->permitir_comentarios,
            'participantes_publicos' => $this->participantes_publicos,
            'notif_nova_inscricao'   => $this->notif_nova_inscricao,
            'notif_lembrete_24h'     => $this->notif_lembrete_24h,
            'notif_resumo_semanal'   => $this->notif_resumo_semanal,
            'status'                 => $this->status,
            'meta'                   => !empty($this->meta) ? $this->meta : null,
        ];

        if ($caminhoImagem) {
            $dados['imagem_capa'] = $caminhoImagem;
        }

        if ($this->editando && $this->eventoId) {
            $evento = Evento::findOrFail($this->eventoId);
            $evento->update($dados);
        } else {
            $evento = Evento::create($dados);

            if (!empty($this->galeria)) {
                $fotos = [];
                foreach ($this->galeria as $foto) {
                    $fotos[] = [
                        'evento_id'  => $evento->id,
                        'caminho'    => $foto->store('galeria_eventos', 'public'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                EventoFoto::insert($fotos);
            }
        }

        // Novos ingressos apenas
        $novos = array_filter($this->ingressos, fn($i) => empty($i['id']) && !empty($i['nome']));

        if (!empty($novos)) {
            if ($evento->status === 'publicado' && $this->editando) {
                $this->addError('ingressos', 'Não podes adicionar bilhetes a um evento publicado.');
                return;
            }
            $ingressosDb = [];
            foreach ($novos as $ingresso) {
                $base  = floatval($ingresso['preco']);
                $final = $base + round($base * 0.20);
                $ingressosDb[] = [
                    'evento_id'             => $evento->id,
                    'nome'                  => strip_tags($ingresso['nome']),
                    'preco'                 => $final,
                    'quantidade_disponivel' => intval($ingresso['quantidade']),
                    'quantidade_total'      => intval($ingresso['quantidade']),
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ];
            }
            TipoIngresso::insert($ingressosDb);
        }

        Cache::forget('categorias_lista_lw');
        Cache::forget('categorias_com_subcategorias');

        $this->redirect(route('admin.eventos'), navigate: true);
    }

    public function render()
    {
        return view('livewire.evento-form');
    }
}