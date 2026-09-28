@extends('layouts.app')
@section('title', 'Sugestões — Admin')
@section('content')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-theme-vars.css') }}">
@endpush

<style>
[x-cloak] { display: none !important; }
:root {
  --bg: #05080f; --s1: #0a0f1e; --s2: #0f1628; --s3: #141d35;
  /* --border, --border2, --sky, --sky2, --green, --red, --gold, --purple,
     --text, --muted, --muted2 → vêm de public/css/admin-theme-vars.css */
}

.sug-wrap{max-width:760px;margin:0 auto;padding:8px 4px 60px;}

.sug-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;}
.sug-eyebrow{font-size:10px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:var(--sky);margin-bottom:5px;display:flex;align-items:center;gap:8px;}
.sug-eyebrow::before{content:'';width:16px;height:2px;background:var(--sky);border-radius:1px;}
.sug-title{font-size:22px;font-weight:800;color:#fff;}

.sug-filters{display:flex;gap:6px;margin-bottom:14px;}
.sug-filter{padding:7px 16px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;background:var(--s1);border:1px solid var(--border);color:var(--muted2);transition:all .2s;}
.sug-filter:hover{border-color:var(--border2);color:var(--text);}
.sug-filter.active{background:rgba(56,189,248,.12);border-color:var(--sky);color:var(--sky);}
.sug-filter-badge{background:var(--red);color:#fff;font-size:9px;font-weight:800;padding:1px 6px;border-radius:20px;margin-left:5px;}

.sug-card{background:var(--s2);border:1px solid var(--border);border-radius:16px;padding:16px 18px;margin-bottom:10px;transition:border-color .2s;}
.sug-card.novo{border-color:rgba(56,189,248,.35);background:rgba(56,189,248,.05);}
.sug-card-top{display:flex;align-items:center;gap:10px;margin-bottom:10px;}
.sug-ava{width:34px;height:34px;border-radius:50%;object-fit:cover;border:2px solid var(--border2);flex-shrink:0;}
.sug-user-name{font-size:13px;font-weight:700;color:#fff;}
.sug-date{font-size:10.5px;color:var(--muted);}
.sug-badge-novo{margin-left:auto;font-size:9px;font-weight:800;letter-spacing:.6px;text-transform:uppercase;background:rgba(56,189,248,.15);border:1px solid rgba(56,189,248,.3);color:var(--sky);padding:2px 9px;border-radius:20px;flex-shrink:0;}
.sug-msg{font-size:13.5px;color:#cbd5e1;line-height:1.65;white-space:pre-wrap;margin-bottom:12px;}
.sug-actions{display:flex;gap:8px;}
.sug-btn{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;border:none;transition:all .2s;}
.sug-btn-responder{background:rgba(56,189,248,.12);border:1px solid rgba(56,189,248,.3);color:var(--sky);}
.sug-btn-responder:hover{background:rgba(56,189,248,.22);}
.sug-btn-lida{background:var(--s3);border:1px solid var(--border2);color:var(--muted2);}
.sug-btn-lida:hover{color:var(--text);border-color:var(--sky);}

.sug-empty{text-align:center;padding:50px 20px;background:var(--s1);border:1px dashed var(--border2);border-radius:18px;}
.sug-empty-icon{font-size:36px;margin-bottom:10px;}
.sug-empty-txt{font-size:13px;color:var(--muted2);font-weight:700;}
</style>

<div class="sug-wrap">
    <div class="sug-head">
        <div>
            <div class="sug-eyebrow">Painel Admin</div>
            <div class="sug-title">💬 Sugestões</div>
        </div>
    </div>

    <div class="sug-filters">
        <a href="{{ route('admin.sugestoes', ['estado' => 'novo']) }}" class="sug-filter {{ $filtro === 'novo' ? 'active' : '' }}">
            🔵 Novas
            @if($totalNovas > 0)<span class="sug-filter-badge">{{ $totalNovas }}</span>@endif
        </a>
        <a href="{{ route('admin.sugestoes', ['estado' => 'lido']) }}" class="sug-filter {{ $filtro === 'lido' ? 'active' : '' }}">✓ Lidas</a>
        <a href="{{ route('admin.sugestoes', ['estado' => 'todos']) }}" class="sug-filter {{ $filtro === 'todos' ? 'active' : '' }}">Todas</a>
    </div>

    @forelse($sugestoes as $sugestao)
    <div class="sug-card {{ $sugestao->estado === 'novo' ? 'novo' : '' }}" id="sugestao-{{ $sugestao->id }}">
        <div class="sug-card-top">
            <img src="{{ $sugestao->user->avatar ? asset('storage/'.$sugestao->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($sugestao->user->name).'&background=0ea5e9&color=fff&size=64' }}" class="sug-ava" alt="">
            <div>
                <div class="sug-user-name">{{ $sugestao->user->name }}</div>
                <div class="sug-date">{{ $sugestao->created_at->diffForHumans() }}</div>
            </div>
            @if($sugestao->estado === 'novo')
            <span class="sug-badge-novo" id="sugestao-badge-{{ $sugestao->id }}">Nova</span>
            @endif
        </div>
        <div class="sug-msg">{{ $sugestao->mensagem }}</div>
        <div class="sug-actions">
            <a href="{{ route('mensagens.index', ['user_id' => $sugestao->user_id]) }}" class="sug-btn sug-btn-responder">💬 Responder</a>
            @if($sugestao->estado === 'novo')
            <button type="button" class="sug-btn sug-btn-lida" onclick="marcarSugestaoLida({{ $sugestao->id }}, this)">✓ Marcar como lida</button>
            @endif
        </div>
    </div>
    @empty
    <div class="sug-empty">
        <div class="sug-empty-icon">💬</div>
        <div class="sug-empty-txt">Nenhuma sugestão por aqui</div>
    </div>
    @endforelse

    <div style="margin-top:16px;">{{ $sugestoes->links() }}</div>
</div>

<script>
async function marcarSugestaoLida(id, btnEl) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    try {
        const res = await fetch(`/admin/sugestoes/${id}/lida`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
            swalToast.fire({ icon: 'error', title: data.message || 'Erro ao marcar como lida.' });
            return;
        }

        const card = document.getElementById('sugestao-' + id);
        if (card) card.classList.remove('novo');
        const badge = document.getElementById('sugestao-badge-' + id);
        if (badge) badge.remove();
        btnEl.remove();

        swalToast.fire({ icon: 'success', title: data.message || 'Sugestão marcada como lida!' });
    } catch (e) {
        swalToast.fire({ icon: 'error', title: 'Erro de conexão. Tenta novamente.' });
    }
}
</script>

@endsection