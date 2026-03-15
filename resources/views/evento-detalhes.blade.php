@extends('layouts.app')

@section('title', $evento->titulo)

@section('content')

<style>
[x-cloak]{display:none!important;}

.ev-detail { max-width: 1100px; margin: 0 auto; padding: 32px 16px; }

.ev-gallery { display: grid; grid-template-columns: 2fr 1fr; gap: 6px; border-radius: 20px; overflow: hidden; margin-bottom: 28px; max-height: 480px; }
.ev-gallery-main { height: 480px; }
.ev-gallery-main img { width: 100%; height: 100%; object-fit: cover; }
.ev-gallery-side { display: grid; grid-template-rows: repeat(2, 1fr); gap: 6px; }
.ev-gallery-side img { width: 100%; height: 100%; object-fit: cover; }
.ev-gallery-more { position: relative; }
.ev-gallery-more img { width: 100%; height: 100%; object-fit: cover; filter: brightness(0.45); }
.ev-gallery-more-label { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 800; color: #fff; }

.ev-cols { display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: flex-start; }

.card { background: #fff; border-radius: 20px; box-shadow: 0 2px 16px rgba(0,0,0,0.07); padding: 28px; margin-bottom: 20px; }
.card:last-child { margin-bottom: 0; }

.ev-title { font-size: 28px; font-weight: 800; color: #0f172a; margin-bottom: 16px; line-height: 1.25; }

.ev-meta { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; }
.ev-meta-item { display: flex; align-items: center; gap: 6px; font-size: 13.5px; color: #475569; background: #f1f5f9; padding: 6px 12px; border-radius: 999px; font-weight: 500; }
.ev-meta-item.online { background: #dcfce7; color: #15803d; }
.ev-meta-item.cat { background: #ede9fe; color: #6d28d9; }

.ev-desc { font-size: 15px; color: #475569; line-height: 1.8; }

.msg-btn { display: inline-flex; align-items: center; gap: 7px; background: #2563eb; color: #fff; padding: 8px 16px; border-radius: 999px; font-size: 13px; font-weight: 700; text-decoration: none; transition: background .15s; border: none; cursor: pointer; }
.msg-btn:hover { background: #1d4ed8; }

.sticky-col { position: sticky; top: 84px; }

.ticket-card { border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 16px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; transition: border-color .15s; }
.ticket-card:hover { border-color: #2563eb; }
.ticket-card:last-child { margin-bottom: 0; }
.ticket-nome { font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 3px; }
.ticket-restam { font-size: 11.5px; color: #ef4444; font-weight: 700; }
.ticket-preco { font-size: 17px; font-weight: 800; color: #0f172a; text-align: right; margin-bottom: 6px; }
.buy-btn { background: #2563eb; color: #fff; padding: 8px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; transition: background .15s; white-space: nowrap; }
.buy-btn:hover { background: #1d4ed8; }
.buy-btn:disabled { background: #94a3b8; cursor: not-allowed; }

.section-title { font-size: 17px; font-weight: 800; color: #0f172a; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }

.info-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 13.5px; }
.info-row:last-child { border-bottom: none; }
.info-label { color: #94a3b8; font-weight: 600; }
.info-val { color: #0f172a; font-weight: 600; text-align: right; }

.modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,.65); backdrop-filter: blur(4px); z-index: 998; }
.modal-box { position: fixed; inset: 0; z-index: 999; display: flex; align-items: center; justify-content: center; padding: 16px; }
.modal-inner { background: #fff; border-radius: 20px; width: 100%; max-width: 440px; padding: 32px; box-shadow: 0 24px 48px rgba(0,0,0,.2); }
.modal-title { font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 20px; }
.modal-close { background: none; border: none; font-size: 26px; color: #94a3b8; cursor: pointer; line-height: 1; }
.modal-close:hover { color: #ef4444; }
.modal-summary { background: #eff6ff; border-radius: 12px; padding: 14px 16px; margin-bottom: 16px; }
.modal-summary-nome { font-size: 15px; font-weight: 700; color: #1e40af; margin-bottom: 4px; }
.modal-summary-preco { font-size: 22px; font-weight: 800; color: #2563eb; }
.form-input { width: 100%; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 11px 14px; font-size: 14px; color: #0f172a; outline: none; transition: border-color .15s; margin-bottom: 12px; box-sizing: border-box; }
.form-input:focus { border-color: #2563eb; }
.form-label { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 5px; display: block; }
.qty-row { display: flex; align-items: center; justify-content: space-between; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; margin-bottom: 12px; }
.qty-label { font-size: 14px; color: #475569; font-weight: 600; }
.qty-input { width: 60px; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 5px 8px; text-align: center; font-size: 14px; font-weight: 700; }
.submit-btn { width: 100%; background: #2563eb; color: #fff; padding: 14px; border-radius: 12px; font-size: 15px; font-weight: 800; border: none; cursor: pointer; transition: background .15s; }
.submit-btn:hover { background: #1d4ed8; }

@media(max-width:768px) {
    .ev-cols { grid-template-columns: 1fr; }
    .ev-gallery { grid-template-columns: 1fr; }
    .ev-gallery-side { display: none; }
    .sticky-col { position: static; }
}
</style>

<div x-data="{
    modalAberto: false,
    ingressoNome: '',
    ingressoPreco: 0,
    ingressoId: '',
    quantidade: 1,
    abrirModal(nome, preco, id) {
        this.ingressoNome = nome;
        this.ingressoPreco = preco;
        this.ingressoId = id;
        this.quantidade = 1;
        this.modalAberto = true;
    }
}" class="ev-detail">

    {{-- GALERIA --}}
    @php
        $fotos = $evento->fotos;
        $temCapa = $evento->imagem_capa;
        $temFotos = $fotos->count() > 0;
    @endphp

    @if($temCapa || $temFotos)
    <div class="ev-gallery">
        <div class="ev-gallery-main">
            @if($temCapa)
                <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ $evento->titulo }}">
            @elseif($temFotos)
                <img src="{{ asset('storage/'.$fotos->first()->caminho) }}" alt="{{ $evento->titulo }}">
            @endif
        </div>
        @if($temFotos && $fotos->count() >= 2)
        <div class="ev-gallery-side">
            <img src="{{ asset('storage/'.$fotos[0]->caminho) }}" alt="Foto 1">
            @if($fotos->count() >= 3)
                @if($fotos->count() > 3)
                <div class="ev-gallery-more">
                    <img src="{{ asset('storage/'.$fotos[1]->caminho) }}" alt="Foto 2">
                    <div class="ev-gallery-more-label">+{{ $fotos->count() - 2 }}</div>
                </div>
                @else
                <img src="{{ asset('storage/'.$fotos[1]->caminho) }}" alt="Foto 2">
                @endif
            @endif
        </div>
        @endif
    </div>
    @endif

    <div class="ev-cols">

        {{-- COLUNA ESQUERDA --}}
        <div>
            <div class="card">
                <h1 class="ev-title">{{ $evento->titulo }}</h1>

                <div class="ev-meta">
                    <span class="ev-meta-item">📅 {{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}</span>

                    @if($evento->hora_inicio)
                        <span class="ev-meta-item">⏰ {{ \Illuminate\Support\Str::substr($evento->hora_inicio, 0, 5) }}
                            @if($evento->hora_fim) – {{ \Illuminate\Support\Str::substr($evento->hora_fim, 0, 5) }} @endif
                        </span>
                    @endif

                    @if($evento->data_fim && $evento->data_fim !== $evento->data_evento)
                        <span class="ev-meta-item">🗓 Até {{ \Carbon\Carbon::parse($evento->data_fim)->format('d/m/Y') }}</span>
                    @endif

                    <span class="ev-meta-item">📍 {{ $evento->localizacao }}</span>

                    @if($evento->municipio)
                        <span class="ev-meta-item">🏙 {{ $evento->municipio }}@if($evento->provincia), {{ $evento->provincia }}@endif</span>
                    @endif

                    @if($evento->online)
                        <span class="ev-meta-item online">🌐 Evento online</span>
                    @endif

                    @if($evento->categoria)
                        <span class="ev-meta-item cat">{{ $evento->categoria->emoji }} {{ $evento->categoria->nome }}
                            @if($evento->subcategoria) · {{ $evento->subcategoria->nome }} @endif
                        </span>
                    @endif

                    <a href="{{ route('mensagens.index', ['user_id' => $evento->user_id, 'evento_id' => $evento->id]) }}"
                       class="msg-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        Contactar organizador
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="section-title">📋 Sobre o evento</div>
                <div class="ev-desc">{!! nl2br(e($evento->descricao)) !!}</div>

                @if($evento->link_externo)
                    <a href="{{ $evento->link_externo }}" target="_blank"
                       style="display:inline-flex;align-items:center;gap:6px;margin-top:16px;font-size:13.5px;color:#2563eb;font-weight:700;text-decoration:none;">
                        🔗 Ver mais informações
                    </a>
                @endif
            </div>

            {{-- GALERIA COMPLETA --}}
            @if($fotos->count() > 0)
            <div class="card">
                <div class="section-title">🖼️ Galeria</div>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:8px;">
                    @foreach($fotos as $foto)
                    <img src="{{ asset('storage/'.$foto->caminho) }}"
                         style="width:100%;height:110px;object-fit:cover;border-radius:10px;cursor:pointer;"
                         alt="Foto do evento">
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- COLUNA DIREITA --}}
        <div class="sticky-col">
            <div class="card">
                <div class="section-title">🎟️ Bilhetes</div>
                @forelse($evento->tiposIngresso as $tipo)
                <div class="ticket-card">
                    <div>
                        <div class="ticket-nome">{{ $tipo->nome }}</div>
                        <div class="ticket-restam">
                            @if($tipo->quantidade_disponivel > 0)
                                Restam {{ $tipo->quantidade_disponivel }}
                            @else
                                Esgotado
                            @endif
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div class="ticket-preco">{{ number_format($tipo->preco, 0, ',', '.') }} Kz</div>
                        <button
                            @click="abrirModal('{{ $tipo->nome }}', {{ $tipo->preco }}, '{{ $tipo->id }}')"
                            class="buy-btn"
                            {{ $tipo->quantidade_disponivel <= 0 ? 'disabled' : '' }}>
                            {{ $tipo->quantidade_disponivel > 0 ? 'Comprar' : 'Esgotado' }}
                        </button>
                    </div>
                </div>
                @empty
                <p style="font-size:14px;color:#94a3b8;text-align:center;padding:16px 0;">
                    Sem bilhetes disponíveis de momento.
                </p>
                @endforelse
            </div>

            <div class="card">
                <div class="section-title">ℹ️ Detalhes</div>
                <div class="info-row">
                    <span class="info-label">Data</span>
                    <span class="info-val">{{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}</span>
                </div>
                @if($evento->hora_inicio)
                <div class="info-row">
                    <span class="info-label">Hora</span>
                    <span class="info-val">{{ \Illuminate\Support\Str::substr($evento->hora_inicio, 0, 5) }}@if($evento->hora_fim) – {{ \Illuminate\Support\Str::substr($evento->hora_fim, 0, 5) }}@endif</span>
                </div>
                @endif
                @if($evento->municipio)
                <div class="info-row">
                    <span class="info-label">Município</span>
                    <span class="info-val">{{ $evento->municipio }}</span>
                </div>
                @endif
                @if($evento->provincia)
                <div class="info-row">
                    <span class="info-label">Província</span>
                    <span class="info-val">{{ $evento->provincia }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Local</span>
                    <span class="info-val">{{ $evento->localizacao }}</span>
                </div>
                @if($evento->lotacao_maxima)
                <div class="info-row">
                    <span class="info-label">Lotação</span>
                    <span class="info-val">{{ number_format($evento->lotacao_maxima, 0, ',', '.') }} pessoas</span>
                </div>
                @endif
                @if($evento->multiplos_dias)
                <div class="info-row">
                    <span class="info-label">Duração</span>
                    <span class="info-val">Múltiplos dias</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Formato</span>
                    <span class="info-val">{{ $evento->online ? '🌐 Online' : '📍 Presencial' }}</span>
                </div>
                @if($evento->categoria)
                <div class="info-row">
                    <span class="info-label">Categoria</span>
                    <span class="info-val">{{ $evento->categoria->emoji }} {{ $evento->categoria->nome }}</span>
                </div>
                @endif
                @if($evento->subcategoria)
                <div class="info-row">
                    <span class="info-label">Subcategoria</span>
                    <span class="info-val">{{ $evento->subcategoria->nome }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div x-show="modalAberto" x-cloak>
        <div class="modal-backdrop" @click="modalAberto = false"></div>
        <div class="modal-box">
            <div x-show="modalAberto" x-transition class="modal-inner">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                    <span class="modal-title">Finalizar compra</span>
                    <button @click="modalAberto = false" class="modal-close">&times;</button>
                </div>

                <div class="modal-summary">
                    <div class="modal-summary-nome" x-text="ingressoNome"></div>
                    <div class="modal-summary-preco" x-text="(Number(ingressoPreco) * Number(quantidade)).toLocaleString('pt-PT') + ' Kz'"></div>
                </div>

                <form action="{{ route('reserva.guardar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="tipo_ingresso_id" :value="ingressoId">
                    <input type="hidden" name="evento_id" value="{{ $evento->id }}">

                    <label class="form-label">O teu nome</label>
                    <input type="text" name="nome_cliente" placeholder="Nome completo" required class="form-input">

                    <label class="form-label">WhatsApp</label>
                    <input type="text" name="whatsapp" placeholder="+244 9XX XXX XXX" required class="form-input">

                    <div class="qty-row">
                        <span class="qty-label">Quantidade</span>
                        <input type="number" name="quantidade" min="1" x-model="quantidade" class="qty-input">
                    </div>

                    <label class="form-label">Comprovativo de pagamento</label>
                    <input type="file" name="comprovativo" required class="form-input" style="padding:8px 14px;">

                    <button type="submit" class="submit-btn" style="margin-top:4px;">Confirmar compra</button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection