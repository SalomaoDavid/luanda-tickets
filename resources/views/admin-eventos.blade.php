@extends('layouts.app')
@section('content')

<style>
:root{
    --bg:#04070f;--s1:#080d1a;--s2:#0d1526;--s3:#111e33;
    --c:#38bdf8;--c2:#0ea5e9;--green:#10b981;--red:#f43f5e;--amber:#f59e0b;--purple:#a78bfa;
    --t1:#f0f6ff;--t2:#94a3b8;--t3:#475569;
    --b1:rgba(56,189,248,.07);--b2:rgba(56,189,248,.15);--b3:rgba(56,189,248,.3);
}
*{box-sizing:border-box;margin:0;padding:0;}

.ae-wrap{max-width:1300px;margin:0 auto;padding:24px 16px 80px;}
@@media(min-width:768px){.ae-wrap{padding:32px 24px 80px;}}

/* TOPBAR */
.ae-top{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:28px;flex-wrap:wrap;}
.ae-top-left{}
.ae-top-eyebrow{font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--c);margin-bottom:6px;display:flex;align-items:center;gap:8px;}
.ae-top-eyebrow::before{content:'';width:16px;height:2px;background:var(--c);border-radius:1px;}
.ae-top-title{font-size:22px;font-weight:800;color:var(--t1);}
@@media(min-width:768px){.ae-top-title{font-size:30px;}}
.ae-top-sub{font-size:12px;color:var(--t3);margin-top:3px;}
.ae-top-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.btn-new{
    display:inline-flex;align-items:center;gap:7px;
    padding:10px 20px;border-radius:12px;
    background:linear-gradient(135deg,var(--c),var(--c2));
    color:#000;font-size:13px;font-weight:800;
    text-decoration:none;transition:all .2s;
    box-shadow:0 4px 16px rgba(56,189,248,.3);
}
.btn-new:hover{transform:translateY(-1px);box-shadow:0 6px 22px rgba(56,189,248,.4);}
.btn-ghost{
    display:inline-flex;align-items:center;gap:6px;
    padding:10px 16px;border-radius:12px;
    background:var(--b1);border:1px solid var(--b2);
    color:var(--t2);font-size:12px;font-weight:600;
    text-decoration:none;transition:all .2s;
}
.btn-ghost:hover{border-color:var(--b3);color:var(--c);}

/* STATS */
.ae-stats{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:24px;}
@@media(min-width:640px){.ae-stats{grid-template-columns:repeat(4,1fr);gap:14px;}}
.ae-stat{background:var(--s1);border:1px solid var(--b2);border-radius:16px;padding:16px 18px;position:relative;overflow:hidden;}
.ae-stat::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:var(--accent-col,var(--c));}
.ae-stat-num{font-size:26px;font-weight:900;color:var(--t1);line-height:1;margin-bottom:4px;}
.ae-stat-lbl{font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);}

/* FILTROS */
.ae-filters{display:flex;gap:8px;overflow-x:auto;scrollbar-width:none;margin-bottom:20px;padding-bottom:4px;}
.ae-filters::-webkit-scrollbar{display:none;}
.ae-filter{
    flex-shrink:0;display:flex;align-items:center;gap:5px;
    padding:7px 14px;border-radius:20px;
    background:var(--s1);border:1px solid var(--b1);
    font-size:12px;font-weight:600;color:var(--t3);
    cursor:pointer;transition:all .2s;text-decoration:none;white-space:nowrap;
}
.ae-filter:hover{border-color:var(--b2);color:var(--t2);}
.ae-filter.active{background:rgba(56,189,248,.1);border-color:var(--b3);color:var(--c);}

/* GRID DE CARDS */
.ae-grid{display:grid;grid-template-columns:1fr;gap:12px;}
@@media(min-width:640px){.ae-grid{grid-template-columns:repeat(2,1fr);gap:14px;}}
@@media(min-width:1024px){.ae-grid{grid-template-columns:repeat(3,1fr);}}

/* CARD DO EVENTO */
.ev-card{
    background:var(--s1);border:1px solid var(--b1);
    border-radius:20px;overflow:hidden;
    transition:border-color .2s,transform .2s;
    display:flex;flex-direction:column;
}
.ev-card:hover{border-color:var(--b2);transform:translateY(-2px);}

.ev-card-img{
    position:relative;height:140px;overflow:hidden;
    background:linear-gradient(135deg,#050d1a,#0c2244);
    flex-shrink:0;
}
.ev-card-img img{width:100%;height:100%;object-fit:cover;}
.ev-card-img-ph{width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:48px;}
.ev-card-img-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(8,13,26,.8),transparent 60%);}

/* status badge no canto */
.ev-status-badge{
    position:absolute;top:10px;left:10px;
    font-size:9px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;
    padding:4px 9px;border-radius:20px;
}
.ev-status-badge.publicado{background:rgba(16,185,129,.9);color:#fff;}
.ev-status-badge.rascunho{background:rgba(245,158,11,.9);color:#000;}
.ev-status-badge.encerrado{background:rgba(244,63,94,.9);color:#fff;}

/* acoes rapidas no hover */
.ev-quick-actions{
    position:absolute;top:10px;right:10px;
    display:flex;gap:5px;
    opacity:0;transition:opacity .2s;
}
.ev-card:hover .ev-quick-actions{opacity:1;}
.ev-qa-btn{
    width:30px;height:30px;border-radius:8px;
    display:flex;align-items:center;justify-content:center;
    font-size:13px;cursor:pointer;
    background:rgba(0,0,0,.6);backdrop-filter:blur(8px);
    border:1px solid rgba(255,255,255,.15);color:#fff;
    text-decoration:none;transition:all .2s;
}
.ev-qa-btn:hover{background:rgba(56,189,248,.3);border-color:var(--c);}
.ev-qa-btn.danger:hover{background:rgba(244,63,94,.3);border-color:var(--red);}

.ev-card-body{padding:14px 16px;flex:1;display:flex;flex-direction:column;gap:8px;}

.ev-card-title{
    font-size:14px;font-weight:700;color:var(--t1);
    line-height:1.3;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
}
.ev-card-meta{display:flex;flex-direction:column;gap:4px;}
.ev-card-meta-row{display:flex;align-items:center;gap:5px;font-size:11px;color:var(--t3);}

/* bilhetes */
.ev-tickets{display:flex;flex-wrap:wrap;gap:5px;margin-top:2px;}
.ev-ticket-tag{
    font-size:10px;font-weight:700;
    padding:3px 8px;border-radius:6px;
    background:rgba(56,189,248,.08);border:1px solid var(--b1);
    color:var(--c);
}

/* footer do card */
.ev-card-footer{
    display:flex;align-items:center;justify-content:space-between;
    padding:12px 16px;border-top:1px solid var(--b1);
    gap:8px;
}
.ev-card-date{font-size:11px;color:var(--t3);font-weight:500;}

/* toggle status inline */
.status-toggle{
    display:flex;align-items:center;gap:4px;
    font-size:10px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;
    padding:5px 10px;border-radius:8px;cursor:pointer;
    border:none;transition:all .2s;
}
.status-toggle.publicado{background:rgba(16,185,129,.12);color:var(--green);border:1px solid rgba(16,185,129,.25);}
.status-toggle.rascunho{background:rgba(245,158,11,.1);color:var(--amber);border:1px solid rgba(245,158,11,.2);}
.status-toggle.encerrado{background:rgba(244,63,94,.1);color:var(--red);border:1px solid rgba(244,63,94,.2);}

/* acoes footer */
.ev-footer-actions{display:flex;gap:6px;}
.ev-action-btn{
    width:32px;height:32px;border-radius:9px;
    display:flex;align-items:center;justify-content:center;
    font-size:14px;cursor:pointer;transition:all .2s;
    background:var(--b1);border:1px solid var(--b2);
    text-decoration:none;color:var(--t2);
}
.ev-action-btn:hover{border-color:var(--b3);color:var(--c);}
.ev-action-btn.danger:hover{background:rgba(244,63,94,.1);border-color:rgba(244,63,94,.3);color:var(--red);}

/* DROPDOWN STATUS */
.status-dropdown{
    position:absolute;top:calc(100% + 6px);left:0;z-index:100;
    background:var(--s2);border:1px solid var(--b2);
    border-radius:12px;overflow:hidden;min-width:150px;
    box-shadow:0 8px 24px rgba(0,0,0,.4);
}
.status-dropdown-item{
    display:flex;align-items:center;gap:8px;
    padding:10px 14px;font-size:12px;font-weight:600;
    cursor:pointer;transition:background .15s;color:var(--t2);
    border:none;background:none;width:100%;text-align:left;
}
.status-dropdown-item:hover{background:var(--b1);color:var(--t1);}
.status-dropdown-item.active{color:var(--c);}

/* EMPTY */
.ae-empty{
    grid-column:1/-1;
    text-align:center;padding:60px 20px;
    background:var(--s1);border:1px dashed var(--b2);
    border-radius:20px;
}
.ae-empty-icon{font-size:48px;margin-bottom:12px;}
.ae-empty-title{font-size:16px;font-weight:700;color:var(--t2);margin-bottom:6px;}
.ae-empty-sub{font-size:12px;color:var(--t3);}

/* MODAL CONFIRMAÇÃO */
.confirm-overlay{
    display:none;position:fixed;inset:0;z-index:9999;
    background:rgba(4,7,15,.88);backdrop-filter:blur(12px);
    align-items:center;justify-content:center;padding:20px;
}
.confirm-overlay.open{display:flex;}
.confirm-box{
    background:var(--s1);border:1px solid rgba(244,63,94,.3);
    border-radius:20px;padding:28px;max-width:400px;width:100%;
    text-align:center;
}
.confirm-icon{font-size:40px;margin-bottom:12px;}
.confirm-title{font-size:18px;font-weight:800;color:var(--t1);margin-bottom:8px;}
.confirm-sub{font-size:13px;color:var(--t3);margin-bottom:22px;line-height:1.6;}
.confirm-actions{display:flex;gap:10px;}
.confirm-btn-cancel{
    flex:1;padding:12px;border-radius:12px;
    background:var(--b1);border:1px solid var(--b2);
    color:var(--t2);font-size:13px;font-weight:700;cursor:pointer;transition:all .2s;
}
.confirm-btn-cancel:hover{border-color:var(--b3);color:var(--t1);}
.confirm-btn-delete{
    flex:1;padding:12px;border-radius:12px;
    background:rgba(244,63,94,.15);border:1px solid rgba(244,63,94,.35);
    color:var(--red);font-size:13px;font-weight:700;cursor:pointer;transition:all .2s;
}
.confirm-btn-delete:hover{background:rgba(244,63,94,.25);}
</style>

@php
$total       = $eventos->count();
$publicados  = $eventos->where('status','publicado')->count();
$rascunhos   = $eventos->where('status','rascunho')->count();
$encerrados  = $eventos->where('status','encerrado')->count();
@endphp

<div class="ae-wrap">

    {{-- TOPBAR --}}
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

    {{-- STATS --}}
    <div class="ae-stats">
        <div class="ae-stat" style="--accent-col:var(--c)">
            <div class="ae-stat-num">{{ $total }}</div>
            <div class="ae-stat-lbl">Total</div>
        </div>
        <div class="ae-stat" style="--accent-col:var(--green)">
            <div class="ae-stat-num" style="color:var(--green)">{{ $publicados }}</div>
            <div class="ae-stat-lbl">Publicados</div>
        </div>
        <div class="ae-stat" style="--accent-col:var(--amber)">
            <div class="ae-stat-num" style="color:var(--amber)">{{ $rascunhos }}</div>
            <div class="ae-stat-lbl">Rascunhos</div>
        </div>
        <div class="ae-stat" style="--accent-col:var(--red)">
            <div class="ae-stat-num" style="color:var(--red)">{{ $encerrados }}</div>
            <div class="ae-stat-lbl">Encerrados</div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="ae-filters">
        <a href="{{ route('admin.eventos') }}" class="ae-filter {{ !request('status') ? 'active' : '' }}">Todos ({{ $total }})</a>
        <a href="{{ route('admin.eventos', ['status' => 'publicado']) }}" class="ae-filter {{ request('status') === 'publicado' ? 'active' : '' }}">✅ Publicados ({{ $publicados }})</a>
        <a href="{{ route('admin.eventos', ['status' => 'rascunho']) }}" class="ae-filter {{ request('status') === 'rascunho' ? 'active' : '' }}">📝 Rascunhos ({{ $rascunhos }})</a>
        <a href="{{ route('admin.eventos', ['status' => 'encerrado']) }}" class="ae-filter {{ request('status') === 'encerrado' ? 'active' : '' }}">🔒 Encerrados ({{ $encerrados }})</a>
    </div>

    {{-- GRID --}}
    <div class="ae-grid">
        @forelse($eventos as $evento)
        @php
        $catEmoji = optional($evento->categoria)->emoji ?? '🎟';
        $vendidos = optional($evento->tiposIngresso)->sum('quantidade_vendida') ?? 0;
        $disponiveis = optional($evento->tiposIngresso)->sum('quantidade_disponivel') ?? 0;
        $precoMin = optional($evento->tiposIngresso->sortBy('preco')->first())->preco ?? 0;
        @endphp

        <div class="ev-card">

            {{-- IMAGEM --}}
            <div class="ev-card-img">
                @if($evento->imagem_capa)
                <img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="{{ e($evento->titulo) }}" loading="lazy">
                @else
                <div class="ev-card-img-ph">{{ $catEmoji }}</div>
                @endif
                <div class="ev-card-img-overlay"></div>

                {{-- Badge de status --}}
                <span class="ev-status-badge {{ $evento->status }}">
                    @if($evento->status === 'publicado') ✅ Publicado
                    @elseif($evento->status === 'rascunho') 📝 Rascunho
                    @else 🔒 Encerrado
                    @endif
                </span>

                {{-- Ações rápidas no hover --}}
                <div class="ev-quick-actions">
                    <a href="{{ route('evento.detalhes', $evento->id) }}" target="_blank" class="ev-qa-btn" title="Ver página pública">👁</a>
                    <a href="{{ route('admin.eventos.editar', $evento->id) }}" class="ev-qa-btn" title="Editar">✏️</a>
                </div>
            </div>

            {{-- CORPO --}}
            <div class="ev-card-body">
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

            {{-- FOOTER --}}
            <div class="ev-card-footer">

                {{-- Toggle de status --}}
                <div style="position:relative;">
                    <button class="status-toggle {{ $evento->status }}" onclick="toggleStatusMenu({{ $evento->id }})">
                        @if($evento->status === 'publicado') ✅ Publicado
                        @elseif($evento->status === 'rascunho') 📝 Rascunho
                        @else 🔒 Encerrado
                        @endif
                        ▾
                    </button>
                    <div class="status-dropdown" id="status-menu-{{ $evento->id }}" style="display:none;">
                        @foreach(['publicado' => '✅ Publicado', 'rascunho' => '📝 Rascunho', 'encerrado' => '🔒 Encerrado'] as $st => $label)
                        <form method="POST" action="{{ route('admin.eventos.atualizar', $evento->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="titulo" value="{{ $evento->titulo }}">
                            <input type="hidden" name="descricao" value="{{ $evento->descricao }}">
                            <input type="hidden" name="localizacao" value="{{ $evento->localizacao }}">
                            <input type="hidden" name="data_evento" value="{{ $evento->data_evento }}">
                            <input type="hidden" name="hora_inicio" value="{{ $evento->hora_inicio }}">
                            <input type="hidden" name="lotacao_maxima" value="{{ $evento->lotacao_maxima }}">
                            <input type="hidden" name="status" value="{{ $st }}">
                            <button type="submit" class="status-dropdown-item {{ $evento->status === $st ? 'active' : '' }}">
                                {{ $label }}
                            </button>
                        </form>
                        @endforeach
                    </div>
                </div>

                {{-- Ações --}}
                <div class="ev-footer-actions">
                    <a href="{{ route('admin.eventos.editar', $evento->id) }}" class="ev-action-btn" title="Editar evento">✏️</a>
                    <a href="{{ route('evento.detalhes', $evento->id) }}" target="_blank" class="ev-action-btn" title="Ver página pública">👁</a>
                    <button class="ev-action-btn danger" title="Eliminar" onclick="confirmarEliminar({{ $evento->id }}, '{{ addslashes(e($evento->titulo)) }}')">🗑</button>
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

</div>

{{-- MODAL CONFIRMAÇÃO ELIMINAR --}}
<div class="confirm-overlay" id="confirm-overlay">
    <div class="confirm-box">
        <div class="confirm-icon">⚠️</div>
        <div class="confirm-title">Eliminar evento?</div>
        <div class="confirm-sub" id="confirm-sub">Esta ação é irreversível. Todos os ingressos e reservas associados serão eliminados.</div>
        <div class="confirm-actions">
            <button class="confirm-btn-cancel" onclick="fecharConfirm()">Cancelar</button>
            <form id="confirm-form" method="POST" style="flex:1;">
                @csrf
                @method('DELETE')
                <button type="submit" class="confirm-btn-delete" style="width:100%;">🗑 Eliminar</button>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle menu de status
var activeMenu = null;
function toggleStatusMenu(id) {
    var menu = document.getElementById('status-menu-' + id);
    if (activeMenu && activeMenu !== menu) {
        activeMenu.style.display = 'none';
    }
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    activeMenu = menu.style.display === 'block' ? menu : null;
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.status-toggle') && !e.target.closest('.status-dropdown')) {
        document.querySelectorAll('.status-dropdown').forEach(function(m) {
            m.style.display = 'none';
        });
        activeMenu = null;
    }
});

// Modal de confirmação de eliminar
function confirmarEliminar(id, titulo) {
    var overlay = document.getElementById('confirm-overlay');
    var sub = document.getElementById('confirm-sub');
    var form = document.getElementById('confirm-form');
    sub.textContent = 'Tens a certeza que queres eliminar "' + titulo + '"? Esta ação é irreversível.';
    form.action = '/admin/eventos/' + id + '/eliminar';
    overlay.classList.add('open');
}

function fecharConfirm() {
    document.getElementById('confirm-overlay').classList.remove('open');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') fecharConfirm();
});
</script>

@endsection