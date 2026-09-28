@extends('layouts.app')
@section('content')
<style>
:root{
    --bg:#04070f;--s1:#080d1a;--s2:#0d1526;--s3:#111e33;
    --c:#38bdf8;--c2:#0ea5e9;--green:#10b981;--red:#f43f5e;
    --amber:#f59e0b;--purple:#a78bfa;
    --t1:#f0f6ff;--t2:#94a3b8;--t3:#475569;
    --b1:rgba(56,189,248,.07);--b2:rgba(56,189,248,.15);--b3:rgba(56,189,248,.3);
}
*{box-sizing:border-box;margin:0;padding:0;}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
@keyframes badgePulse{0%,100%{box-shadow:0 0 0 rgba(244,63,94,0);transform:scale(1);}50%{box-shadow:0 0 10px rgba(244,63,94,.5);transform:scale(1.04);}}

.ae-wrap{max-width:1300px;margin:0 auto;padding:12px 0 60px;}
@media(min-width:768px){.ae-wrap{padding:32px 24px 80px;}}

.ae-top{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px;flex-wrap:wrap;padding:0 8px;}
.ae-top-eyebrow{font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--c);margin-bottom:6px;display:flex;align-items:center;gap:8px;}
.ae-top-eyebrow::before{content:'';width:16px;height:2px;background:var(--c);border-radius:1px;}
.ae-top-title{font-size:22px;font-weight:800;color:var(--t1);}
@media(min-width:768px){.ae-top-title{font-size:30px;}}
.ae-top-sub{font-size:12px;color:var(--t3);margin-top:3px;}
.ae-top-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.btn-new{display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:12px;background:linear-gradient(135deg,var(--c),var(--c2));color:#000;font-size:13px;font-weight:800;text-decoration:none;transition:all .2s;box-shadow:0 4px 16px rgba(56,189,248,.3);}
.btn-new:hover{transform:translateY(-1px);box-shadow:0 6px 22px rgba(56,189,248,.4);}
.btn-ghost{display:inline-flex;align-items:center;gap:6px;padding:10px 16px;border-radius:12px;background:var(--b1);border:1px solid var(--b2);color:var(--t2);font-size:12px;font-weight:600;text-decoration:none;transition:all .2s;}
.btn-ghost:hover{border-color:var(--b3);color:var(--c);}

.ae-stats{display:grid;grid-template-columns:repeat(2,1fr);gap:6px;margin-bottom:12px;}
@media(min-width:640px){.ae-stats{grid-template-columns:repeat(4,1fr);gap:14px;}}
.ae-stat{background:var(--s1);border:1px solid var(--b2);border-radius:16px;padding:10px 12px;position:relative;overflow:hidden;}
.ae-stat::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--accent-col,var(--c));}
.ae-stat-num{font-size:26px;font-weight:900;color:var(--t1);line-height:1;margin-bottom:4px;}
.ae-stat-lbl{font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);}

/* Pesquisa */
.ae-search{display:flex;align-items:center;gap:8px;background:var(--s1);border:1px solid var(--b2);border-radius:12px;padding:9px 14px;margin:0 8px 10px;}
.ae-search input{flex:1;background:none;border:none;outline:none;color:var(--t1);font-size:13px;}
.ae-search input::placeholder{color:var(--t3);}
.ae-empty-filtered{grid-column:1/-1;display:none;text-align:center;padding:40px 20px;background:var(--s1);border:1px dashed var(--b2);border-radius:16px;font-size:12px;color:var(--t2);}
.ae-empty-filtered.show{display:block;}

.ae-filters{display:flex;gap:6px;overflow-x:auto;scrollbar-width:none;margin-bottom:10px;padding:0 8px 4px;}
.ae-filters::-webkit-scrollbar{display:none;}
.ae-filter{flex-shrink:0;display:flex;align-items:center;gap:5px;padding:7px 14px;border-radius:20px;background:var(--s1);border:1px solid var(--b1);font-size:12px;font-weight:600;color:var(--t3);cursor:pointer;transition:all .2s;text-decoration:none;white-space:nowrap;}
.ae-filter:hover{border-color:var(--b2);color:var(--t2);}
.ae-filter.active{background:rgba(56,189,248,.1);border-color:var(--b3);color:var(--c);}

.ae-grid{display:grid;grid-template-columns:1fr;gap:6px;padding:0 8px;}
@media(min-width:640px){.ae-grid{grid-template-columns:repeat(2,1fr);gap:14px;padding:0;}}
@media(min-width:1024px){.ae-grid{grid-template-columns:repeat(3,1fr);}}

/* Card — agora com faixa de cor por categoria e entrada suave */
.ev-card{background:var(--s1);border:1px solid var(--b1);border-radius:14px;transition:border-color .2s,transform .2s;display:flex;flex-direction:column;position:relative;overflow:hidden;
    opacity:0;transform:translateY(24px);transition:opacity .6s cubic-bezier(.16,1,.3,1),transform .6s cubic-bezier(.16,1,.3,1),border-color .2s;}
.ev-card.is-visible{opacity:1;transform:translateY(0);}
.ev-card:hover{border-color:var(--b2);transform:translateY(-2px);}
.ev-card.is-visible:hover{transform:translateY(-4px);}
.ev-card.cat-musica{border-top:3px solid var(--c);}
.ev-card.cat-festa{border-top:3px solid var(--purple);}
.ev-card.cat-desporto{border-top:3px solid var(--green);}
.ev-card.cat-arte{border-top:3px solid #f472b6;}
.ev-card.cat-gastro{border-top:3px solid var(--amber);}
.ev-card.cat-outro{border-top:3px solid var(--t3);}

.ev-card-img{position:relative;height:130px;overflow:hidden;background:linear-gradient(135deg,#050d1a,#0c2244);flex-shrink:0;}
.ev-card-img img{width:100%;height:100%;object-fit:cover;}
.ev-card-img-ph{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:48px;}
.ev-card-img-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(8,13,26,.8),transparent 60%);}
.ev-status-badge{position:absolute;top:10px;left:10px;font-size:9px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;padding:4px 9px;border-radius:20px;}
.ev-status-badge.publicado{background:rgba(16,185,129,.9);color:#fff;}
.ev-status-badge.rascunho{background:rgba(245,158,11,.9);color:#000;}
.ev-status-badge.encerrado{background:rgba(244,63,94,.9);color:#fff;}

/* Selos de urgência/proximidade — novos */
.ev-urgent-badge{position:absolute;bottom:8px;left:10px;font-size:9px;font-weight:800;letter-spacing:.5px;text-transform:uppercase;padding:4px 9px;border-radius:20px;background:rgba(244,63,94,.92);color:#fff;animation:badgePulse 1.8s ease-in-out infinite;}
.ev-soon-badge{position:absolute;bottom:8px;left:10px;font-size:9px;font-weight:800;letter-spacing:.5px;text-transform:uppercase;padding:4px 9px;border-radius:20px;background:rgba(56,189,248,.9);color:#001018;}

.ev-quick-actions{position:absolute;top:10px;right:10px;display:flex;gap:5px;opacity:0;transition:opacity .2s;}
.ev-card:hover .ev-quick-actions{opacity:1;}
.ev-qa-btn{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:13px;cursor:pointer;background:rgba(0,0,0,.6);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.15);color:#fff;text-decoration:none;transition:all .2s;}
.ev-qa-btn:hover{background:rgba(56,189,248,.3);border-color:var(--c);}
.ev-qa-btn.danger:hover{background:rgba(244,63,94,.3);border-color:var(--red);}

.ev-card-body{padding:10px 12px;flex:1;display:flex;flex-direction:column;gap:6px;}
.ev-card-cat-lbl{font-size:9px;font-weight:800;letter-spacing:.6px;text-transform:uppercase;}
.ev-card-title{font-size:14px;font-weight:700;color:var(--t1);line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.ev-card-meta{display:flex;flex-direction:column;gap:4px;}
.ev-card-meta-row{display:flex;align-items:center;gap:5px;font-size:11px;color:var(--t3);}
.ev-tickets{display:flex;flex-wrap:wrap;gap:5px;margin-top:2px;}
.ev-ticket-tag{font-size:10px;font-weight:700;padding:3px 8px;border-radius:6px;background:rgba(56,189,248,.08);border:1px solid var(--b1);color:var(--c);}
.ev-card-footer{display:flex;align-items:center;justify-content:space-between;padding:8px 12px;border-top:1px solid var(--b1);gap:8px;}
.status-wrap{position:relative;}
.status-toggle{display:flex;align-items:center;gap:4px;font-size:10px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;padding:5px 10px;border-radius:8px;cursor:pointer;border:none;transition:all .2s;}
.status-toggle.publicado{background:rgba(16,185,129,.12);color:var(--green);border:1px solid rgba(16,185,129,.25);}
.status-toggle.rascunho{background:rgba(245,158,11,.1);color:var(--amber);border:1px solid rgba(245,158,11,.2);}
.status-toggle.encerrado{background:rgba(244,63,94,.1);color:var(--red);border:1px solid rgba(244,63,94,.2);}

/* Dropdown teleportado para o body via JS — sem problemas de stacking context */
.status-dropdown{position:fixed;z-index:99999;background:var(--s2);border:1px solid var(--b2);border-radius:12px;overflow:hidden;min-width:160px;box-shadow:0 8px 32px rgba(0,0,0,.7);display:none;}
.status-dropdown-item{display:flex;align-items:center;gap:8px;padding:10px 14px;font-size:12px;font-weight:600;cursor:pointer;transition:background .15s;color:var(--t2);border:none;background:none;width:100%;text-align:left;}
.status-dropdown-item:hover{background:var(--b1);color:var(--t1);}
.status-dropdown-item.active{color:var(--c);}

.ev-footer-actions{display:flex;gap:6px;}
.ev-action-btn{width:32px;height:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:14px;cursor:pointer;transition:all .2s;background:var(--b1);border:1px solid var(--b2);text-decoration:none;color:var(--t2);}
.ev-action-btn:hover{border-color:var(--b3);color:var(--c);}
.ev-action-btn.danger:hover{background:rgba(244,63,94,.1);border-color:rgba(244,63,94,.3);color:var(--red);}

.ae-empty{grid-column:1/-1;text-align:center;padding:60px 20px;background:var(--s1);border:1px dashed var(--b2);border-radius:20px;}
.ae-empty-icon{font-size:48px;margin-bottom:12px;}
.ae-empty-title{font-size:16px;font-weight:700;color:var(--t2);margin-bottom:6px;}
.ae-empty-sub{font-size:12px;color:var(--t3);}

@media (prefers-reduced-motion: reduce){
    .ev-card,.ev-urgent-badge{animation:none!important;opacity:1!important;transform:none!important;}
}
</style>
@php
$total       = $eventos->count();
$publicados  = $eventos->where('status','publicado')->count();
$rascunhos   = $eventos->where('status','rascunho')->count();
$encerrados  = $eventos->where('status','encerrado')->count();
@endphp
<div class="ae-wrap">
    <div class="ae-top">
        <div class="ae-top-left">
            <div class="ae-top-eyebrow">Painel Admin</div>
            <div class="ae-top-title">Gestão de Eventos</div>
            <div class="ae-top-sub">{{ $total }} evento(s) no sistema</div>
        </div>
        <div class="ae-top-actions">
            <a href="{{ route('admin.reservas') }}" class="btn-ghost">📋 Reservas</a>
            <a href="{{ route('admin.pagos') }}" class="btn-ghost">💰 Pagamentos</a>
            <a href="{{ route('admin.eventos.criar') }}" class="btn-new">+ Novo Evento</a>
        </div>
    </div>

    <div class="ae-stats">
        <div class="ae-stat" style="--accent-col:var(--c)"><div class="ae-stat-num">{{ $total }}</div><div class="ae-stat-lbl">Total</div></div>
        <div class="ae-stat" style="--accent-col:var(--green)"><div class="ae-stat-num" style="color:var(--green)">{{ $publicados }}</div><div class="ae-stat-lbl">Publicados</div></div>
        <div class="ae-stat" style="--accent-col:var(--amber)"><div class="ae-stat-num" style="color:var(--amber)">{{ $rascunhos }}</div><div class="ae-stat-lbl">Rascunhos</div></div>
        <div class="ae-stat" style="--accent-col:var(--red)"><div class="ae-stat-num" style="color:var(--red)">{{ $encerrados }}</div><div class="ae-stat-lbl">Encerrados</div></div>
    </div>

    {{-- Pesquisa por nome (filtra só a página atual carregada) --}}
    <div class="ae-search">
        🔍 <input type="text" id="eventoSearch" placeholder="Buscar por nome do evento..." oninput="filtrarEventosAdmin()">
    </div>

    <div class="ae-filters">
        <a href="{{ route('admin.eventos') }}" class="ae-filter {{ !request('status') ? 'active' : '' }}">Todos ({{ $total }})</a>
        <a href="{{ route('admin.eventos', ['status' => 'publicado']) }}" class="ae-filter {{ request('status') === 'publicado' ? 'active' : '' }}">✅ Publicados ({{ $publicados }})</a>
        <a href="{{ route('admin.eventos', ['status' => 'rascunho']) }}" class="ae-filter {{ request('status') === 'rascunho' ? 'active' : '' }}">📝 Rascunhos ({{ $rascunhos }})</a>
        <a href="{{ route('admin.eventos', ['status' => 'encerrado']) }}" class="ae-filter {{ request('status') === 'encerrado' ? 'active' : '' }}">🔒 Encerrados ({{ $encerrados }})</a>
    </div>

    <div class="ae-grid" id="eventosGrid">
        @forelse($eventos as $evento)
        @php
            $catNome2 = strtolower(optional($evento->categoria)->nome ?? '');
            if (str_contains($catNome2,'música') || str_contains($catNome2,'musica') || str_contains($catNome2,'show')) { $catEmoji = '🎵'; $catClass = 'cat-musica'; }
            elseif (str_contains($catNome2,'festa') || str_contains($catNome2,'festival')) { $catEmoji = '🎉'; $catClass = 'cat-festa'; }
            elseif (str_contains($catNome2,'desporto')) { $catEmoji = '⚽'; $catClass = 'cat-desporto'; }
            elseif (str_contains($catNome2,'arte') || str_contains($catNome2,'cultura')) { $catEmoji = '🎨'; $catClass = 'cat-arte'; }
            elseif (str_contains($catNome2,'gastro') || str_contains($catNome2,'comida')) { $catEmoji = '🍽'; $catClass = 'cat-gastro'; }
            elseif (str_contains($catNome2,'negócio') || str_contains($catNome2,'negocio')) { $catEmoji = '💼'; $catClass = 'cat-outro'; }
            elseif (str_contains($catNome2,'viagem')) { $catEmoji = '✈️'; $catClass = 'cat-outro'; }
            elseif (str_contains($catNome2,'confer') || str_contains($catNome2,'workshop')) { $catEmoji = '🎙'; $catClass = 'cat-outro'; }
            else { $catEmoji = '🎟'; $catClass = 'cat-outro'; }

            $disponiveis = optional($evento->tiposIngresso)->sum('quantidade_disponivel') ?? 0;
            $totalGeral  = optional($evento->tiposIngresso)->sum('quantidade_total') ?? 0;
            $ehUrgente   = $totalGeral > 0 && ($disponiveis / $totalGeral) <= 0.15 && $evento->status === 'publicado';

            $diasParaComecar = null;
            if ($evento->status === 'publicado') {
                $dataEv = \Carbon\Carbon::parse($evento->data_evento);
                if ($dataEv->isFuture() && $dataEv->diffInDays(now()) <= 3) {
                    $diasParaComecar = max(0, $dataEv->diffInDays(now()));
                }
            }
        @endphp
        <div class="ev-card {{ $catClass }} scroll-reveal" data-nome="{{ strtolower($evento->titulo) }}">
            <div class="ev-card-img">
                @if($evento->imagem_capa)
                <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ e($evento->titulo) }}" loading="lazy">
                @else
                <div class="ev-card-img-ph">{{ $catEmoji }}</div>
                @endif
                <div class="ev-card-img-overlay"></div>
                <span class="ev-status-badge {{ $evento->status }}" id="status-badge-{{ $evento->id }}">
                    @if($evento->status === 'publicado') ✅ Publicado
                    @elseif($evento->status === 'rascunho') 📝 Rascunho
                    @else 🔒 Encerrado
                    @endif
                </span>

                {{-- Selos novos: urgência de lotação OU proximidade da data (nunca os dois ao mesmo tempo) --}}
                @if($ehUrgente)
                <span class="ev-urgent-badge">🔥 Últimos {{ $disponiveis }} bilhetes</span>
                @elseif($diasParaComecar !== null)
                <span class="ev-soon-badge">{{ $diasParaComecar === 0 ? '📅 É hoje' : '📅 Daqui a ' . $diasParaComecar . 'd' }}</span>
                @endif

                <div class="ev-quick-actions">
                    <a href="{{ route('evento.detalhes', $evento->id) }}" target="_blank" class="ev-qa-btn" title="Ver">👁</a>
                    <a href="{{ route('admin.eventos.editar', $evento->id) }}" class="ev-qa-btn" title="Editar">✏️</a>
                </div>
            </div>
            <div class="ev-card-body">
                @if($evento->categoria)
                <div class="ev-card-cat-lbl" style="color:var(--c);">{{ $catEmoji }} {{ $evento->categoria->nome }}</div>
                @endif
                <div class="ev-card-title">{{ e($evento->titulo) }}</div>
                <div class="ev-card-meta">
                    <div class="ev-card-meta-row">📅 {{ \Carbon\Carbon::parse($evento->data_evento)->format('d/m/Y') }}@if($evento->hora_inicio) · {{ substr($evento->hora_inicio,0,5) }}@endif</div>
                    <div class="ev-card-meta-row">📍 {{ Str::limit($evento->localizacao,30) }}</div>
                    @if($evento->lotacao_maxima)
                    <div class="ev-card-meta-row">👥 {{ number_format($disponiveis) }} disponíveis de {{ number_format($evento->lotacao_maxima) }}</div>
                    @endif
                </div>
                @if($evento->tiposIngresso->count() > 0)
                <div class="ev-tickets">
                    @foreach($evento->tiposIngresso as $tipo)
                    <span class="ev-ticket-tag">{{ e($tipo->nome) }}: {{ number_format($tipo->preco,0,',','.') }} Kz</span>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="ev-card-footer">
                <div class="status-wrap">
                    <button class="status-toggle {{ $evento->status }}" id="status-toggle-{{ $evento->id }}"
                            onclick="toggleStatusMenu(event, {{ $evento->id }})">
                        @if($evento->status === 'publicado') ✅ Publicado
                        @elseif($evento->status === 'rascunho') 📝 Rascunho
                        @else 🔒 Encerrado
                        @endif ▾
                    </button>
                    {{-- Dropdown permanece no DOM aqui mas será teleportado para o body via JS --}}
                    <div class="status-dropdown" id="status-menu-{{ $evento->id }}">
                        @foreach(['publicado' => '✅ Publicado', 'rascunho' => '📝 Rascunho', 'encerrado' => '🔒 Encerrado'] as $st => $label)
                        <button type="button"
                                class="status-dropdown-item {{ $evento->status === $st ? 'active' : '' }}"
                                onclick="mudarStatusEvento({{ $evento->id }}, '{{ $st }}', this)">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>
                <div class="ev-footer-actions">
                    <a href="{{ route('admin.eventos.editar', $evento->id) }}" class="ev-action-btn" title="Editar">✏️</a>
                    <a href="{{ route('evento.detalhes', $evento->id) }}" target="_blank" class="ev-action-btn" title="Ver">👁</a>
                    <button class="ev-action-btn danger" onclick="confirmarEliminar({{ $evento->id }}, '{{ addslashes(e($evento->titulo)) }}')">🗑</button>
                </div>
            </div>
        </div>
        @empty
        <div class="ae-empty">
            <div class="ae-empty-icon">🎭</div>
            <div class="ae-empty-title">Nenhum evento encontrado</div>
            <div class="ae-empty-sub">Cria o teu primeiro evento para começar</div>
        </div>
        @endforelse
    </div>

    {{-- Estado vazio ao filtrar por pesquisa (distinto de "sem eventos nenhuns") --}}
    <div class="ae-empty-filtered" id="eventoEmptyFiltered">🔍 Nenhum evento encontrado com esse nome nesta página.</div>
</div>

{{-- Form partilhado para eliminar — a acção é definida dinamicamente pelo confirmarEliminar() --}}
<form id="form-eliminar-evento" method="POST" style="display:none;">
    @csrf @method('DELETE')
</form>
<script>
// ── DROPDOWN TELEPORTADO PARA O BODY ─────────────────────────
// Resolve o problema de ficar atrás de outros cards (stacking context do transform)
var _dropdownAtivo = null;
function toggleStatusMenu(e, id) {
    e.stopPropagation();
    var btn = e.currentTarget;
    var menu = document.getElementById('status-menu-' + id);
    if (_dropdownAtivo === menu) {
        fecharDropdown();
        return;
    }
    fecharDropdown();
    document.body.appendChild(menu);
    var rect = btn.getBoundingClientRect();
    menu.style.top = (rect.bottom + 6) + 'px';
    menu.style.left = rect.left + 'px';
    menu.style.display = 'block';
    _dropdownAtivo = menu;
}
function fecharDropdown() {
    if (_dropdownAtivo) {
        _dropdownAtivo.style.display = 'none';
        _dropdownAtivo = null;
    }
}
document.addEventListener('click', fecharDropdown);
window.addEventListener('scroll', fecharDropdown, true);

// ── MUDAR STATUS — via AJAX, sem recarregar a página ──
async function mudarStatusEvento(eventoId, novoStatus, btnEl) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    const labels = { publicado: '✅ Publicado', rascunho: '📝 Rascunho', encerrado: '🔒 Encerrado' };
    try {
        const res = await fetch(`/admin/eventos/${eventoId}/status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ status: novoStatus }),
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
            swalToast.fire({ icon: 'error', title: data.message || 'Erro ao atualizar o estado.' });
            return;
        }
        const toggleBtn = document.getElementById('status-toggle-' + eventoId);
        if (toggleBtn) {
            toggleBtn.classList.remove('publicado', 'rascunho', 'encerrado');
            toggleBtn.classList.add(novoStatus);
            toggleBtn.innerHTML = labels[novoStatus] + ' ▾';
        }
        const badge = document.getElementById('status-badge-' + eventoId);
        if (badge) {
            badge.classList.remove('publicado', 'rascunho', 'encerrado');
            badge.classList.add(novoStatus);
            badge.innerHTML = labels[novoStatus];
        }
        document.querySelectorAll('#status-menu-' + eventoId + ' .status-dropdown-item').forEach(function (item) {
            item.classList.remove('active');
        });
        if (btnEl) btnEl.classList.add('active');
        fecharDropdown();
        swalToast.fire({ icon: 'success', title: data.message || 'Estado atualizado!' });
    } catch (e) {
        swalToast.fire({ icon: 'error', title: 'Erro de conexão. Tenta novamente.' });
    }
}

// ── ELIMINAR — usa o alerta personalizado global (confirmarAcao, definido em layouts/app.blade.php) ──
function confirmarEliminar(id, titulo) {
    var form = document.getElementById('form-eliminar-evento');
    form.action = '/admin/eventos/' + id + '/eliminar';
    confirmarAcao(
        'Eliminar evento?',
        'Tens a certeza que queres eliminar "' + titulo + '"? Esta ação é irreversível.',
        function () { form.submit(); }
    );
}

// ── Pesquisa por nome (filtra só a página atual carregada) ──
function filtrarEventosAdmin() {
    const q = document.getElementById('eventoSearch').value.toLowerCase();
    let visiveis = 0;
    document.querySelectorAll('#eventosGrid .ev-card').forEach(function (card) {
        const visivel = card.dataset.nome.includes(q);
        card.style.display = visivel ? '' : 'none';
        if (visivel) visiveis++;
    });
    document.getElementById('eventoEmptyFiltered').classList.toggle('show', visiveis === 0 && q.length > 0);
}

// ── Entrada suave dos cards ao carregar (scroll-reveal) ──
document.addEventListener('DOMContentLoaded', function () {
    var cards = document.querySelectorAll('.scroll-reveal');
    if (!('IntersectionObserver' in window)) {
        cards.forEach(function (c) { c.classList.add('is-visible'); });
        return;
    }
    var obs = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                obs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    cards.forEach(function (c) { obs.observe(c); });
});
</script>
@endsection