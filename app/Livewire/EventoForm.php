<?php

namespace App\Livewire;

use App\Models\Categoria;
use App\Models\Evento;
use App\Models\EventoFoto;
use App\Models\TipoIngresso;
use App\Models\User;
use App\Notifications\NovoEventoCriadoNotification;
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

    // ── Categoria ─────────────────────────────────────────────
    public ?int   $categoria_id    = null;
    public ?int   $subcategoria_id = null;
    public string $catNome         = '';
    public string $catEmoji        = '🎟';

    // ── Campos comuns ─────────────────────────────────────────
    public string $titulo       = '';
    public string $descricao    = '';
    public string $link_externo = '';

    // ── Vídeo: link externo OU upload ─────────────────────────
    public string $video_preview = '';   // URL externa (YouTube, Vimeo, etc.)
    public string $video_tipo    = 'link'; // 'link' ou 'upload'
    public $video_file           = null; // ficheiro uploaded (mp4, webm, etc.)

    public string $data_evento    = '';
    public string $data_fim       = '';
    public string $hora_inicio    = '';
    public string $hora_fim       = '';
    public bool   $multiplos_dias = false;
    public bool   $online         = false;

    // ── Localização ───────────────────────────────────────────
    public string $provincia          = '';
    public string $municipio          = '';
    public string $bairro             = '';
    public string $localizacao        = '';
    public bool   $novo_bairro        = false;
    public string $novo_bairro_nome   = '';

    // ── Viagem: rota dupla (Partida / Destino) — NOVO ───────────
    public string $partida_provincia = '';
    public string $partida_municipio = '';
    public string $partida_bairro    = '';
    public string $destino_provincia = '';
    public string $destino_municipio = '';
    public string $destino_bairro    = '';

    // ── Pessoas com foto (Artistas/Palestrantes/Elenco, conforme
    //     a categoria) — NOVO. Cada item: ['nome' => '', 'foto' => null]
    public array $pessoas = [];

    // ── Fotos únicas por categoria — NOVO ────────────────────────
    public $foto_instrutor   = null; // Workshop
    public $foto_chef        = null; // Gastronomia
    public $escudo_casa      = null; // Desporto
    public $escudo_visitante = null; // Desporto

    // ── Meta ──────────────────────────────────────────────────
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

    // ── Upload imagens ────────────────────────────────────────
    public $imagem_capa = null;
    public array $galeria = [];

    // ── Bilhetes bloqueados ───────────────────────────────────
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
            // ✅ Categoria por defeito só entre as ativas de Evento — uma
            // categoria desativada no painel de gestão nunca deve ficar
            // pré-selecionada num evento novo.
            $cat = Categoria::where('tipo', 'evento')->where('ativo', true)->orderBy('nome')->first();
            if ($cat) {
                $this->categoria_id = $cat->id;
                $this->catNome      = strtolower($cat->nome);
                $this->catEmoji     = '🎟';
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
        $this->catEmoji             = '🎟';
        $this->meta                 = is_array($evento->meta) ? $evento->meta : (json_decode($evento->meta ?? '{}', true) ?? []);

        // NOVO — restaura a rota dupla (Viagem) e as pessoas com foto
        // (Artistas/Palestrantes/Elenco), guardadas dentro do próprio meta.
        $this->partida_provincia = $this->meta['partida_provincia'] ?? '';
        $this->partida_municipio = $this->meta['partida_municipio'] ?? '';
        $this->partida_bairro    = $this->meta['partida_bairro'] ?? '';
        $this->destino_provincia = $this->meta['destino_provincia'] ?? '';
        $this->destino_municipio = $this->meta['destino_municipio'] ?? '';
        $this->destino_bairro    = $this->meta['destino_bairro'] ?? '';
        $this->pessoas           = $this->meta['artistas'] ?? $this->meta['palestrantes'] ?? $this->meta['elenco'] ?? [];

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

        // Detectar tipo de vídeo actual: URL ou ficheiro local
        $vp = $evento->video_preview ?? '';
        if (!empty($vp)) {
            if (str_starts_with($vp, 'http')) {
                $this->video_preview = $vp;
                $this->video_tipo    = 'link';
            } else {
                $this->video_preview = $vp; // caminho local
                $this->video_tipo    = 'upload';
            }
        }

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
        // ✅ Só categorias de Evento e ATIVAS entram na lista de escolha —
        // uma categoria desativada no painel de gestão deixa de aparecer
        // aqui, mas eventos já criados com ela continuam a funcionar.
        return Cache::remember('categorias_lista_lw', 600, fn() =>
            Categoria::where('tipo', 'evento')->where('ativo', true)->orderBy('nome')->get()
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

    // NOVO — mesma lógica, duplicada para a rota de Viagem (Partida/Destino)
    public function getPartidaMunicipiosProperty(): array
    {
        return $this->localizacoes[$this->partida_provincia] ?? [];
    }

    public function getPartidaBairrosProperty(): array
    {
        return $this->bairrosPorMunicipio[$this->partida_municipio] ?? [];
    }

    public function getDestinoMunicipiosProperty(): array
    {
        return $this->localizacoes[$this->destino_provincia] ?? [];
    }

    public function getDestinoBairrosProperty(): array
    {
        return $this->bairrosPorMunicipio[$this->destino_municipio] ?? [];
    }

    public function getSubcategoriasProperty(): array
    {
        if (!$this->categoria_id) return [];
        // ✅ Só subcategorias ATIVAS aparecem para escolher num evento novo/editado.
        $cat = Categoria::with(['subcategorias' => function ($q) {
            $q->where('ativo', true);
        }])->find($this->categoria_id);
        return $cat ? $cat->subcategorias->toArray() : [];
    }

    // ─────────────────────────────────────────────────────────
    // SELECTORES DE CATEGORIA
    // ─────────────────────────────────────────────────────────
    public function selectCategoria(int $id, string $nome, string $emoji = '🎟'): void
    {
        $this->categoria_id    = $id;
        $this->catNome         = strtolower($nome);
        $this->catEmoji        = '🎟';
        $this->subcategoria_id = null;
        $this->meta            = [];

        // NOVO — limpa também os campos específicos de categoria, para não
        // ficar lixo de uma categoria anterior escondido dentro do formulário.
        $this->partida_provincia = $this->partida_municipio = $this->partida_bairro = '';
        $this->destino_provincia = $this->destino_municipio = $this->destino_bairro = '';
        $this->pessoas = [];
        $this->foto_instrutor = $this->foto_chef = $this->escudo_casa = $this->escudo_visitante = null;
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
        $this->bairro           = $this->novo_bairro_nome;
        $this->localizacao      = "{$this->bairro}, {$this->municipio}, {$this->provincia}";
        $this->novo_bairro      = false;
        $this->novo_bairro_nome = '';
    }

    // NOVO — mesma lógica em cascata, duplicada para Partida e Destino (Viagem)
    public function updatedPartidaProvincia(): void
    {
        $this->partida_municipio = '';
        $this->partida_bairro    = '';
    }

    public function updatedPartidaMunicipio(): void
    {
        $this->partida_bairro = '';
    }

    public function updatedDestinoProvincia(): void
    {
        $this->destino_municipio = '';
        $this->destino_bairro    = '';
    }

    public function updatedDestinoMunicipio(): void
    {
        $this->destino_bairro = '';
    }

    // ─────────────────────────────────────────────────────────
    // PESSOAS COM FOTO (Artistas / Palestrantes / Elenco) — NOVO
    // ─────────────────────────────────────────────────────────
    public function adicionarPessoa(): void
    {
        $this->pessoas[] = ['nome' => '', 'foto' => null];
    }

    public function removerPessoa(int $index): void
    {
        unset($this->pessoas[$index]);
        $this->pessoas = array_values($this->pessoas);
    }

    // NOVO — alterna um valor dentro/fora de uma lista guardada num campo do
    // meta (ex: comodidades do Festival, restrições alimentares da
    // Gastronomia), sem precisar de montar JSON no lado do Blade.
    public function toggleMetaLista(string $campo, string $valor): void
    {
        $atuais = $this->meta[$campo] ?? [];
        $this->meta[$campo] = in_array($valor, $atuais)
            ? array_values(array_diff($atuais, [$valor]))
            : array_merge($atuais, [$valor]);
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

            // Segurança: repete a validação de todos os passos no servidor
        foreach ([1, 2, 3] as $passo) {
            $this->validateStep($passo);
        }

        // Segurança: valida ingressos e imagem de capa
        $this->validate([
            'ingressos'              => 'required|array|min:1|max:10',
            'ingressos.*.nome'       => 'nullable|string|max:100',
            'ingressos.*.preco'      => 'nullable|numeric|min:0|max:100000',
            'ingressos.*.quantidade' => 'nullable|integer|min:1|max:100000',
            'imagem_capa'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status'                 => 'required|in:rascunho,publicado,encerrado',
        ], [
            'ingressos.*.preco.min'      => 'O preço não pode ser negativo.',
            'ingressos.*.quantidade.min' => 'A quantidade tem de ser pelo menos 1.',
            'imagem_capa.image'          => 'A capa tem de ser uma imagem.',
            'imagem_capa.mimes'          => 'A capa tem de ser JPG, PNG ou WebP.',
            'imagem_capa.max'            => 'A capa não pode ultrapassar 5 MB.',
        ]);
                // Segurança: valida os restantes uploads (fotos, escudos, galeria, pessoas)
        $regrasUploads = [
            'foto_instrutor'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'foto_chef'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'escudo_casa'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'escudo_visitante' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'galeria'          => 'nullable|array|max:20',
            'galeria.*'        => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ];

        // Fotos das pessoas: só valida os uploads novos (as já guardadas são texto)
        foreach ($this->pessoas ?? [] as $i => $p) {
            if (!empty($p['foto']) && is_object($p['foto'])) {
                $regrasUploads["pessoas.$i.foto"] = 'image|mimes:jpg,jpeg,png,webp|max:5120';
            }
        }

        $this->validate($regrasUploads, [
            'image'       => 'O ficheiro tem de ser uma imagem.',
            'mimes'       => 'Só são aceites imagens JPG, PNG ou WebP.',
            'max'         => 'A imagem não pode ultrapassar 5 MB.',
            'galeria.max' => 'A galeria pode ter no máximo 20 fotos.',
        ]);


        if (!$this->termos) {
            $this->addError('termos', 'Tens de aceitar os termos de publicação.');
            return;
        }

        // NOVO — impede guardar um evento sem nenhum bilhete válido. Antes,
        // um bilhete sem "nome" preenchido era ignorado em silêncio, e o
        // evento acabava por não ter nenhum tipo de bilhete — o que fazia a
        // página mostrar "Grátis", mesmo que um preço tivesse sido escrito.
        $temBilheteValido = collect($this->ingressos)->contains(
            fn($i) => !empty($i['bloqueado']) || (!empty($i['nome']) && $i['preco'] !== '' && $i['preco'] !== null)
        );
        if (!$temBilheteValido) {
            $this->addError('ingressos', 'Preenche pelo menos um tipo de bilhete com nome e preço antes de guardar.');
            $this->step = 4;
            return;
        }

        // Validar vídeo conforme o tipo escolhido
        if ($this->video_tipo === 'link' && !empty($this->video_preview)) {
            $this->validate([
                'video_preview' => 'url|max:500',
            ], [
                'video_preview.url' => 'O link do vídeo deve ser um URL válido.',
            ]);
        }

        if ($this->video_tipo === 'upload') {
            $this->validate([
                'video_file' => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg,video/quicktime|max:102400',
            ], [
                'video_file.mimetypes' => 'O vídeo deve ser MP4, WebM, OGG ou MOV.',
                'video_file.max'       => 'O vídeo não pode ultrapassar 100 MB.',
            ]);
        }

        // Upload imagem de capa
        $caminhoImagem = null;
        if ($this->imagem_capa) {
            $caminhoImagem = $this->imagem_capa->store('capas_eventos', 'public');
        }

        // Determinar o valor final de video_preview
        $videoFinal = null;
        if ($this->video_tipo === 'link' && !empty($this->video_preview)) {
            $videoFinal = $this->video_preview;
        } elseif ($this->video_tipo === 'upload' && $this->video_file) {
            // Upload do vídeo para storage
            $videoFinal = $this->video_file->store('videos_eventos', 'public');
        } elseif ($this->video_tipo === 'upload' && $this->editando && !empty($this->video_preview)) {
            // Manter vídeo actual se não fez novo upload
            $videoFinal = $this->video_preview;
        }

        // ─────────────────────────────────────────────────────
        // NOVO — junta ao $meta tudo o que vem dos campos específicos
        // de categoria (rota dupla da Viagem, fotos únicas, pessoas com
        // foto). Não mexe em nenhuma chave que já lá estivesse (dresscode,
        // lineup, camping, etc.) — só acrescenta.
        // ─────────────────────────────────────────────────────
        if ($this->partida_provincia || $this->destino_provincia) {
            $this->meta['partida_provincia'] = $this->partida_provincia;
            $this->meta['partida_municipio'] = $this->partida_municipio;
            $this->meta['partida_bairro']    = $this->partida_bairro;
            $this->meta['destino_provincia'] = $this->destino_provincia;
            $this->meta['destino_municipio'] = $this->destino_municipio;
            $this->meta['destino_bairro']    = $this->destino_bairro;
        }

        if ($this->foto_instrutor) {
            $this->meta['foto_instrutor'] = $this->foto_instrutor->store('meta_eventos', 'public');
        }
        if ($this->foto_chef) {
            $this->meta['foto_chef'] = $this->foto_chef->store('meta_eventos', 'public');
        }
        if ($this->escudo_casa) {
            $this->meta['escudo_casa'] = $this->escudo_casa->store('meta_eventos', 'public');
        }
        if ($this->escudo_visitante) {
            $this->meta['escudo_visitante'] = $this->escudo_visitante->store('meta_eventos', 'public');
        }

        // Pessoas com foto — a chave usada dentro do meta depende da categoria
        // (Artistas para Show/Festival, Palestrantes para Conferência, Elenco
        // para Cultura), para não misturar conceitos diferentes no mesmo sítio.
        if (!empty($this->pessoas)) {
            $chavePessoas = match(true) {
                str_contains($this->catNome, 'confer') => 'palestrantes',
                str_contains($this->catNome, 'cultura') => 'elenco',
                default => 'artistas',
            };
            $pessoasFinal = [];
            foreach ($this->pessoas as $p) {
                if (empty($p['nome'])) continue;
                $fotoPath = null;
                if (!empty($p['foto']) && is_object($p['foto'])) {
                    // Upload novo, feito agora
                    $fotoPath = $p['foto']->store('meta_eventos', 'public');
                } elseif (!empty($p['foto']) && is_string($p['foto'])) {
                    // Já guardada antes (edição, sem novo upload para esta pessoa)
                    $fotoPath = $p['foto'];
                }
                $pessoasFinal[] = ['nome' => strip_tags($p['nome']), 'foto' => $fotoPath];
            }
            $this->meta[$chavePessoas] = $pessoasFinal;
        }

        $dados = [
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
            'video_preview'          => $videoFinal,
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
            'meta'                   => !empty($this->meta) ? $this->meta : null,
        ];

        if ($this->editando && $this->eventoId) {
            $evento = Evento::findOrFail($this->eventoId);

            if (auth()->user()->role !== 'admin' && $evento->user_id !== auth()->id()) {
                abort(403);
            }

            // Apagar vídeo local antigo se foi substituído por novo upload
            if ($this->video_tipo === 'upload' && $this->video_file && !empty($evento->video_preview) && !str_starts_with($evento->video_preview, 'http')) {
                Storage::disk('public')->delete($evento->video_preview);
            }

            $evento->update($dados);
            if ($evento->podeMudarEstadoPara($this->status, auth()->user())) {
                $evento->status = $this->status;
            }
            if ($caminhoImagem) {
                if ($evento->imagem_capa) Storage::disk('public')->delete($evento->imagem_capa);
                $evento->imagem_capa = $caminhoImagem;
            }
            $evento->save();
        } else {
            $evento = new Evento();
            $evento->fill($dados);
            $evento->user_id     = auth()->id();
            $evento->status      = $evento->podeMudarEstadoPara($this->status, auth()->user()) ? $this->status : 'rascunho';
            $evento->imagem_capa = $caminhoImagem;
            $evento->save();

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

            // Notifica os admins de que um novo evento foi criado
            $notificacaoEvento = NovoEventoCriadoNotification::fromEvento($evento, auth()->user());
            User::where('role', 'admin')->get()->each(
                fn ($admin) => $admin->notify($notificacaoEvento)
            );
        }

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
                    // NOVO — "Passe Completo" (Festival): guarda quantos dias este
                    // bilhete cobre, para o BilheteService emitir um QR por dia.
                    'dias_validos'          => !empty($ingresso['passe_completo'])
                                                    ? (int) ($this->meta['dias_festival'] ?? 0) ?: null
                                                    : null,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ];
            }
            TipoIngresso::insert($ingressosDb);
        }

        Cache::forget('categorias_lista_lw');
        Cache::forget('categorias_com_subcategorias');

        session()->flash(
            'success',
            $this->editando
                ? 'O teu evento foi atualizado com sucesso!'
                : 'O teu evento foi criado com sucesso!'
        );

        $this->redirect(route('admin.eventos'), navigate: true);
    }

    public function render()
    {
        return view('livewire.evento-form');
    }
}