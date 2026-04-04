@extends('layouts.app')
@section('title', $user->name . ' — Luanda Bilhetes')
@section('content')

@php $handle = strtolower(preg_replace('/\s+/', '.', trim($user->name))); @endphp

<style>
*, *::before, *::after { box-sizing: border-box; }
@keyframes fadeUp { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }

.p-cover-wrap { position:relative; height:160px; overflow:hidden; margin:-40px -16px 0; }
@media(min-width:768px){ .p-cover-wrap { height:240px; margin:-40px -40px 0; } }
.p-cover-bg { width:100%; height:100%; position:relative; background:linear-gradient(135deg,#050d1a 0%,#091828 30%,#0c1f3a 60%,#071220 100%); }
.p-cover-bg img { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
.p-cover-glow { position:absolute; inset:0; z-index:1; pointer-events:none; background:radial-gradient(ellipse at 60% 40%,rgba(6,182,212,.22) 0%,transparent 55%),radial-gradient(ellipse at 20% 80%,rgba(245,158,11,.08) 0%,transparent 40%); }
.p-cover-fade { position:absolute; bottom:0; left:0; right:0; height:100px; z-index:2; background:linear-gradient(to top,#06090f 0%,transparent 100%); }

.p-header { position:relative; z-index:3; }
.p-top { display:flex; align-items:flex-end; gap:14px; margin-top:-44px; padding-bottom:14px; border-bottom:1px solid rgba(6,182,212,.15); flex-wrap:wrap; }
@media(min-width:768px){ .p-top { margin-top:-60px; gap:20px; padding-bottom:20px; } }

.p-ava { width:78px; height:78px; border-radius:50%; background:linear-gradient(135deg,#0c3a4a,#1e6a7a); border:3px solid #06090f; display:flex; align-items:center; justify-content:center; font-size:26px; font-weight:800; color:#06b6d4; overflow:hidden; box-shadow:0 8px 28px rgba(6,182,212,.3); position:relative; flex-shrink:0; }
@media(min-width:768px){ .p-ava { width:110px; height:110px; font-size:40px; } }
.p-ava img { width:100%; height:100%; object-fit:cover; }
.p-dot-on  { position:absolute; bottom:4px; right:4px; width:13px; height:13px; background:#10b981; border-radius:50%; border:2px solid #06090f; }
.p-dot-off { position:absolute; bottom:4px; right:4px; width:13px; height:13px; background:#475569; border-radius:50%; border:2px solid #06090f; }
.p-verified { position:absolute; top:2px; right:2px; width:17px; height:17px; background:#06b6d4; border-radius:50%; border:2px solid #06090f; display:flex; align-items:center; justify-content:center; font-size:8px; }

.p-info { flex:1; min-width:0; padding-bottom:6px; }
.p-name-row { display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-bottom:3px; }
.p-name { font-size:17px; font-weight:800; color:#fff; }
@media(min-width:768px){ .p-name { font-size:24px; } }
.p-handle { font-size:11px; color:#94a3b8; }
.p-badge { font-size:9px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; padding:2px 7px; border-radius:20px; }
.badge-admin   { background:rgba(244,63,94,.2); border:1px solid rgba(244,63,94,.4); color:#f87171; }
.badge-creator { background:rgba(6,182,212,.2); border:1px solid rgba(6,182,212,.4); color:#22d3ee; }
.badge-user    { background:rgba(148,163,184,.15); border:1px solid rgba(148,163,184,.3); color:#94a3b8; }

/* ✅ BIO — sem quebra vertical */
.p-bio {
    font-size: 12px;
    color: #94a3b8;
    line-height: 1.6;
    margin-bottom: 6px;
    max-width: 520px;
    display: block;
    word-break: normal !important;
    overflow-wrap: normal !important;
    white-space: normal !important;
    hyphens: none !important;
    letter-spacing: normal !important;
}

.p-meta { display:flex; gap:10px; flex-wrap:wrap; }
.p-meta-item { display:flex; align-items:center; gap:4px; font-size:11px; color:#64748b; }

.p-actions { display:flex; gap:8px; align-items:center; padding-bottom:6px; flex-shrink:0; }
.btn-follow { padding:8px 16px; border-radius:11px; font-size:12px; font-weight:700; background:linear-gradient(135deg,#06b6d4,#0ea5e9); color:#fff; border:none; cursor:pointer; }
.btn-msg, .btn-edit { padding:8px 14px; border-radius:11px; font-size:12px; font-weight:600; background:#1e293b; border:1px solid #334155; color:#e2e8f0; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:5px; }
.btn-edit { background:#0c2a3a; border-color:rgba(6,182,212,.4); color:#22d3ee; }
.btn-more { width:34px; height:34px; border-radius:11px; background:#1e293b; border:1px solid #334155; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#94a3b8; font-size:15px; }

.p-stats { display:flex; border-bottom:1px solid rgba(6,182,212,.12); overflow-x:auto; scrollbar-width:none; }
.p-stats::-webkit-scrollbar { display:none; }
.p-stat { flex:1; min-width:55px; text-align:center; padding:10px 6px; border-right:1px solid rgba(6,182,212,.1); cursor:pointer; transition:background .2s; }
.p-stat:last-child { border-right:none; }
.p-stat:hover { background:rgba(6,182,212,.05); }
.p-stat-num { font-size:17px; font-weight:800; color:#fff; line-height:1; }
.p-stat-lbl { font-size:9px; color:#64748b; margin-top:3px; text-transform:uppercase; letter-spacing:.8px; }

.p-quick-actions { display:flex; gap:8px; padding:14px 0; overflow-x:auto; scrollbar-width:none; border-bottom:1px solid rgba(6,182,212,.12); }
.p-quick-actions::-webkit-scrollbar { display:none; }
.p-qa-btn { flex-shrink:0; display:flex; flex-direction:column; align-items:center; gap:4px; padding:10px 16px; border-radius:12px; cursor:pointer; background:#111c2d; border:1px solid rgba(6,182,212,.15); transition:all .2s; min-width:70px; }
.p-qa-btn:hover { border-color:rgba(6,182,212,.4); background:#162032; }
.p-qa-btn.active { border-color:rgba(6,182,212,.5); background:rgba(6,182,212,.1); }
.p-qa-icon { font-size:20px; }
.p-qa-label { font-size:10px; font-weight:700; color:#64748b; text-align:center; white-space:nowrap; }
.p-qa-btn.active .p-qa-label { color:#06b6d4; }
.p-qa-badge { font-size:9px; font-weight:700; background:rgba(6,182,212,.2); color:#06b6d4; border-radius:10px; padding:1px 5px; }

.p-panel { display:none; flex-direction:column; gap:14px; padding-top:16px; animation:fadeUp .3s ease; }
.p-panel.active { display:flex; }

.ev-post { background:#111c2d; border:1px solid rgba(6,182,212,.18); border-radius:16px; overflow:hidden; transition:border-color .2s; }
.ev-post:hover { border-color:rgba(6,182,212,.45); }
.ev-post-img { height:150px; display:flex; align-items:center; justify-content:center; font-size:50px; position:relative; overflow:hidden; background:linear-gradient(135deg,#050d1a,#091828); }
.ev-post-img img { width:100%; height:100%; object-fit:cover; position:absolute; inset:0; }
.ev-post-img-overlay { position:absolute; inset:0; background:linear-gradient(to bottom,transparent 50%,rgba(6,9,15,.7) 100%); }
.ev-badge { position:absolute; top:10px; left:10px; z-index:2; font-size:9px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; padding:3px 8px; border-radius:20px; }
.ev-date-pill { position:absolute; bottom:10px; left:10px; z-index:2; background:rgba(6,9,15,.88); backdrop-filter:blur(8px); border:1px solid rgba(6,182,212,.25); border-radius:7px; font-size:10px; font-weight:600; padding:3px 8px; color:#e2e8f0; }
.ev-body { padding:12px 14px 14px; }
.ev-cat { font-size:10px; font-weight:700; letter-spacing:1px; text-transform:uppercase; margin-bottom:3px; }
.ev-title { font-size:15px; font-weight:700; margin-bottom:7px; line-height:1.25; color:#fff; }
.ev-meta { display:flex; gap:8px; margin-bottom:10px; flex-wrap:wrap; }
.ev-meta span { display:flex; align-items:center; gap:4px; font-size:11px; color:#94a3b8; }
.ev-bar-wrap { margin-bottom:10px; }
.ev-bar-top { display:flex; justify-content:space-between; font-size:10px; margin-bottom:4px; }
.ev-bar-top span { color:#64748b; } .ev-bar-top strong { color:#cbd5e1; }
.ev-bar { height:4px; background:#1e293b; border-radius:2px; overflow:hidden; }
.ev-bar-fill { height:100%; border-radius:2px; }
.fill-ok   { background:linear-gradient(to right,#06b6d4,#0ea5e9); }
.fill-warn { background:linear-gradient(to right,#f59e0b,#f97316); }
.fill-crit { background:linear-gradient(to right,#f43f5e,#f97316); }
.ev-footer { display:flex; align-items:center; justify-content:space-between; padding-top:10px; border-top:1px solid #1e293b; }
.ev-price { font-size:16px; font-weight:800; color:#f59e0b; }
.ev-price small { font-size:10px; color:#64748b; font-weight:400; }
.ev-price.free { color:#10b981; font-size:13px; font-weight:700; }
.ev-actions { display:flex; gap:6px; align-items:center; }
.ev-like-btn { display:flex; align-items:center; gap:4px; padding:5px 9px; border-radius:9px; background:#1e293b; border:1px solid #334155; font-size:11px; font-weight:600; cursor:pointer; color:#94a3b8; }
.ev-like-btn.liked { color:#f43f5e; border-color:rgba(244,63,94,.4); background:rgba(244,63,94,.12); }
.ev-buy-btn { padding:6px 12px; border-radius:9px; background:linear-gradient(135deg,#06b6d4,#0ea5e9); color:#fff; font-size:11px; font-weight:700; border:none; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:4px; }

.post-card { background:#111c2d; border:1px solid rgba(6,182,212,.14); border-radius:14px; padding:14px; animation:fadeUp .4s ease both; }
.post-author { display:flex; align-items:center; gap:10px; margin-bottom:10px; }
.post-ava { width:34px; height:34px; border-radius:50%; flex-shrink:0; overflow:hidden; background:linear-gradient(135deg,#0c3a4a,#1e6a7a); border:2px solid rgba(6,182,212,.3); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:13px; color:#06b6d4; }
.post-ava img { width:100%; height:100%; object-fit:cover; }
.post-name { font-size:12px; font-weight:700; color:#fff; }
.post-time { font-size:10px; color:#64748b; }
.post-text { font-size:13px; color:#cbd5e1; line-height:1.65; }
.post-img { width:100%; border-radius:10px; margin-top:10px; object-fit:cover; max-height:280px; display:block; }
.post-del-btn { margin-left:auto; background:none; border:none; cursor:pointer; color:#64748b; font-size:14px; padding:4px; }
.post-del-btn:hover { color:#f43f5e; }

.compose-box { background:#111c2d; border:1px solid rgba(6,182,212,.25); border-radius:14px; padding:14px; }
.compose-top { display:flex; align-items:flex-start; gap:10px; margin-bottom:10px; }
.compose-ava { width:34px; height:34px; border-radius:50%; flex-shrink:0; background:linear-gradient(135deg,#0c3a4a,#1e6a7a); border:2px solid rgba(6,182,212,.3); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:12px; color:#06b6d4; overflow:hidden; }
.compose-ava img { width:100%; height:100%; object-fit:cover; }
.compose-textarea { flex:1; background:#0d1a2e; border:1px solid #2d3f55; border-radius:10px; padding:9px 12px; color:#e2e8f0; font-size:13px; resize:none; font-family:inherit; min-height:70px; outline:none; line-height:1.6; }
.compose-textarea:focus { border-color:#06b6d4; }
.compose-textarea::placeholder { color:#475569; }
.compose-footer { display:flex; justify-content:flex-end; }
.compose-submit { padding:7px 18px; border-radius:9px; background:linear-gradient(135deg,#06b6d4,#0ea5e9); color:#fff; font-size:13px; font-weight:700; border:none; cursor:pointer; }

.p-empty { text-align:center; padding:36px 20px; background:#111c2d; border:1px solid rgba(6,182,212,.1); border-radius:14px; }
.p-empty-icon { font-size:32px; margin-bottom:8px; }
.p-empty-txt { font-size:10px; color:#64748b; text-transform:uppercase; font-weight:700; letter-spacing:1px; }

/* ✅ DRAWERS — corrigido height */
.drawer-overlay {
    display:none; position:fixed; inset:0; z-index:9999;
    background:rgba(0,0,0,.75); backdrop-filter:blur(6px);
}
.drawer-overlay.open { display:flex; align-items:flex-end; justify-content:center; }
@media(min-width:640px){ .drawer-overlay.open { align-items:center; } }
.drawer-box {
    width:100%; background:#0d1526;
    border:1px solid rgba(6,182,212,.2);
    border-radius:24px 24px 0 0;
    padding:20px 20px 32px;
    max-height:85vh; overflow-y:auto;
    scrollbar-width:none;
}
.drawer-box::-webkit-scrollbar { display:none; }
@media(min-width:640px){ .drawer-box { border-radius:24px; max-width:500px; max-height:80vh; } }
.drawer-handle { width:36px; height:4px; border-radius:2px; background:rgba(6,182,212,.25); margin:0 auto 16px; }
@media(min-width:640px){ .drawer-handle { display:none; } }
.drawer-title { font-size:15px; font-weight:800; color:#fff; margin-bottom:16px; display:flex; align-items:center; justify-content:space-between; }
.drawer-close { background:none; border:none; font-size:20px; color:#64748b; cursor:pointer; }
.drawer-close:hover { color:#fff; }

.going-list { display:flex; flex-direction:column; gap:8px; }
.going-item { display:flex; align-items:center; gap:10px; padding:10px; background:#0d1a2e; border-radius:11px; border:1px solid transparent; text-decoration:none; }
.going-item:hover { border-color:rgba(6,182,212,.3); }
.going-emoji { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:15px; flex-shrink:0; background:#1e293b; }
.going-info { flex:1; min-width:0; }
.going-name { font-size:11px; font-weight:600; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.going-date { font-size:10px; color:#06b6d4; font-weight:600; }
.going-price { font-size:11px; color:#f59e0b; font-weight:700; flex-shrink:0; }

.interests { display:flex; flex-wrap:wrap; gap:6px; }
.interest-tag { padding:4px 10px; border-radius:20px; font-size:11px; font-weight:600; border:1px solid #2d3f55; background:#1a2a3a; color:#94a3b8; }
.interest-tag.active { background:rgba(6,182,212,.15); border-color:rgba(6,182,212,.4); color:#22d3ee; }

.mutual-list { display:flex; flex-direction:column; gap:10px; }
.mutual-item { display:flex; align-items:center; gap:10px; }
.mutual-ava { width:36px; height:36px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:13px; color:#fff; border:1.5px solid #2d3f55; }
.mutual-info { flex:1; }
.mutual-name { font-size:12px; font-weight:600; color:#fff; }
.mutual-sub { font-size:10px; color:#64748b; }
.mutual-btn { padding:4px 10px; border-radius:8px; font-size:10px; font-weight:700; background:rgba(6,182,212,.12); border:1px solid rgba(6,182,212,.3); color:#06b6d4; text-decoration:none; display:inline-block; }

.gallery-section-title { font-size:10px; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:1.5px; margin:16px 0 8px; }
.gallery-section-title:first-child { margin-top:0; }
.gallery-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:6px; }
.gallery-item { aspect-ratio:1; border-radius:8px; overflow:hidden; background:#1e293b; display:flex; align-items:center; justify-content:center; border:1px solid #2d3f55; cursor:pointer; transition:all .2s; position:relative; }
.gallery-item:hover { border-color:rgba(6,182,212,.4); transform:scale(1.02); }
.gallery-item img { width:100%; height:100%; object-fit:cover; }

/* ✅ LIGHTBOX */
.lightbox { display:none; position:fixed; inset:0; z-index:999999; background:rgba(0,0,0,.93); backdrop-filter:blur(8px); align-items:center; justify-content:center; }
.lightbox.open { display:flex; }
.lightbox-img { max-width:92vw; max-height:86vh; border-radius:12px; object-fit:contain; }
.lightbox-close { position:absolute; top:16px; right:16px; width:38px; height:38px; border-radius:50%; background:rgba(255,255,255,.12); border:none; color:#fff; font-size:18px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
.lightbox-close:hover { background:rgba(244,63,94,.4); }

/* ✅ MODAL BILHETES — z-index máximo, sempre visível */
.mb-overlay {
    display:none;
    position:fixed;
    inset:0;
    z-index:999998;
    background:rgba(0,0,0,.88);
    backdrop-filter:blur(8px);
    align-items:flex-end;
    justify-content:center;
    padding:0;
}
@media(min-width:640px){
    .mb-overlay { align-items:center; padding:20px; }
}
.mb-overlay.open { display:flex; }

.mb-box {
    background:#0d1526;
    border:1px solid rgba(6,182,212,.3);
    border-radius:24px 24px 0 0;
    width:100%;
    max-width:600px;
    /* ✅ altura máxima com scroll interno */
    max-height:90vh;
    display:flex;
    flex-direction:column;
    overflow:hidden;
}
@media(min-width:640px){
    .mb-box { border-radius:24px; max-height:85vh; }
}

.mb-header {
    padding:16px 20px;
    border-bottom:1px solid rgba(6,182,212,.15);
    display:flex; justify-content:space-between; align-items:center;
    flex-shrink:0;
}
.mb-header h3 { font-size:16px; font-weight:800; color:#fff; margin-bottom:2px; }
.mb-header p { font-size:10px; color:#06b6d4; font-weight:700; text-transform:uppercase; letter-spacing:1.5px; }
.mb-close { width:32px; height:32px; border-radius:50%; background:rgba(255,255,255,.05); border:none; color:#fff; font-size:16px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
.mb-close:hover { background:rgba(244,63,94,.2); }

/* ✅ Body do modal com scroll e sem barra visível */
.mb-body {
    flex:1;
    overflow-y:auto;
    padding:14px;
    display:flex;
    flex-direction:column;
    gap:12px;
    scrollbar-width:none;
}
.mb-body::-webkit-scrollbar { display:none; }

/* ✅ Card de bilhete */
.mb-ticket {
    background:#fff;
    border-radius:14px;
    overflow:hidden;
    border:1px solid #e2e8f0;
}
.mb-ticket-info {
    padding:14px 14px 12px;
    border-bottom:2px dashed #e2e8f0;
}
.mb-ticket-badge {
    display:inline-block;
    background:#2563eb; color:#fff;
    font-size:10px; font-weight:800;
    padding:3px 10px; border-radius:20px;
    text-transform:uppercase; margin-bottom:10px;
}
.mb-ticket-title { color:#0f172a; font-weight:800; font-size:15px; line-height:1.2; margin-bottom:8px; }
.mb-ticket-meta { color:#64748b; font-size:12px; margin-bottom:2px; }
.mb-ticket-id { color:#94a3b8; font-size:10px; font-family:monospace; margin-top:8px; }

/* ✅ Acções do bilhete — flex-wrap para não esconder botões */
.mb-ticket-actions {
    display:flex;
    align-items:center;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:8px;
    margin-top:10px;
}
.mb-btn-download {
    display:inline-flex; align-items:center; gap:6px;
    background:#2563eb; color:#fff;
    padding:8px 14px; border-radius:10px;
    font-size:12px; font-weight:700;
    text-decoration:none; white-space:nowrap;
    flex-shrink:0;
}
.mb-btn-download:hover { background:#1d4ed8; }
.mb-btn-delete {
    display:inline-flex; align-items:center; gap:5px;
    background:rgba(244,63,94,.1); color:#f43f5e;
    border:1px solid rgba(244,63,94,.25);
    padding:8px 12px; border-radius:10px;
    font-size:12px; font-weight:700;
    cursor:pointer; white-space:nowrap;
    flex-shrink:0;
}
.mb-btn-delete:hover { background:rgba(244,63,94,.2); }

.mb-ticket-qr {
    padding:12px;
    background:#f8fafc;
    display:flex; flex-direction:column; align-items:center;
}
.mb-ticket-qr img { width:76px; height:76px; margin-bottom:5px; }
.mb-ticket-qr p { font-size:9px; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:1px; }
.mb-status-used  { margin-top:5px; background:#dcfce7; color:#15803d; font-size:9px; font-weight:700; padding:2px 8px; border-radius:20px; display:inline-block; }
.mb-status-valid { margin-top:5px; background:#dbeafe; color:#1d4ed8; font-size:9px; font-weight:700; padding:2px 8px; border-radius:20px; display:inline-block; }
</style>

{{-- LIGHTBOX --}}
<div class="lightbox" id="lightbox" onclick="fecharLightbox()">
    <button class="lightbox-close" onclick="fecharLightbox()">✕</button>
    <img class="lightbox-img" id="lightboxImg" src="" alt="">
</div>


{{-- ✅ MODAL BILHETES — estilos inline para evitar conflitos com o layout --}}
@if($isOwner)
<div id="modalBilhetes"
     onclick="if(event.target===this) fecharModalBilhetes()"
     style="display:none;position:fixed;inset:0;z-index:999999;background:rgba(0,0,0,0.88);backdrop-filter:blur(10px);align-items:center;justify-content:center;padding:20px;">
 
    <div style="background:#0d1526;border:1px solid rgba(6,182,212,0.3);border-radius:20px;width:100%;max-width:640px;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,0.6);">
 
        {{-- Header --}}
        <div style="padding:14px 18px;border-bottom:1px solid rgba(6,182,212,0.15);display:flex;justify-content:space-between;align-items:center;flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="font-size:14px;font-weight:800;color:#ffffff;font-family:sans-serif;">🎫 Meus Bilhetes</div>
                @if($bilhetes->count() > 0)
                <span id="mb-counter" style="font-size:11px;color:#64748b;font-family:sans-serif;background:rgba(255,255,255,0.06);padding:2px 8px;border-radius:20px;">1 / {{ $bilhetes->count() }}</span>
                @endif
            </div>
            <button onclick="fecharModalBilhetes()" style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,0.08);border:none;color:#94a3b8;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;">✕</button>
        </div>
 
        @if($bilhetes->count() > 0)
 
        {{-- Container dos slides --}}
        <div id="mb-track"
             style="overflow:hidden;position:relative;touch-action:pan-y;"
             onwheel="mbOnWheel(event)">
 
            @foreach($bilhetes as $i => $bilhete)
            <div id="mb-slide-{{ $i }}"
                 style="display:{{ $i===0?'block':'none' }};">
 
                {{-- Bilhete horizontal --}}
                <div style="margin:14px;border-radius:14px;overflow:hidden;border:1.5px solid #e2e8f0;position:relative;"
                     onmouseenter="document.getElementById('mb-actions-{{ $i }}').style.opacity='1'"
                     onmouseleave="document.getElementById('mb-actions-{{ $i }}').style.opacity='0'">
 
                    {{-- Layout horizontal: esquerda info + direita QR --}}
                    <div style="display:flex;background:#ffffff;">
 
                        {{-- Coluna esquerda --}}
                        <div style="flex:1;padding:16px 14px 14px;border-right:2px dashed #e2e8f0;position:relative;">
                            {{-- Semicírculos do picote --}}
                            <div style="position:absolute;right:-10px;top:-10px;width:20px;height:20px;border-radius:50%;background:#0d1526;"></div>
                            <div style="position:absolute;right:-10px;bottom:-10px;width:20px;height:20px;border-radius:50%;background:#0d1526;"></div>
 
                            <span style="display:inline-block;background:#2563eb;color:#fff;font-size:9px;font-weight:800;padding:2px 10px;border-radius:20px;text-transform:uppercase;margin-bottom:8px;font-family:sans-serif;">
                                {{ $bilhete->tipoIngresso->nome ?? 'Geral' }}
                            </span>
                            <div style="font-size:14px;font-weight:800;color:#0f172a;line-height:1.25;margin-bottom:8px;font-family:sans-serif;">
                                {{ $bilhete->evento->titulo }}
                            </div>
                            <div style="font-size:11px;color:#475569;margin-bottom:3px;font-family:sans-serif;">📅 {{ \Carbon\Carbon::parse($bilhete->evento->data_evento)->translatedFormat('d M Y') }}</div>
                            <div style="font-size:11px;color:#475569;margin-bottom:3px;font-family:sans-serif;">📍 {{ Str::limit($bilhete->evento->localizacao, 28) }}</div>
                            <div style="font-size:11px;color:#475569;margin-bottom:10px;font-family:sans-serif;">💰 {{ number_format($bilhete->tipoIngresso->preco ?? 0, 0, ',', '.') }} Kz</div>
 
                            <div style="font-size:9px;color:#94a3b8;font-family:monospace;background:#f1f5f9;padding:5px 7px;border-radius:6px;word-break:break-all;line-height:1.4;">
                                {{ substr($bilhete->codigo_unico, 0, 20) }}...
                            </div>
 
                            @if($bilhete->validado_em)
                            <span style="display:inline-block;margin-top:8px;background:#dcfce7;color:#15803d;font-size:9px;font-weight:700;padding:2px 10px;border-radius:20px;font-family:sans-serif;">✓ Utilizado</span>
                            @else
                            <span style="display:inline-block;margin-top:8px;background:#dbeafe;color:#1d4ed8;font-size:9px;font-weight:700;padding:2px 10px;border-radius:20px;font-family:sans-serif;">● Válido</span>
                            @endif
                        </div>
 
                        {{-- Coluna direita: QR --}}
                        <div style="width:130px;flex-shrink:0;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:14px 10px;background:#f8fafc;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($bilhete->codigo_unico) }}"
                                 alt="QR"
                                 style="width:88px;height:88px;border-radius:6px;margin-bottom:6px;">
                            <div style="font-size:8px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;text-align:center;font-family:sans-serif;">Apresente na entrada</div>
                        </div>
                    </div>
 
                    {{-- ✅ Overlay de hover com botões --}}
                    <div id="mb-actions-{{ $i }}"
                         style="position:absolute;inset:0;background:rgba(13,21,38,0.82);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;gap:10px;opacity:0;transition:opacity 0.2s;border-radius:14px;">
                        <a href="{{ route('bilhete.individual.download', $bilhete->id) }}"
                           target="_blank"
                           style="display:inline-flex;align-items:center;gap:6px;background:linear-gradient(135deg,#06b6d4,#0ea5e9);color:#000;padding:10px 18px;border-radius:12px;font-size:13px;font-weight:800;text-decoration:none;white-space:nowrap;font-family:sans-serif;box-shadow:0 4px 14px rgba(6,182,212,0.4);">
                            📄 Baixar PDF
                        </a>
                        @if($bilhete->validado_em)
                        <form method="POST" action="{{ route('bilhete.eliminar', $bilhete->id) }}"
                              onsubmit="return confirm('Eliminar este bilhete já utilizado?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="display:inline-flex;align-items:center;gap:5px;background:rgba(244,63,94,0.2);color:#f43f5e;border:1px solid rgba(244,63,94,0.5);padding:10px 16px;border-radius:12px;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;font-family:sans-serif;">
                                🗑 Eliminar
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
 
                {{-- Botões visíveis sempre no mobile (abaixo do bilhete) --}}
                <div style="display:none;padding:0 14px 4px;gap:8px;" id="mb-mobile-actions-{{ $i }}" class="mb-mobile-actions">
                    <a href="{{ route('bilhete.individual.download', $bilhete->id) }}"
                       target="_blank"
                       style="flex:1;display:inline-flex;align-items:center;justify-content:center;gap:6px;background:linear-gradient(135deg,#06b6d4,#0ea5e9);color:#000;padding:10px;border-radius:12px;font-size:12px;font-weight:800;text-decoration:none;font-family:sans-serif;">
                        📄 Baixar PDF
                    </a>
                    @if($bilhete->validado_em)
                    <form method="POST" action="{{ route('bilhete.eliminar', $bilhete->id) }}"
                          onsubmit="return confirm('Eliminar?')" style="flex:1;">
                        @csrf @method('DELETE')
                        <button type="submit"
                                style="width:100%;display:inline-flex;align-items:center;justify-content:center;gap:5px;background:rgba(244,63,94,0.15);color:#f43f5e;border:1px solid rgba(244,63,94,0.3);padding:10px;border-radius:12px;font-size:12px;font-weight:700;cursor:pointer;font-family:sans-serif;">
                            🗑 Eliminar
                        </button>
                    </form>
                    @endif
                </div>
 
            </div>
            @endforeach
        </div>
 
        {{-- Navegação --}}
        <div style="padding:12px 18px 14px;display:flex;align-items:center;justify-content:space-between;border-top:1px solid rgba(6,182,212,0.1);">
            <button onclick="mbNav(-1)"
                    style="display:flex;align-items:center;gap:5px;background:rgba(6,182,212,0.1);border:1px solid rgba(6,182,212,0.2);color:#06b6d4;padding:7px 14px;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;font-family:sans-serif;{{ $bilhetes->count() <= 1 ? 'opacity:0.3;pointer-events:none;' : '' }}">
                ← Anterior
            </button>
 
            <div style="display:flex;gap:5px;align-items:center;">
                @foreach($bilhetes as $i => $b)
                <div onclick="mbGoTo({{ $i }})"
                     id="mb-dot-{{ $i }}"
                     style="width:{{ $i===0?'18px':'7px' }};height:7px;border-radius:4px;background:{{ $i===0?'#06b6d4':'rgba(255,255,255,0.2)' }};cursor:pointer;transition:all 0.2s;">
                </div>
                @endforeach
            </div>
 
            <button onclick="mbNav(1)"
                    style="display:flex;align-items:center;gap:5px;background:rgba(6,182,212,0.1);border:1px solid rgba(6,182,212,0.2);color:#06b6d4;padding:7px 14px;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;font-family:sans-serif;{{ $bilhetes->count() <= 1 ? 'opacity:0.3;pointer-events:none;' : '' }}">
                Próximo →
            </button>
        </div>
 
        @else
        <div style="padding:40px 20px;text-align:center;">
            <div style="font-size:36px;margin-bottom:10px;">🎫</div>
            <div style="font-size:14px;color:#94a3b8;font-weight:600;margin-bottom:4px;font-family:sans-serif;">Nenhum bilhete encontrado</div>
            <div style="font-size:12px;color:#64748b;font-family:sans-serif;">Os bilhetes aparecem após confirmação de pagamento.</div>
        </div>
        @endif
 
    </div>
   </div>
@endif

{{-- HEADER --}}
<div class="p-header">
    <div class="p-top">
        <div class="p-ava" style="position:relative;">
            @if($user->avatar)
                <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}">
            @else {{ strtoupper(substr($user->name,0,2)) }} @endif
            @if(method_exists($user,'isOnline') && $user->isOnline())
                <div class="p-dot-on"></div>
            @else
                <div class="p-dot-off"></div>
            @endif
            @if($user->is_verified)<div class="p-verified">✓</div>@endif
        </div>

        <div class="p-info">
            <div class="p-name-row">
                <div class="p-name">{{ $user->name }}</div>
                <div class="p-handle">&#64;{{ $handle }}</div>
                @if($user->role==='admin') <span class="p-badge badge-admin">🛡 Admin</span>
                @elseif($user->role==='creator') <span class="p-badge badge-creator">🎟 Criador</span>
                @else <span class="p-badge badge-user">👤 Membro</span> @endif
            </div>
            @if(!empty($user->bio))
            <div class="p-bio">{{ $user->bio }}</div>
            @endif
            <div class="p-meta">
                <div class="p-meta-item">📍 <strong style="color:#94a3b8">Luanda</strong></div>
                <div class="p-meta-item">🗓 <strong style="color:#94a3b8">{{ $user->created_at->translatedFormat('M Y') }}</strong></div>
            </div>
        </div>

        <div class="p-actions">
            @if($isOwner)
                <a href="{{ route('profile.edit') }}" class="btn-edit">✏️ Editar</a>
            @else
                @auth
                    <button class="btn-follow" id="followBtn" onclick="toggleFollow()">+ Seguir</button>
                    <a href="{{ route('mensagens.index', ['user_id' => $user->id]) }}" class="btn-msg">💬</a>
                @endauth
            @endif
            <div class="btn-more" onclick="abrirDrawer('drawer-mais')">⋯</div>
        </div>
    </div>

    <div class="p-stats">
        <div class="p-stat"><div class="p-stat-num">0</div><div class="p-stat-lbl">Seguidores</div></div>
        <div class="p-stat"><div class="p-stat-num">0</div><div class="p-stat-lbl">A seguir</div></div>
        <div class="p-stat"><div class="p-stat-num">{{ $statsCount }}</div><div class="p-stat-lbl">{{ $statsLabel }}</div></div>
        <div class="p-stat"><div class="p-stat-num">{{ $postagens->count() }}</div><div class="p-stat-lbl">Posts</div></div>
        <div class="p-stat"><div class="p-stat-num">{{ $statsCount2 }}</div><div class="p-stat-lbl">{{ $statsLabel2 }}</div></div>
    </div>

    <div class="p-quick-actions">
        <div class="p-qa-btn active" id="qa-eventos" onclick="switchPanel('eventos')">
            <div class="p-qa-icon">🎟</div><div class="p-qa-label">Eventos</div>
            <div class="p-qa-badge">{{ $eventos->count() }}</div>
        </div>
        <div class="p-qa-btn" id="qa-posts" onclick="switchPanel('posts')">
            <div class="p-qa-icon">📝</div><div class="p-qa-label">Posts</div>
            <div class="p-qa-badge">{{ $postagens->count() }}</div>
        </div>
        <div class="p-qa-btn" onclick="abrirDrawer('drawer-galeria')">
            <div class="p-qa-icon">📸</div><div class="p-qa-label">Galeria</div>
        </div>
        @if($eventos->count()>0)
        <div class="p-qa-btn" onclick="abrirDrawer('drawer-agenda')">
            <div class="p-qa-icon">📅</div><div class="p-qa-label">Agenda</div>
        </div>
        @endif
        <div class="p-qa-btn" onclick="abrirDrawer('drawer-interesses')">
            <div class="p-qa-icon">🏷</div><div class="p-qa-label">Interesses</div>
        </div>
        @if($isOwner)
        <div class="p-qa-btn" onclick="abrirModalBilhetes()">
            <div class="p-qa-icon">🎫</div><div class="p-qa-label">Bilhetes</div>
        </div>
        @endif
        @auth @if(!$isOwner)
        <div class="p-qa-btn" onclick="abrirDrawer('drawer-conhecer')">
            <div class="p-qa-icon">👥</div><div class="p-qa-label">Conhecer</div>
        </div>
        @endif @endauth
    </div>
</div>

{{-- PANEL EVENTOS --}}
<div class="p-panel active" id="panel-eventos">
    @forelse($eventos as $evento)
    @php
        $preco=$evento->tiposIngresso->sortBy('preco')->first()?->preco??0;
        $lotacao=$evento->lotacao_maxima??0;
        $vendidos=$evento->tiposIngresso->sum('quantidade_vendida')??0;
        $pct=$lotacao>0?round(($vendidos/$lotacao)*100):0;
        $barClass=$pct>=80?'fill-crit':($pct>=50?'fill-warn':'fill-ok');
        $catNome=optional($evento->categoria)->nome??'Evento';
        $catEmoji=match(strtolower($catNome)){'música','musica'=>'🎵','arte','arte & cultura'=>'🎨','festa','festas'=>'🎉','desporto'=>'⚽','gastronomia'=>'🍽','negócios','negocios'=>'💼',default=>'🎟'};
        $catColor=match(strtolower($catNome)){'música','musica'=>'#06b6d4','arte','arte & cultura'=>'#a78bfa','festa','festas'=>'#f59e0b','desporto'=>'#10b981','gastronomia'=>'#f97316','negócios','negocios'=>'#0ea5e9',default=>'#06b6d4'};
    @endphp
    <div class="ev-post">
        <div class="ev-post-img">
            @if($evento->imagem_capa)<img src="{{ asset('storage/'.$evento->imagem_capa) }}" alt="">@else {{ $catEmoji }} @endif
            <div class="ev-post-img-overlay"></div>
            @if($pct>=80)<span class="ev-badge" style="background:rgba(244,63,94,.9);color:#fff">🔥 A Esgotar</span>
            @elseif($preco==0)<span class="ev-badge" style="background:rgba(16,185,129,.9);color:#fff">Gratuito</span>
            @elseif($evento->created_at->isCurrentWeek())<span class="ev-badge" style="background:rgba(14,165,233,.9);color:#fff">✨ Novo</span>@endif
            <div class="ev-date-pill">📅 {{ \Carbon\Carbon::parse($evento->data_evento)->translatedFormat('D, d M') }}</div>
        </div>
        <div class="ev-body">
            <div class="ev-cat" style="color:{{ $catColor }}">{{ $catEmoji }} {{ $catNome }}</div>
            <div class="ev-title">{{ $evento->titulo }}</div>
            <div class="ev-meta">
                <span>📍 {{ $evento->localizacao??'Luanda' }}</span>
                @if($lotacao>0)<span>👥 {{ number_format($lotacao) }}</span>@endif
            </div>
            @if($lotacao>0)
            <div class="ev-bar-wrap">
                <div class="ev-bar-top"><span>Disponibilidade</span><strong>{{ $vendidos }}/{{ $lotacao }}</strong></div>
                <div class="ev-bar"><div class="ev-bar-fill {{ $barClass }}" style="width:{{ $pct }}%"></div></div>
            </div>
            @endif
            <div class="ev-footer">
                <div class="ev-price {{ $preco==0?'free':'' }}">
                    @if($preco==0) ✅ Gratuito @else {{ number_format($preco,0,',','.') }} <small>Kz</small> @endif
                </div>
                <div class="ev-actions">
                    @auth
                    <form method="POST" action="{{ route('evento.curtir',$evento->id) }}" style="display:inline">
                        @csrf
                        <button type="submit" class="ev-like-btn {{ $evento->usuariosQueCurtiram->contains(auth()->id())?'liked':'' }}">
                            {{ $evento->usuariosQueCurtiram->contains(auth()->id())?'❤️':'🤍' }} {{ $evento->curtidas->count() }}
                        </button>
                    </form>
                    @endauth
                    <a href="{{ route('evento.detalhes',$evento->id) }}" class="ev-buy-btn">🎟 Comprar</a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="p-empty"><div class="p-empty-icon">🎟</div><div class="p-empty-txt">Nenhum evento ainda</div></div>
    @endforelse
</div>

{{-- PANEL POSTS --}}
<div class="p-panel" id="panel-posts">
    @if($isOwner)
    <div class="compose-box">
        <form method="POST" action="{{ route('social.publicar') }}" enctype="multipart/form-data">
            @csrf
            <div class="compose-top">
                <div class="compose-ava">
                    @if(auth()->user()->avatar)<img src="{{ asset('storage/'.auth()->user()->avatar) }}" alt="">
                    @else {{ strtoupper(substr(auth()->user()->name,0,2)) }} @endif
                </div>
                <textarea name="conteudo" class="compose-textarea" placeholder="Partilha algo..."></textarea>
            </div>
            <div class="compose-footer"><button type="submit" class="compose-submit">✈️ Publicar</button></div>
        </form>
    </div>
    @endif
    @forelse($postagens as $post)
    <div class="post-card">
        <div class="post-author">
            <div class="post-ava">
                @if($user->avatar)<img src="{{ asset('storage/'.$user->avatar) }}" alt="">
                @else {{ strtoupper(substr($user->name,0,2)) }} @endif
            </div>
            <div><div class="post-name">{{ $user->name }}</div><div class="post-time">{{ $post->created_at->diffForHumans() }}</div></div>
            @if($isOwner)
            <form method="POST" action="{{ route('post.eliminar',$post->id) }}" style="margin-left:auto" onsubmit="return confirm('Eliminar?')">
                @csrf @method('DELETE')
                <button type="submit" class="post-del-btn">🗑</button>
            </form>
            @endif
        </div>
        <p class="post-text">{{ $post->conteudo }}</p>
        @if($post->imagem)<img src="{{ asset('storage/'.$post->imagem) }}" class="post-img" alt="">@endif
    </div>
    @empty
    <div class="p-empty"><div class="p-empty-icon">📝</div><div class="p-empty-txt">Nenhuma publicação ainda</div></div>
    @endforelse
</div>

{{-- ✅ DRAWER GALERIA --}}
<div class="drawer-overlay" id="drawer-galeria" onclick="if(event.target===this) fecharDrawer('drawer-galeria')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">📸 Galeria <button class="drawer-close" onclick="fecharDrawer('drawer-galeria')">✕</button></div>

        @if($user->avatar)
        <div class="gallery-section-title">📷 Foto de Perfil</div>
        <div class="gallery-grid">
            <div class="gallery-item" onclick="abrirLightbox('{{ asset('storage/'.$user->avatar) }}')">
                <img src="{{ asset('storage/'.$user->avatar) }}" alt="Perfil">
            </div>
        </div>
        @endif

        @if(!empty($user->cover))
        <div class="gallery-section-title">🖼️ Foto de Capa</div>
        <div style="border-radius:10px;overflow:hidden;cursor:pointer;aspect-ratio:16/5;margin-bottom:4px;" onclick="abrirLightbox('{{ asset('storage/'.$user->cover) }}')">
            <img src="{{ asset('storage/'.$user->cover) }}" style="width:100%;height:100%;object-fit:cover;" alt="Capa">
        </div>
        @endif

        @php $comImg = $eventos->whereNotNull('imagem_capa'); @endphp
        @if($comImg->count()>0)
        <div class="gallery-section-title">🎟 Fotos de Eventos ({{ $comImg->count() }})</div>
        <div class="gallery-grid">
            @foreach($comImg as $ev)
            <div class="gallery-item" onclick="abrirLightbox('{{ asset('storage/'.$ev->imagem_capa) }}')">
                <img src="{{ asset('storage/'.$ev->imagem_capa) }}" alt="{{ $ev->titulo }}">
                <div style="position:absolute;bottom:0;left:0;right:0;background:linear-gradient(to top,rgba(0,0,0,.75),transparent);padding:5px 6px 4px;font-size:9px;color:#fff;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ev->titulo }}</div>
            </div>
            @endforeach
        </div>
        @endif

        @if(!$user->avatar && empty($user->cover) && $comImg->count()===0)
        <div class="p-empty"><div class="p-empty-icon">📸</div><div class="p-empty-txt">Nenhuma foto ainda</div></div>
        @endif
    </div>
</div>

{{-- DRAWER AGENDA --}}
<div class="drawer-overlay" id="drawer-agenda" onclick="if(event.target===this) fecharDrawer('drawer-agenda')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">📅 Agenda <button class="drawer-close" onclick="fecharDrawer('drawer-agenda')">✕</button></div>
        <div class="going-list">
            @foreach($eventos->take(8) as $ev)
            @php $pS=optional($ev->tiposIngresso->sortBy('preco')->first())->preco??0; $eS=match(strtolower(optional($ev->categoria)->nome??'')){'música','musica'=>'🎵','arte'=>'🎨','festa','festas'=>'🎉','desporto'=>'⚽',default=>'🎟'}; @endphp
            <a href="{{ route('evento.detalhes',$ev->id) }}" class="going-item">
                <div class="going-emoji">{{ $eS }}</div>
                <div class="going-info">
                    <div class="going-name">{{ $ev->titulo }}</div>
                    <div class="going-date">{{ \Carbon\Carbon::parse($ev->data_evento)->translatedFormat('D, d M') }}</div>
                </div>
                <div class="going-price" style="{{ $pS==0?'color:#10b981':'' }}">{{ $pS==0?'Grátis':number_format($pS/1000,0).'k Kz' }}</div>
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- DRAWER INTERESSES --}}
<div class="drawer-overlay" id="drawer-interesses" onclick="if(event.target===this) fecharDrawer('drawer-interesses')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">🏷 Interesses <button class="drawer-close" onclick="fecharDrawer('drawer-interesses')">✕</button></div>
        <div class="interests">
            @php $cats=$eventos->map(fn($e)=>optional($e->categoria)->nome)->filter()->unique()->values(); @endphp
            @if($cats->count()>0) @foreach($cats->take(8) as $c)<span class="interest-tag active">{{ $c }}</span>@endforeach
            @else @foreach(['🎵 Música','🎉 Festas','🎟 Eventos','🌊 Luanda','🇦🇴 Angola'] as $t)<span class="interest-tag">{{ $t }}</span>@endforeach @endif
        </div>
    </div>
</div>

{{-- DRAWER CONHECER --}}
@auth @if(!$isOwner)
<div class="drawer-overlay" id="drawer-conhecer" onclick="if(event.target===this) fecharDrawer('drawer-conhecer')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">👥 Poderás conhecer <button class="drawer-close" onclick="fecharDrawer('drawer-conhecer')">✕</button></div>
        <div class="mutual-list">
            @foreach(\App\Models\User::where('id','!=',$user->id)->where('id','!=',auth()->id())->take(6)->get() as $u)
            <div class="mutual-item">
                <div class="mutual-ava" style="background:linear-gradient(135deg,#0c3a4a,#1e6a7a)">{{ strtoupper(substr($u->name,0,2)) }}</div>
                <div class="mutual-info"><div class="mutual-name">{{ $u->name }}</div><div class="mutual-sub">{{ ucfirst($u->role) }}</div></div>
                <a href="{{ route('profile.show',$u->id) }}" class="mutual-btn">Ver</a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif @endauth

{{-- DRAWER MAIS --}}
<div class="drawer-overlay" id="drawer-mais" onclick="if(event.target===this) fecharDrawer('drawer-mais')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">⋯ Mais opções <button class="drawer-close" onclick="fecharDrawer('drawer-mais')">✕</button></div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            @if($isOwner)
            <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:12px;padding:13px;border-radius:12px;background:#111c2d;border:1px solid rgba(6,182,212,.15);color:#e2e8f0;text-decoration:none;">
                <span style="font-size:18px;">✏️</span><span style="font-size:13px;font-weight:600;">Editar perfil</span>
            </a>
            <button onclick="fecharDrawer('drawer-mais');abrirModalBilhetes();" style="display:flex;align-items:center;gap:12px;padding:13px;border-radius:12px;background:#111c2d;border:1px solid rgba(6,182,212,.15);color:#e2e8f0;width:100%;text-align:left;cursor:pointer;font-family:inherit;">
                <span style="font-size:18px;">🎫</span><span style="font-size:13px;font-weight:600;">Meus bilhetes</span>
            </button>
            @endif
            @auth @if(!$isOwner)
            <a href="{{ route('mensagens.index',['user_id'=>$user->id]) }}" style="display:flex;align-items:center;gap:12px;padding:13px;border-radius:12px;background:#111c2d;border:1px solid rgba(6,182,212,.15);color:#e2e8f0;text-decoration:none;">
                <span style="font-size:18px;">💬</span><span style="font-size:13px;font-weight:600;">Enviar mensagem</span>
            </a>
            @endif @endauth
        </div>
    </div>
</div>

<script>
var mbIndex = 0;
var mbTotal = {{ $bilhetes->count() }};
var mbTouchStartX = 0;
var isMobile = ('ontouchstart' in window);
 
// Mostra botões mobile se for touch
if (isMobile) {
    document.querySelectorAll('.mb-mobile-actions').forEach(function(el) {
        el.style.display = 'flex';
    });
}

function mbGoTo(n) {
    if (n < 0 || n >= mbTotal) return;
 
    var oldSlide = document.getElementById('mb-slide-' + mbIndex);
    var oldDot   = document.getElementById('mb-dot-'   + mbIndex);
    if (oldSlide) oldSlide.style.display = 'none';
    if (oldDot)   { oldDot.style.width = '7px'; oldDot.style.background = 'rgba(255,255,255,0.2)'; }
 
    mbIndex = n;
 
    var newSlide = document.getElementById('mb-slide-' + mbIndex);
    var newDot   = document.getElementById('mb-dot-'   + mbIndex);
    if (newSlide) newSlide.style.display = 'block';
    if (newDot)   { newDot.style.width = '18px'; newDot.style.background = '#06b6d4'; }
 
    var counter = document.getElementById('mb-counter');
    if (counter) counter.textContent = (mbIndex + 1) + ' / ' + mbTotal;
}

function mbNav(dir) { mbGoTo(mbIndex + dir); }
 
// Scroll com wheel (desktop)
function mbOnWheel(e) {
    e.preventDefault();
    if (e.deltaY > 0 || e.deltaX > 0) mbNav(1);
    else mbNav(-1);
}

var mbTrack = document.getElementById('mb-track');
if (mbTrack) {
    mbTrack.addEventListener('touchstart', function(e) {
        mbTouchStartX = e.touches[0].clientX;
    }, { passive: true });
 
    mbTrack.addEventListener('touchend', function(e) {
        var diff = mbTouchStartX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) {
            if (diff > 0) mbNav(1);
            else mbNav(-1);
        }
    }, { passive: true });
}


function switchPanel(n){document.querySelectorAll('.p-qa-btn').forEach(b=>b.classList.remove('active'));document.querySelectorAll('.p-panel').forEach(p=>p.classList.remove('active'));document.getElementById('qa-'+n)?.classList.add('active');document.getElementById('panel-'+n)?.classList.add('active');}
function toggleFollow(){const b=document.getElementById('followBtn');if(!b)return;b.classList.toggle('following');b.textContent=b.classList.contains('following')?'✓ A seguir':'+ Seguir';}
function abrirDrawer(id){document.getElementById(id)?.classList.add('open');document.body.style.overflow='hidden';}
function fecharDrawer(id){document.getElementById(id)?.classList.remove('open');document.body.style.overflow='';}
function abrirModalBilhetes() {
    var m = document.getElementById('modalBilhetes');
    if (!m) return;
    mbGoTo(0);
    m.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function fecharModalBilhetes() {
    var m = document.getElementById('modalBilhetes');
    if (!m) return;
    m.style.display = 'none';
    document.body.style.overflow = '';
}

function abrirLightbox(src){document.getElementById('lightboxImg').src=src;document.getElementById('lightbox').classList.add('open');document.body.style.overflow='hidden';}
function fecharLightbox(){document.getElementById('lightbox').classList.remove('open');document.body.style.overflow='';}
document.addEventListener('keydown',function(e){if(e.key==='Escape'){document.querySelectorAll('.drawer-overlay.open').forEach(d=>d.classList.remove('open'));fecharModalBilhetes();fecharLightbox();document.body.style.overflow='';}});
</script>
@endsection
