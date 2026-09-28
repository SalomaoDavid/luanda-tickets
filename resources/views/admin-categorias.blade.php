@extends('layouts.app')

@section('content')

<style>
[x-cloak] { display: none !important; }

:root {
  --bg:      #05080f;
  --s1:      #0a0f1e;
  --s2:      #0f1628;
  --s3:      #141d35;
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

@keyframes fadeUp  { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }

.adm-wrap { max-width: 900px; margin: 0 auto; padding: 16px 16px 80px; }
@media(min-width:768px){ .adm-wrap { padding: 24px 16px 80px; } }

/* ── HERO ── */
.adm-hero {
  position: relative; overflow: hidden;
  background: var(--s1); border: 1px solid var(--border2);
  border-radius: 20px; padding: 22px 20px 18px;
  margin-bottom: 16px; animation: fadeUp .4s ease both;
}
@media(min-width:768px){ .adm-hero { border-radius: 24px; padding: 28px 28px 22px; margin-bottom: 20px; } }
.adm-hero-bg {
  position: absolute; inset: 0; pointer-events: none;
  background:
    radial-gradient(ellipse at 80% 40%, rgba(56,189,248,.1) 0%, transparent 55%),
    radial-gradient(ellipse at 10% 80%, rgba(167,139,250,.06) 0%, transparent 40%);
}
.adm-hero-grid {
  position: absolute; inset: 0; opacity: .025;
  background-image: linear-gradient(rgba(56,189,248,1) 1px,transparent 1px),linear-gradient(90deg,rgba(56,189,248,1) 1px,transparent 1px);
  background-size: 28px 28px;
}
.adm-hero-inner { position: relative; z-index: 1; display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
.adm-eyebrow { font-size: 10px; font-weight: 700; letter-spacing: 2.5px; text-transform: uppercase; color: var(--sky); margin-bottom: 5px; display: flex; align-items: center; gap: 7px; }
.adm-eyebrow::before { content:''; width:16px; height:2px; background:var(--sky); border-radius:1px; }
.adm-title { font-size: 22px; font-weight: 900; color: #fff; letter-spacing: -1px; line-height: 1.1; margin-bottom: 4px; }
@media(min-width:768px){ .adm-title { font-size: 32px; } }
.adm-title span { color: var(--sky); }
.adm-sub { font-size: 12px; color: var(--muted2); }

.adm-hero-actions { display: flex; gap: 8px; flex-shrink: 0; }
.adm-btn {
  display: flex; align-items: center; gap: 6px; padding: 10px 16px; border-radius: 12px;
  font-size: 12px; font-weight: 700; border: none; cursor: pointer; text-decoration: none;
  white-space: nowrap; transition: all .2s;
}
.adm-btn.sky { background: rgba(56,189,248,.14); border: 1px solid rgba(56,189,248,.3); color: var(--sky); }
.adm-btn.sky:hover { background: rgba(56,189,248,.24); }

/* ── FLASH ── */
.adm-flash { border-radius: 14px; padding: 12px 16px; margin-bottom: 14px; font-size: 13px; font-weight: 600; animation: fadeUp .3s ease both; }
.adm-flash.ok  { background: rgba(16,185,129,.1);  border: 1px solid rgba(16,185,129,.3);  color: var(--green); }
.adm-flash.err { background: rgba(244,63,94,.1);   border: 1px solid rgba(244,63,94,.3);   color: var(--red); }

/* ── TOOLBAR / FILTRO TIPO ── */
.adm-filters { display: flex; gap: 6px; margin-bottom: 16px; }
.adm-filter-btn {
  flex-shrink: 0; display: flex; align-items: center; gap: 5px;
  padding: 8px 14px; border-radius: 10px; font-size: 11px; font-weight: 700;
  border: 1px solid var(--border); background: var(--s1); color: var(--muted2);
  cursor: pointer; transition: all .2s; white-space: nowrap; text-decoration: none;
}
.adm-filter-btn.active { border-color: var(--sky); color: var(--sky); background: rgba(56,189,248,.1); }

/* ── CATEGORIA CARD ── */
.adm-grid { display: flex; flex-direction: column; gap: 10px; }
.cat-card {
  background: var(--s2); border: 1px solid var(--border);
  border-radius: 16px; overflow: hidden; transition: border-color .2s;
  animation: fadeUp .35s ease both;
}
.cat-card.inativa { opacity: .55; }
.cat-card:hover { border-color: var(--border2); }

.cat-card-main { display: flex; align-items: center; gap: 12px; padding: 14px 16px; cursor: pointer; }
.cat-emoji-box {
  width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0;
  background: var(--s3); border: 1.5px solid var(--border2);
  display: flex; align-items: center; justify-content: center; font-size: 19px;
}
.cat-info { flex: 1; min-width: 0; }
.cat-nome { font-size: 14px; font-weight: 800; color: #fff; margin-bottom: 2px; display:flex; align-items:center; gap:8px; }
.cat-meta { font-size: 10.5px; color: var(--muted); display: flex; gap: 10px; flex-wrap: wrap; }

.tag-inativa {
  font-size: 8px; font-weight: 900; letter-spacing: 1px; text-transform: uppercase;
  color: var(--red); border: 1px solid rgba(244,63,94,.3); border-radius: 6px; padding: 1px 6px;
}

.cat-quick { display: flex; gap: 5px; align-items: center; flex-shrink: 0; }
.cat-act-btn {
  width: 32px; height: 32px; border-radius: 9px; display: flex; align-items: center; justify-content: center;
  border: 1px solid var(--border); background: var(--s3); color: var(--muted2);
  cursor: pointer; transition: all .2s; font-size: 13px;
}
.cat-act-btn:hover { border-color: var(--border2); color: var(--text); }

.cat-expand { display: none; border-top: 1px solid var(--border); padding: 16px; animation: fadeUp .25s ease; }
.cat-expand.open { display: block; }

/* form inline de editar */
.f-row { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; }
.f-input, .f-select {
  background: var(--s3); border: 1px solid var(--border2); border-radius: 10px;
  padding: 9px 12px; color: var(--text); font-size: 12.5px; outline: none; transition: border-color .2s;
  flex: 1; min-width: 140px;
}
.f-input:focus, .f-select:focus { border-color: var(--sky); }
.f-select option { background: var(--s2); }

.f-btn {
  padding: 9px 14px; border-radius: 10px; font-size: 11.5px; font-weight: 700; border: none; cursor: pointer;
  transition: all .2s; white-space: nowrap;
}
.f-btn.sky   { background: rgba(56,189,248,.14); border: 1px solid rgba(56,189,248,.3); color: var(--sky); }
.f-btn.sky:hover { background: rgba(56,189,248,.24); }
.f-btn.gold  { background: rgba(245,158,11,.1); border: 1px solid rgba(245,158,11,.3); color: var(--gold); }
.f-btn.gold:hover { background: rgba(245,158,11,.2); }
.f-btn.red   { background: rgba(244,63,94,.1);  border: 1px solid rgba(244,63,94,.25); color: var(--red); }
.f-btn.red:hover { background: rgba(244,63,94,.2); }
.f-btn.outline { background: var(--s3); border: 1px solid var(--border2); color: var(--muted2); }
.f-btn.outline:hover { color: var(--text); border-color: var(--sky); }

/* ── SUBCATEGORIAS ── */
.sub-title { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px; color: var(--muted); margin: 14px 0 8px; }
.sub-list { display: flex; flex-direction: column; gap: 6px; margin-bottom: 10px; }
.sub-row {
  display: flex; align-items: center; gap: 8px; background: var(--s3);
  border: 1px solid var(--border); border-radius: 10px; padding: 8px 10px;
}
.sub-row.inativa { opacity: .5; }
.sub-nome { flex: 1; font-size: 12px; font-weight: 700; color: var(--text); }
.sub-nome small { color: var(--muted); font-weight: 500; }
.sub-mini-btn {
  width: 26px; height: 26px; border-radius: 7px; border: 1px solid var(--border); background: var(--s2);
  color: var(--muted2); font-size: 11px; cursor: pointer; display:flex; align-items:center; justify-content:center;
}
.sub-mini-btn:hover { color: var(--text); border-color: var(--border2); }

/* ── EMPTY ── */
.adm-empty { background: var(--s1); border: 1px dashed var(--border2); border-radius: 20px; padding: 60px 20px; text-align: center; }
.adm-empty-icon { font-size: 40px; margin-bottom: 10px; }
.adm-empty-txt { font-size: 14px; color: var(--muted2); font-weight: 700; }

/* ── MODAL CRIAR CATEGORIA ── */
.modal-overlay {
  position: fixed; inset: 0; z-index: 9999; background: rgba(2,6,20,.75);
  display: flex; align-items: center; justify-content: center; padding: 16px;
}
.modal-box {
  background: var(--s2); border: 1px solid var(--border2); border-radius: 18px;
  padding: 20px; width: 100%; max-width: 380px; animation: fadeUp .25s ease;
}
.modal-title { font-size: 15px; font-weight: 800; color: #fff; margin-bottom: 14px; }
</style>

<div class="adm-wrap" x-data="{ modalCriar: false }">

    {{-- ── HERO ── --}}
    <div class="adm-hero">
        <div class="adm-hero-bg"></div>
        <div class="adm-hero-grid"></div>
        <div class="adm-hero-inner">
            <div>
                <div class="adm-eyebrow">Painel de Administração</div>
                <div class="adm-title">Gestão de <span>Categorias</span></div>
                <div class="adm-sub">Cria, edita, ativa/desativa e organiza categorias e subcategorias</div>
            </div>
            <div class="adm-hero-actions">
                <button class="adm-btn sky" @click="modalCriar = true">＋ Nova Categoria</button>
            </div>
        </div>
    </div>

    {{-- ── FLASH ── --}}
    @if(session('sucesso'))
        <div class="adm-flash ok">✅ {{ session('sucesso') }}</div>
    @endif
    @if(session('erro'))
        <div class="adm-flash err">⚠️ {{ session('erro') }}</div>
    @endif
    @if($errors->any())
        <div class="adm-flash err">⚠️ {{ $errors->first() }}</div>
    @endif

    {{-- ── FILTRO POR TIPO ── --}}
    <div class="adm-filters">
        <a href="{{ route('admin.categorias.index', ['tipo' => 'evento']) }}"
           class="adm-filter-btn {{ $tipo === 'evento' ? 'active' : '' }}">🎟 Eventos</a>
        <a href="{{ route('admin.categorias.index', ['tipo' => 'noticia']) }}"
           class="adm-filter-btn {{ $tipo === 'noticia' ? 'active' : '' }}">📰 Notícias</a>
    </div>

    {{-- ── LISTA ── --}}
    <div class="adm-grid">
        @forelse($categorias as $categoria)
            @php $inativa = !$categoria->ativo; @endphp
            <div class="cat-card {{ $inativa ? 'inativa' : '' }}">
                <div class="cat-card-main" onclick="toggleExpandCat({{ $categoria->id }})">
                    <div class="cat-emoji-box">{{ $categoria->emoji }}</div>
                    <div class="cat-info">
                        <div class="cat-nome">
                            {{ $categoria->nome }}
                            @if($inativa)<span class="tag-inativa">Inativa</span>@endif
                        </div>
                        <div class="cat-meta">
                            <span>🎟 {{ $categoria->eventos_count }} evento{{ $categoria->eventos_count !== 1 ? 's' : '' }}</span>
                            <span>🏷 {{ $categoria->subcategorias->count() }} subcategoria{{ $categoria->subcategorias->count() !== 1 ? 's' : '' }}</span>
                        </div>
                    </div>
                    <div class="cat-quick" onclick="event.stopPropagation()">
                        <button class="cat-act-btn" id="chevron-cat-{{ $categoria->id }}" title="Ver mais">↓</button>
                    </div>
                </div>

                <div class="cat-expand" id="expand-cat-{{ $categoria->id }}">

                    {{-- Editar categoria --}}
                    <form action="{{ route('admin.categorias.update', $categoria->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="f-row">
                            <input type="text" name="nome" class="f-input" value="{{ $categoria->nome }}" required maxlength="100">
                            <select name="tipo" class="f-select" style="flex:0 0 130px;">
                                <option value="evento"  {{ $categoria->tipo === 'evento'  ? 'selected' : '' }}>🎟 Evento</option>
                                <option value="noticia" {{ $categoria->tipo === 'noticia' ? 'selected' : '' }}>📰 Notícia</option>
                            </select>
                            <button type="submit" class="f-btn sky">💾 Guardar</button>
                        </div>
                    </form>

                    <div class="f-row" style="margin-top:-6px;">
                        {{-- Ativar/Desativar --}}
                        <form action="{{ route('admin.categorias.ativo', $categoria->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="f-btn gold">
                                {{ $categoria->ativo ? '⏸ Desativar' : '▶️ Reativar' }}
                            </button>
                        </form>

                        {{-- Eliminar (só se não houver eventos ligados) --}}
                        <form action="{{ route('admin.categorias.destroy', $categoria->id) }}" method="POST"
                              onsubmit="return confirm('Eliminar definitivamente esta categoria? Esta ação não pode ser desfeita.');">
                            @csrf @method('DELETE')
                            <button type="submit" class="f-btn red"
                                title="{{ $categoria->eventos_count > 0 ? 'Existem eventos ligados — desative em vez de eliminar' : 'Eliminar definitivamente' }}"
                                @if($categoria->eventos_count > 0) disabled style="opacity:.4;cursor:not-allowed;" @endif>
                                🗑 Eliminar definitivamente
                            </button>
                        </form>
                    </div>

                    {{-- Subcategorias --}}
                    <div class="sub-title">Subcategorias</div>
                    <div class="sub-list">
                        @forelse($categoria->subcategorias as $sub)
                            <div class="sub-row {{ !$sub->ativo ? 'inativa' : '' }}">
                                <form action="{{ route('admin.subcategorias.update', $sub->id) }}" method="POST" style="flex:1;display:flex;gap:6px;">
                                    @csrf @method('PUT')
                                    <input type="text" name="nome" value="{{ $sub->nome }}" class="f-input" style="min-width:0;padding:6px 10px;font-size:11.5px;">
                                    <small style="align-self:center;color:var(--muted);white-space:nowrap;">{{ $sub->eventos_count }} ev.</small>
                                    <button type="submit" class="sub-mini-btn" title="Guardar">💾</button>
                                </form>
                                <form action="{{ route('admin.subcategorias.ativo', $sub->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="sub-mini-btn" title="{{ $sub->ativo ? 'Desativar' : 'Reativar' }}">
                                        {{ $sub->ativo ? '⏸' : '▶️' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.subcategorias.destroy', $sub->id) }}" method="POST"
                                      onsubmit="return confirm('Eliminar esta subcategoria?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="sub-mini-btn"
                                        title="{{ $sub->eventos_count > 0 ? 'Existem eventos ligados' : 'Eliminar' }}"
                                        @if($sub->eventos_count > 0) disabled style="opacity:.4;cursor:not-allowed;" @endif>🗑</button>
                                </form>
                            </div>
                        @empty
                            <div style="font-size:11.5px;color:var(--muted);">Sem subcategorias ainda.</div>
                        @endforelse
                    </div>

                    {{-- Nova subcategoria --}}
                    <form action="{{ route('admin.subcategorias.store', $categoria->id) }}" method="POST" style="display:flex;gap:6px;">
                        @csrf
                        <input type="text" name="nome" class="f-input" placeholder="Nova subcategoria..." required maxlength="100" style="padding:8px 10px;font-size:12px;">
                        <button type="submit" class="f-btn outline">＋ Adicionar</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="adm-empty">
                <div class="adm-empty-icon">🏷️</div>
                <div class="adm-empty-txt">Nenhuma categoria deste tipo ainda.</div>
            </div>
        @endforelse
    </div>

    {{-- ── MODAL: NOVA CATEGORIA ── --}}
    <div class="modal-overlay" x-show="modalCriar" x-cloak @click.self="modalCriar = false">
        <div class="modal-box">
            <div class="modal-title">Nova Categoria</div>
            <form action="{{ route('admin.categorias.store') }}" method="POST">
                @csrf
                <div class="f-row" style="flex-direction:column;">
                    <input type="text" name="nome" class="f-input" placeholder="Nome da categoria" required maxlength="100" style="width:100%;">
                    <select name="tipo" class="f-select" style="width:100%;">
                        <option value="evento">🎟 Evento</option>
                        <option value="noticia">📰 Notícia</option>
                    </select>
                </div>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" class="f-btn outline" @click="modalCriar = false">Cancelar</button>
                    <button type="submit" class="f-btn sky">Criar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleExpandCat(id) {
    const el = document.getElementById('expand-cat-' + id);
    const chevron = document.getElementById('chevron-cat-' + id);
    if (!el) return;
    const aberto = el.classList.toggle('open');
    if (chevron) chevron.textContent = aberto ? '↑' : '↓';
}
</script>

@endsection