@extends('layouts.app')

@section('content')

<style>
[x-cloak] { display: none !important; }

:root {
  --ink:     #0a0e1a;
  --ink2:    #111827;
  --surface: #131c2e;
  --card:    #192236;
  --border:  rgba(56,189,248,0.12);
  --border2: rgba(56,189,248,0.25);
  --sky:     #38bdf8;
  --sky2:    #0ea5e9;
  --green:   #10b981;
  --red:     #f43f5e;
  --gold:    #f59e0b;
  --purple:  #a78bfa;
  --text:    #e2e8f0;
  --muted:   #64748b;
  --muted2:  #94a3b8;
}

@keyframes fadeUp   { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
@keyframes slideIn  { from{opacity:0;transform:translateX(-12px)} to{opacity:1;transform:translateX(0)} }
@keyframes pulse-dot{ 0%,100%{opacity:1} 50%{opacity:.4} }

body { background: var(--ink); }

/* ── WRAPPER ── */
.rv-wrap { max-width: 1100px; margin: 0 auto; padding: 20px 16px 80px; }

/* ── HERO HEADER ── */
.rv-hero {
  position: relative; overflow: hidden;
  background: var(--surface);
  border: 1px solid var(--border2);
  border-radius: 24px; padding: 28px 24px 24px;
  margin-bottom: 24px;
  animation: fadeUp .4s ease both;
}
@media(min-width:768px){ .rv-hero { padding: 36px 36px 30px; border-radius: 28px; } }
.rv-hero-bg {
  position: absolute; inset: 0; z-index: 0; pointer-events: none;
  background:
    radial-gradient(ellipse at 80% 50%, rgba(56,189,248,.1) 0%, transparent 55%),
    radial-gradient(ellipse at 10% 80%, rgba(167,139,250,.07) 0%, transparent 40%);
}
.rv-hero-grid {
  position: absolute; inset: 0; z-index: 0; opacity: .03;
  background-image:
    linear-gradient(rgba(56,189,248,1) 1px, transparent 1px),
    linear-gradient(90deg, rgba(56,189,248,1) 1px, transparent 1px);
  background-size: 32px 32px;
}
.rv-hero-inner { position: relative; z-index: 1; display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.rv-hero-eyebrow { font-size: 10px; font-weight: 700; letter-spacing: 2.5px; text-transform: uppercase; color: var(--sky); margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
.rv-hero-eyebrow::before { content:''; width:20px; height:2px; background:var(--sky); border-radius:1px; }
.rv-hero-title { font-size: 26px; font-weight: 900; color: #fff; letter-spacing: -1px; line-height: 1.1; margin-bottom: 6px; }
@media(min-width:768px){ .rv-hero-title { font-size: 38px; } }
.rv-hero-title span { color: var(--sky); }
.rv-hero-sub { font-size: 13px; color: var(--muted2); }

.rv-hero-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
.btn-confirmar-link {
  display: flex; align-items: center; gap: 7px;
  padding: 11px 20px; border-radius: 14px;
  background: var(--card); border: 1px solid var(--border2);
  color: var(--sky); font-size: 12px; font-weight: 700;
  text-decoration: none; transition: all .2s;
}
.btn-confirmar-link:hover { background: rgba(56,189,248,.12); border-color: var(--sky); }
.rv-badge-pendente {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 18px; border-radius: 14px;
  background: rgba(245,158,11,.12); border: 1px solid rgba(245,158,11,.3);
  color: var(--gold); font-size: 12px; font-weight: 800;
}
.rv-badge-pendente .dot { width: 7px; height: 7px; border-radius: 50%; background: var(--gold); animation: pulse-dot 1.5s infinite; }

/* ── STATS ROW ── */
.rv-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 24px; animation: fadeUp .4s .06s ease both; }
@media(min-width:480px){ .rv-stats { gap: 14px; } }
.rv-stat {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: 16px; padding: 14px 16px;
  display: flex; flex-direction: column; gap: 4px;
  transition: border-color .2s;
}
.rv-stat:hover { border-color: var(--border2); }
.rv-stat-icon { font-size: 18px; margin-bottom: 4px; }
.rv-stat-num { font-size: 22px; font-weight: 900; color: #fff; line-height: 1; }
@media(min-width:480px){ .rv-stat-num { font-size: 28px; } }
.rv-stat-lbl { font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .8px; }

/* ── CARDS GRID ── */
.rv-grid { display: flex; flex-direction: column; gap: 12px; }
@media(min-width:640px){ .rv-grid { gap: 14px; } }

/* ── RESERVA CARD ── */
.rv-card {
  background: var(--card); border: 1px solid var(--border);
  border-radius: 20px; overflow: hidden;
  animation: fadeUp .4s ease both;
  transition: border-color .25s, transform .2s, box-shadow .2s;
}
.rv-card:hover { border-color: var(--border2); transform: translateY(-2px); box-shadow: 0 12px 40px rgba(0,0,0,.4); }

.rv-card-top {
  display: flex; align-items: flex-start; gap: 14px;
  padding: 18px 18px 14px;
  border-bottom: 1px solid var(--border);
  flex-wrap: wrap;
}
@media(min-width:640px){ .rv-card-top { flex-wrap: nowrap; } }

/* Avatar / Initials */
.rv-avatar {
  width: 44px; height: 44px; border-radius: 14px; flex-shrink: 0;
  background: linear-gradient(135deg,#0c2a3a,#1e4a6a);
  border: 1px solid var(--border2);
  display: flex; align-items: center; justify-content: center;
  font-size: 16px; font-weight: 900; color: var(--sky);
}

/* Info cliente */
.rv-client-name { font-size: 14px; font-weight: 800; color: #fff; margin-bottom: 3px; }
.rv-client-phone {
  display: flex; align-items: center; gap: 6px;
  font-size: 11px; font-family: monospace; color: var(--sky);
}
.rv-wa-btn {
  display: flex; align-items: center; justify-content: center;
  width: 22px; height: 22px; border-radius: 6px;
  background: rgba(16,185,129,.15); color: var(--green);
  transition: background .2s; flex-shrink: 0;
}
.rv-wa-btn:hover { background: rgba(16,185,129,.3); }

/* Evento info */
.rv-evento { flex: 1; min-width: 0; }
.rv-evento-nome { font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.rv-evento-tipo {
  display: inline-flex; align-items: center;
  font-size: 9px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;
  padding: 2px 8px; border-radius: 20px;
  background: rgba(167,139,250,.15); border: 1px solid rgba(167,139,250,.3); color: var(--purple);
}

/* Total + Qtd */
.rv-amounts { display: flex; gap: 16px; align-items: center; flex-shrink: 0; margin-left: auto; }
.rv-qty { text-align: center; }
.rv-qty-num { font-size: 20px; font-weight: 900; color: #fff; line-height: 1; }
.rv-qty-lbl { font-size: 9px; color: var(--muted); text-transform: uppercase; letter-spacing: .8px; }
.rv-total { text-align: right; }
.rv-total-val { font-size: 16px; font-weight: 900; color: var(--sky); line-height: 1; }
@media(min-width:480px){ .rv-total-val { font-size: 19px; } }
.rv-total-lbl { font-size: 9px; color: var(--muted); text-transform: uppercase; letter-spacing: .8px; }

/* Bottom actions */
.rv-card-bottom {
  display: flex; align-items: center; gap: 10px;
  padding: 12px 18px; flex-wrap: wrap;
}
.rv-btn-comp {
  display: flex; align-items: center; gap: 6px;
  padding: 8px 14px; border-radius: 10px;
  background: rgba(56,189,248,.08); border: 1px solid var(--border2);
  color: var(--sky); font-size: 11px; font-weight: 700;
  cursor: pointer; transition: all .2s; white-space: nowrap;
}
.rv-btn-comp:hover { background: rgba(56,189,248,.18); }
.rv-btn-confirmar {
  display: flex; align-items: center; gap: 6px;
  padding: 9px 18px; border-radius: 10px;
  background: linear-gradient(135deg, var(--green), #059669);
  color: #fff; font-size: 11px; font-weight: 800;
  border: none; cursor: pointer; transition: all .2s;
  box-shadow: 0 4px 14px rgba(16,185,129,.3);
}
.rv-btn-confirmar:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(16,185,129,.4); }
.rv-btn-eliminar {
  width: 36px; height: 36px; border-radius: 10px;
  background: rgba(244,63,94,.06); border: 1px solid rgba(244,63,94,.15);
  color: var(--muted); display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: all .2s; flex-shrink: 0;
}
.rv-btn-eliminar:hover { background: rgba(244,63,94,.15); border-color: rgba(244,63,94,.4); color: var(--red); }
.rv-btn-wa-bottom {
  display: flex; align-items: center; gap: 6px;
  padding: 9px 14px; border-radius: 10px;
  background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.25);
  color: var(--green); font-size: 11px; font-weight: 700;
  text-decoration: none; transition: all .2s; white-space: nowrap;
}
.rv-btn-wa-bottom:hover { background: rgba(16,185,129,.2); }

/* ── EMPTY ── */
.rv-empty {
  background: var(--surface); border: 1px dashed var(--border2);
  border-radius: 24px; padding: 80px 20px; text-align: center;
  animation: fadeUp .4s ease;
}
.rv-empty-icon { font-size: 52px; margin-bottom: 14px; }
.rv-empty-title { font-size: 16px; font-weight: 800; color: var(--muted2); margin-bottom: 6px; }
.rv-empty-sub { font-size: 12px; color: var(--muted); }

/* ── MODAL COMPROVATIVO ── */
.modal-overlay {
  position: fixed; inset: 0; z-index: 500;
  background: rgba(0,0,0,.85); backdrop-filter: blur(8px);
  display: flex; align-items: flex-end; justify-content: center;
}
@media(min-width:640px){ .modal-overlay { align-items: center; padding: 20px; } }
.modal-box {
  background: var(--surface); border: 1px solid var(--border2);
  border-radius: 28px 28px 0 0; width: 100%; max-width: 560px;
  overflow: hidden; max-height: 92vh; display: flex; flex-direction: column;
}
@media(min-width:640px){ .modal-box { border-radius: 28px; max-height: 88vh; } }
.modal-handle { width: 36px; height: 4px; border-radius: 2px; background: var(--border2); margin: 14px auto 0; }
@media(min-width:640px){ .modal-handle { display: none; } }
.modal-head {
  display: flex; align-items: center; justify-content: space-between;
  padding: 16px 20px; border-bottom: 1px solid var(--border);
}
.modal-head-left {}
.modal-head-eyebrow { font-size: 9px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--sky); }
.modal-head-title { font-size: 15px; font-weight: 800; color: #fff; }
.modal-close { width: 32px; height: 32px; border-radius: 10px; background: rgba(255,255,255,.05); border: none; color: var(--muted2); font-size: 18px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .2s; }
.modal-close:hover { background: rgba(244,63,94,.15); color: var(--red); }
.modal-img-wrap { flex: 1; overflow: hidden; background: #0a0e1a; display: flex; align-items: center; justify-content: center; min-height: 200px; }
.modal-img-wrap img { max-height: 55vh; width: auto; max-width: 100%; object-fit: contain; }
.modal-actions { display: flex; gap: 10px; padding: 16px 20px; border-top: 1px solid var(--border); }
.modal-btn {
  flex: 1; padding: 12px; border-radius: 14px; font-size: 12px; font-weight: 800;
  text-transform: uppercase; letter-spacing: .8px; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 6px;
  text-decoration: none; transition: all .2s; border: none;
}
.modal-btn-fechar { background: var(--card); border: 1px solid var(--border2); color: var(--muted2); }
.modal-btn-fechar:hover { color: var(--text); }
.modal-btn-wa { background: rgba(16,185,129,.15); border: 1px solid rgba(16,185,129,.3); color: var(--green); }
.modal-btn-wa:hover { background: rgba(16,185,129,.25); }
.modal-btn-orig { background: linear-gradient(135deg, var(--sky), var(--sky2)); color: #fff; box-shadow: 0 4px 14px rgba(56,189,248,.3); }
.modal-btn-orig:hover { transform: translateY(-1px); }
</style>

<div class="rv-wrap" x-data="{ showModal: false, imgUrl: '', clienteNome: '', clienteWhatsapp: '' }">

    {{-- ── HERO ── --}}
    <div class="rv-hero">
        <div class="rv-hero-bg"></div>
        <div class="rv-hero-grid"></div>
        <div class="rv-hero-inner">
            <div>
                <div class="rv-hero-eyebrow">Painel de Gestão</div>
                <div class="rv-hero-title">Fila de <span>Validação</span></div>
                <div class="rv-hero-sub">Reservas aguardando confirmação de pagamento</div>
            </div>
            <div class="rv-hero-actions">
                <a href="{{ route('admin.pagos') }}" class="btn-confirmar-link">
                    ✅ Ver Confirmados
                </a>
                <div class="rv-badge-pendente">
                    <div class="dot"></div>
                    {{ $reservas->count() }} Pendente{{ $reservas->count() !== 1 ? 's' : '' }}
                </div>
            </div>
        </div>
    </div>

    {{-- ── STATS ── --}}
    @php
        $totalKz = $reservas->sum('total');
        $totalQtd = $reservas->sum('quantidade');
    @endphp
    <div class="rv-stats">
        <div class="rv-stat">
            <div class="rv-stat-icon">📋</div>
            <div class="rv-stat-num">{{ $reservas->count() }}</div>
            <div class="rv-stat-lbl">Reservas</div>
        </div>
        <div class="rv-stat">
            <div class="rv-stat-icon">🎟</div>
            <div class="rv-stat-num">{{ $totalQtd }}</div>
            <div class="rv-stat-lbl">Bilhetes</div>
        </div>
        <div class="rv-stat">
            <div class="rv-stat-icon">💰</div>
            <div class="rv-stat-num" style="font-size:16px;padding-top:3px;">{{ number_format($totalKz, 0, ',', '.') }}</div>
            <div class="rv-stat-lbl">Total Kz</div>
        </div>
    </div>

    {{-- ── CARDS ── --}}
    <div class="rv-grid">
        @forelse($reservas as $reserva)
        @php
            $phoneClean = preg_replace('/[^0-9]/', '', $reserva->whatsapp);
            $initials   = strtoupper(substr($reserva->nome_cliente, 0, 2));
        @endphp

        <div class="rv-card" style="animation-delay: {{ $loop->index * 0.05 }}s">

            {{-- TOP --}}
            <div class="rv-card-top">

                {{-- Avatar --}}
                <div class="rv-avatar">{{ $initials }}</div>

                {{-- Cliente --}}
                <div style="flex:1;min-width:0;">
                    <div class="rv-client-name">{{ $reserva->nome_cliente }}</div>
                    <div class="rv-client-phone">
                        {{ $reserva->whatsapp }}
                        <a href="https://wa.me/{{ $phoneClean }}?text=Olá {{ $reserva->nome_cliente }}, estamos a validar o seu pagamento para o evento {{ $reserva->tipoIngresso->evento->titulo ?? '' }}."
                           target="_blank" class="rv-wa-btn" title="Contactar via WhatsApp">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.301-.15-1.779-.879-2.053-.979-.275-.101-.476-.151-.675.15-.199.302-.771.979-.945 1.181-.174.201-.347.227-.649.076-.301-.151-1.274-.47-2.426-1.498-.897-.8-1.502-1.788-1.678-2.09-.175-.301-.019-.465.131-.614.136-.134.301-.352.451-.527.151-.176.201-.302.302-.503.101-.201.05-.377-.025-.527-.075-.151-.675-1.628-.925-2.229-.243-.584-.489-.505-.675-.514-.174-.009-.374-.011-.574-.011s-.525.076-.8.376c-.275.301-1.051 1.029-1.051 2.508 0 1.479 1.076 2.91 1.226 3.111.15.201 2.117 3.232 5.129 4.532.716.31 1.274.495 1.71.635.719.227 1.373.195 1.89.117.577-.087 1.779-.727 2.03-1.43.25-.702.25-1.303.174-1.43-.075-.127-.275-.201-.576-.351z"/>
                            </svg>
                        </a>
                    </div>
                    <div style="margin-top:8px;">
                        <div class="rv-evento-nome">{{ $reserva->tipoIngresso->evento->titulo ?? 'Evento N/D' }}</div>
                        <span class="rv-evento-tipo">{{ $reserva->tipoIngresso->nome ?? 'Tipo N/D' }}</span>
                    </div>
                </div>

                {{-- Qtd + Total --}}
                <div class="rv-amounts">
                    <div class="rv-qty">
                        <div class="rv-qty-num">{{ $reserva->quantidade }}</div>
                        <div class="rv-qty-lbl">Qtd</div>
                    </div>
                    <div style="width:1px;height:36px;background:var(--border);"></div>
                    <div class="rv-total">
                        <div class="rv-total-val">{{ number_format($reserva->total, 0, ',', '.') }}</div>
                        <div class="rv-total-lbl">Kz</div>
                    </div>
                </div>
            </div>

            {{-- BOTTOM ACTIONS --}}
            <div class="rv-card-bottom">
                {{-- Ver comprovativo --}}
                @if($reserva->comprovativo_path)
                <button class="rv-btn-comp"
                        @click="showModal = true; imgUrl = '{{ asset('storage/' . $reserva->comprovativo_path) }}'; clienteNome = '{{ $reserva->nome_cliente }}'; clienteWhatsapp = '{{ $phoneClean }}'">
                    🖼 Comprovativo
                </button>
                @else
                <span style="font-size:11px;color:var(--red);font-weight:700;">⚠️ Sem comprovativo</span>
                @endif

                {{-- WhatsApp --}}
                <a href="https://wa.me/{{ $phoneClean }}?text=Olá {{ $reserva->nome_cliente }}, estamos a analisar o seu pagamento."
                   target="_blank" class="rv-btn-wa-bottom">
                    💬 WhatsApp
                </a>

                {{-- Spacer --}}
                <div style="flex:1;"></div>

                {{-- Confirmar --}}
                <form action="{{ route('reserva.confirmar', $reserva->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="rv-btn-confirmar">
                        ✅ Confirmar
                    </button>
                </form>

                {{-- Eliminar --}}
                <form action="{{ route('reserva.eliminar', $reserva->id) }}" method="POST"
                      onsubmit="return confirm('Apagar esta reserva definitivamente?')" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="rv-btn-eliminar" title="Eliminar reserva">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="rv-empty">
            <div class="rv-empty-icon">🎉</div>
            <div class="rv-empty-title">Tudo validado!</div>
            <div class="rv-empty-sub">Nenhuma reserva pendente de validação</div>
        </div>
        @endforelse
    </div>

    {{-- ── MODAL COMPROVATIVO ── --}}
    <div x-show="showModal" x-cloak class="modal-overlay"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="modal-box" @click.away="showModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">
            <div class="modal-handle"></div>
            <div class="modal-head">
                <div>
                    <div class="modal-head-eyebrow">Comprovativo de pagamento</div>
                    <div class="modal-head-title" x-text="clienteNome"></div>
                </div>
                <button class="modal-close" @click="showModal = false">✕</button>
            </div>
            <div class="modal-img-wrap">
                <img :src="imgUrl" onerror="this.src='https://placehold.co/600x800/131c2e/38bdf8?text=Erro+ao+carregar'">
            </div>
            <div class="modal-actions">
                <button @click="showModal = false" class="modal-btn modal-btn-fechar">Fechar</button>
                <a :href="'https://wa.me/' + clienteWhatsapp + '?text=Olá ' + clienteNome + ', estou analisando seu comprovativo.'"
                   target="_blank" class="modal-btn modal-btn-wa">💬 WhatsApp</a>
                <a :href="imgUrl" target="_blank" class="modal-btn modal-btn-orig">🖼 Original</a>
            </div>
        </div>
    </div>

</div>

@endsection