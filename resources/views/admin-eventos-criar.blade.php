@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
<style>
:root {
    --bg:#0d0d12;--s1:#131318;--s2:#1a1a22;--s3:#22222c;
    --b1:#ffffff0a;--b2:#ffffff16;--b3:#ffffff22;
    --acc:#a78bfa;--acc2:#7c3aed;--acc-bg:#a78bfa14;
    --green:#34d399;--red:#f87171;
    --t1:#f0ecff;--t2:#9b92b3;--t3:#5a5270;
}
*{box-sizing:border-box;margin:0;padding:0;}
.criar-page{background:var(--bg);min-height:100vh;font-family:'DM Sans',sans-serif;color:var(--t1);}

/* ── TOPBAR ── */
.criar-topbar{position:sticky;top:0;z-index:50;display:flex;align-items:center;gap:10px;padding:12px 16px;background:var(--s1);border-bottom:1px solid var(--b2);flex-wrap:wrap;}
@media(min-width:768px){.criar-topbar{gap:16px;padding:14px 32px;flex-wrap:nowrap;}}
.logo{display:flex;align-items:center;gap:9px;}
.logo-k{width:28px;height:28px;border-radius:8px;background:var(--acc2);display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-weight:800;font-size:14px;color:#fff;flex-shrink:0;}
.logo-n{font-family:'Syne',sans-serif;font-weight:700;font-size:15px;color:var(--t1);}
.tb-div{width:1px;height:16px;background:var(--b3);display:none;}
@media(min-width:640px){.tb-div{display:block;}}
.back-link{font-size:13px;color:var(--t3);text-decoration:none;transition:color .15s;display:none;}
@media(min-width:640px){.back-link{display:inline;}}
.back-link:hover{color:var(--t2);}
.page-title{font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:var(--t1);display:none;}
@media(min-width:640px){.page-title{display:block;}}
.tb-actions{margin-left:auto;display:flex;align-items:center;gap:6px;}
@media(min-width:768px){.tb-actions{gap:8px;}}
.btn-ghost{padding:7px 12px;border-radius:9px;border:1px solid var(--b2);background:transparent;font-size:12px;color:var(--t2);cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .15s;text-decoration:none;display:inline-flex;align-items:center;}
@media(min-width:768px){.btn-ghost{padding:7px 16px;font-size:13px;}}
.btn-ghost:hover{background:var(--s2);color:var(--t1);}
.btn-primary{padding:7px 14px;border-radius:9px;background:var(--acc2);border:none;font-size:12px;font-weight:500;color:#fff;cursor:pointer;font-family:'DM Sans',sans-serif;display:inline-flex;align-items:center;gap:5px;transition:opacity .15s;}
@media(min-width:768px){.btn-primary{padding:7px 20px;font-size:13px;gap:6px;}}
.btn-primary:hover{opacity:.85;}
.btn-publish{padding:7px 14px;border-radius:9px;background:var(--green);border:none;font-size:12px;font-weight:600;color:#052e16;cursor:pointer;font-family:'DM Sans',sans-serif;display:none;align-items:center;gap:5px;transition:opacity .15s;}
@media(min-width:768px){.btn-publish{padding:7px 20px;font-size:13px;gap:6px;}}
.btn-publish:hover{opacity:.85;}

/* ── STEPS ── */
.criar-steps{display:flex;align-items:center;padding:0 16px;background:var(--s1);border-bottom:1px solid var(--b1);overflow-x:auto;scrollbar-width:none;}
.criar-steps::-webkit-scrollbar{display:none;}
@media(min-width:768px){.criar-steps{padding:0 32px;}}
.step-item{display:flex;align-items:center;gap:6px;padding:11px 0;cursor:pointer;flex-shrink:0;}
@media(min-width:768px){.step-item{gap:8px;padding:13px 0;}}
.step-num{width:22px;height:22px;border-radius:50%;border:1.5px solid var(--t3);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--t3);flex-shrink:0;transition:all .2s;font-family:'Syne',sans-serif;}
@media(min-width:768px){.step-num{width:24px;height:24px;font-size:11px;}}
.step-num.active{border-color:var(--acc);color:var(--acc);background:var(--acc-bg);}
.step-num.done{border-color:var(--green);color:var(--green);background:rgba(52,211,153,.1);}
.step-lbl{font-size:11px;font-weight:600;color:var(--t3);white-space:nowrap;transition:color .2s;}
@media(min-width:768px){.step-lbl{font-size:12px;}}
.step-lbl.active{color:var(--acc);}
.step-lbl.done{color:var(--green);}
.step-line{flex:1;height:1px;background:var(--b2);margin:0 6px;min-width:12px;transition:background .2s;}
@media(min-width:768px){.step-line{margin:0 10px;min-width:16px;}}
.step-line.done{background:var(--green);}

/* ── PROGRESS ── */
.prog-wrap{display:flex;align-items:center;gap:12px;padding:7px 16px;background:var(--s1);border-bottom:1px solid var(--b1);}
@media(min-width:768px){.prog-wrap{gap:16px;padding:7px 32px;}}
.prog-track{flex:1;height:3px;border-radius:999px;background:var(--b2);overflow:hidden;}
.prog-fill{height:100%;border-radius:999px;background:var(--acc);transition:width .4s cubic-bezier(.4,0,.2,1);}
.prog-lbl{font-size:11px;color:var(--t3);white-space:nowrap;}
@media(min-width:768px){.prog-lbl{font-size:12px;}}

/* ── LAYOUT ── */
.criar-layout{display:flex;align-items:flex-start;flex-direction:column;}
@media(min-width:1024px){.criar-layout{flex-direction:row;}}
.form-col{width:100%;padding:20px 16px;min-width:0;}
@media(min-width:768px){.form-col{padding:32px;}}
@media(min-width:1024px){.form-col{flex:1;max-width:700px;}}

/* ASIDE — escondido em mobile, sticky em desktop */
.aside-col{display:none;}
@media(min-width:1024px){
    .aside-col{display:block;width:280px;flex-shrink:0;padding:32px 32px 32px 0;position:sticky;top:112px;}
}

/* ── MOBILE PREVIEW DRAWER ── */
.mob-preview-btn{
    display:flex;align-items:center;gap:8px;width:100%;padding:11px 16px;
    background:var(--s1);border:1px solid var(--b2);border-radius:12px;
    color:var(--t2);font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer;
    margin-bottom:16px; transition:all .2s;
}
.mob-preview-btn:hover{border-color:var(--acc);color:var(--acc);}
@media(min-width:1024px){.mob-preview-btn{display:none;}}

.drawer-overlay{display:none;position:fixed;inset:0;z-index:200;background:rgba(0,0,0,.75);backdrop-filter:blur(4px);}
.drawer-overlay.open{display:flex;align-items:flex-end;}
.drawer-box{width:100%;background:var(--s1);border:1px solid var(--b2);border-radius:20px 20px 0 0;padding:20px 16px 32px;max-height:85vh;overflow-y:auto;}
.drawer-handle{width:36px;height:4px;border-radius:2px;background:var(--b3);margin:0 auto 18px;}

/* ── CARDS ── */
.form-card{background:var(--s1);border:1px solid var(--b2);border-radius:14px;padding:18px;margin-bottom:14px;}
@media(min-width:768px){.form-card{border-radius:16px;padding:26px;margin-bottom:18px;}}
.form-card:last-child{margin-bottom:0;}
.card-head{display:flex;align-items:flex-start;gap:12px;margin-bottom:18px;padding-bottom:16px;border-bottom:1px solid var(--b1);}
@media(min-width:768px){.card-head{gap:14px;margin-bottom:22px;padding-bottom:18px;}}
.card-icon{width:32px;height:32px;border-radius:9px;background:var(--acc-bg);border:1px solid var(--acc2);display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;}
@media(min-width:768px){.card-icon{width:36px;height:36px;font-size:17px;}}
.card-title{font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:var(--t1);margin-bottom:2px;}
.card-sub{font-size:12px;color:var(--t3);}

/* ── FIELDS ── */
.field{margin-bottom:16px;}
.field:last-child{margin-bottom:0;}
.field label{display:block;font-size:11px;font-weight:700;color:var(--t3);letter-spacing:.07em;text-transform:uppercase;margin-bottom:6px;}
.req{color:var(--acc);}
.field input[type=text],
.field input[type=date],
.field input[type=time],
.field input[type=number],
.field input[type=url],
.field textarea,
.field select{width:100%;padding:10px 13px;background:var(--s2);border:1px solid var(--b2);border-radius:10px;font-size:13px;color:var(--t1);font-family:'DM Sans',sans-serif;outline:none;transition:border-color .15s,background .15s;-webkit-appearance:none;appearance:none;}
.field input:focus,.field textarea:focus,.field select:focus{border-color:var(--acc);background:var(--s3);}
.field input::placeholder,.field textarea::placeholder{color:var(--t3);}
.field select option{background:var(--s2);}
.field textarea{resize:vertical;min-height:90px;line-height:1.7;}
.char-count{text-align:right;font-size:11px;color:var(--t3);margin-top:4px;}
.grid2{display:grid;grid-template-columns:1fr;gap:12px;}
@media(min-width:480px){.grid2{grid-template-columns:1fr 1fr;gap:14px;}}
.error-msg{font-size:12px;color:var(--red);margin-top:5px;}

/* ── TOGGLE ── */
.tog-row{display:flex;align-items:center;justify-content:space-between;padding:11px 0;border-bottom:1px solid var(--b1);}
.tog-row:last-child{border-bottom:none;padding-bottom:0;}
.tog-info .tog-lbl{font-size:13px;color:var(--t1);}
.tog-info .tog-desc{font-size:11.5px;color:var(--t3);margin-top:2px;}
.tog-btn{width:36px;height:20px;border-radius:999px;background:var(--t3);border:none;cursor:pointer;position:relative;flex-shrink:0;transition:background .2s;margin-left:12px;}
.tog-btn.on{background:var(--acc);}
.tog-btn::after{content:'';width:14px;height:14px;border-radius:50%;background:#fff;position:absolute;top:3px;left:3px;transition:left .18s;}
.tog-btn.on::after{left:19px;}

/* ── CATEGORIAS ── */
.cat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;}
@media(min-width:480px){.cat-grid{grid-template-columns:repeat(4,1fr);gap:9px;}}
.cat-tile{padding:12px 6px;border-radius:10px;border:1px solid var(--b2);background:var(--s2);cursor:pointer;text-align:center;transition:all .18s;}
@media(min-width:768px){.cat-tile{padding:15px 7px;}}
.cat-tile:hover{border-color:var(--b3);transform:translateY(-1px);}
.cat-tile.sel{border-color:var(--acc);background:var(--acc-bg);}
.cat-em{font-size:20px;display:block;margin-bottom:5px;}
@media(min-width:768px){.cat-em{font-size:22px;}}
.cat-nm{font-size:11px;font-weight:600;color:var(--t2);font-family:'Syne',sans-serif;transition:color .15s;}
.cat-tile:hover .cat-nm,.cat-tile.sel .cat-nm{color:var(--acc);}
.sub-section{margin-top:16px;padding:16px;background:var(--s2);border-radius:10px;border:1px solid var(--b2);display:none;}
.sub-section.show{display:block;}
.sub-header{display:flex;align-items:center;gap:10px;margin-bottom:12px;}
.sub-em{font-size:18px;}
.sub-name{font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:var(--t1);}
.sub-lbl{font-size:10.5px;font-weight:700;color:var(--t3);letter-spacing:.1em;text-transform:uppercase;margin-bottom:9px;}
.sub-chips{display:flex;flex-wrap:wrap;gap:7px;}
.sub-chip{padding:6px 12px;border-radius:999px;border:1px solid var(--b2);background:var(--s1);font-size:12px;color:var(--t2);cursor:pointer;transition:all .15s;font-family:'DM Sans',sans-serif;}
.sub-chip:hover{border-color:var(--acc);color:var(--acc);}
.sub-chip.sel{background:var(--acc2);border-color:var(--acc2);color:#fff;}
.sel-confirm{margin-top:14px;padding:10px 14px;background:var(--acc-bg);border:1px solid var(--acc2);border-radius:10px;display:none;align-items:center;gap:9px;}
.sel-confirm.show{display:flex;}
.sel-check{width:18px;height:18px;border-radius:50%;background:var(--acc2);display:flex;align-items:center;justify-content:center;font-size:10px;color:#fff;flex-shrink:0;}

/* ── COVER ── */
.cover-zone{border:1.5px dashed var(--b3);border-radius:11px;cursor:pointer;overflow:hidden;transition:all .2s;}
.cover-zone:hover{border-color:var(--acc);background:var(--acc-bg);}
.cover-empty{padding:32px 20px;text-align:center;}
@media(min-width:768px){.cover-empty{padding:40px 20px;}}
.cover-icon-box{width:44px;height:44px;border-radius:12px;background:var(--s2);border:1px solid var(--b2);display:flex;align-items:center;justify-content:center;font-size:20px;margin:0 auto 10px;}
.cover-empty p{font-size:13px;color:var(--t2);margin-bottom:4px;}
.cover-empty small{font-size:11px;color:var(--t3);}
.cover-preview{height:160px;position:relative;display:none;}
@media(min-width:768px){.cover-preview{height:180px;}}
.cover-preview img{width:100%;height:100%;object-fit:cover;}
.cover-ov{position:absolute;inset:0;background:rgba(0,0,0,.55);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity .2s;}
.cover-zone:hover .cover-ov{opacity:1;}
.cover-chg{padding:8px 16px;border-radius:8px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);color:#fff;font-size:12px;font-family:'DM Sans',sans-serif;cursor:pointer;}

/* ── GALERIA ── */
.galeria-zone{border:1.5px dashed var(--b3);border-radius:11px;cursor:pointer;overflow:hidden;transition:all .2s;padding:24px 20px;text-align:center;}
@media(min-width:768px){.galeria-zone{padding:28px 20px;}}
.galeria-zone:hover{border-color:var(--acc);background:var(--acc-bg);}
.galeria-zone p{font-size:13px;color:var(--t2);margin-bottom:4px;}
.galeria-zone small{font-size:11px;color:var(--t3);}
.galeria-preview{display:flex;flex-wrap:wrap;gap:8px;margin-top:14px;}
.galeria-thumb{width:64px;height:64px;border-radius:8px;object-fit:cover;border:1px solid var(--b2);}
@media(min-width:768px){.galeria-thumb{width:72px;height:72px;}}

/* ── BILHETES ── */
.tk-headers{display:none;}
@media(min-width:640px){
    .tk-headers{display:grid;grid-template-columns:2fr 1fr 1fr 1fr 34px;gap:10px;padding-bottom:8px;margin-bottom:10px;border-bottom:1px solid var(--b2);}
    .tk-headers span{font-size:10px;font-weight:700;color:var(--t3);letter-spacing:.07em;text-transform:uppercase;}
}
.tk-row{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid var(--b1);}
@media(min-width:640px){.tk-row{grid-template-columns:2fr 1fr 1fr 1fr 34px;gap:10px;margin-bottom:9px;padding-bottom:0;border-bottom:none;align-items:center;}}
.tk-row input{padding:9px 12px;background:var(--s2);border:1px solid var(--b2);border-radius:9px;font-size:13px;color:var(--t1);font-family:'DM Sans',sans-serif;outline:none;width:100%;transition:border-color .15s;}
.tk-row input:focus{border-color:var(--acc);}
.tk-row input::placeholder{color:var(--t3);}
.tk-taxa{padding:9px 12px;background:var(--s3);border:1px solid var(--b1);border-radius:9px;font-size:12px;color:var(--t3);font-family:'DM Sans',sans-serif;text-align:center;}
.tk-row-mobile-label{font-size:10px;font-weight:700;color:var(--t3);letter-spacing:.07em;text-transform:uppercase;margin-bottom:4px;display:block;}
@media(min-width:640px){.tk-row-mobile-label{display:none;}}
.tk-nome-col{grid-column:1/-1;}
@media(min-width:640px){.tk-nome-col{grid-column:auto;}}
.rm-btn{width:34px;height:38px;border-radius:8px;border:1px solid var(--b2);background:transparent;color:var(--t3);cursor:pointer;font-size:13px;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .15s;}
.rm-btn:hover{border-color:var(--red);color:var(--red);background:rgba(248,113,113,.07);}
.rm-btn-mobile{grid-column:1/-1;justify-self:end;}
@media(min-width:640px){.rm-btn-mobile{grid-column:auto;justify-self:auto;}}
.add-tk-btn{width:100%;padding:9px;border-radius:9px;border:1.5px dashed var(--b2);background:transparent;font-size:13px;color:var(--t3);cursor:pointer;margin-top:4px;font-family:'DM Sans',sans-serif;transition:all .15s;}
.add-tk-btn:hover{border-color:var(--acc);color:var(--acc);}
.taxa-note{font-size:12px;color:var(--t3);margin-top:10px;padding:10px 14px;background:var(--s2);border-radius:8px;border:1px solid var(--b1);}

/* ── STATUS ── */
.status-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;}
.status-tile{padding:12px 8px;border-radius:10px;border:1px solid var(--b2);background:var(--s2);cursor:pointer;text-align:center;transition:all .18s;}
@media(min-width:768px){.status-tile{padding:13px 10px;}}
.status-tile:hover{border-color:var(--b3);}
.status-tile.sel-rascunho{border-color:#facc15;background:rgba(250,204,21,.08);}
.status-tile.sel-publicado{border-color:var(--green);background:rgba(52,211,153,.08);}
.status-tile.sel-encerrado{border-color:var(--red);background:rgba(248,113,113,.08);}
.status-em{font-size:18px;display:block;margin-bottom:5px;}
.status-nm{font-size:11px;font-weight:600;font-family:'Syne',sans-serif;color:var(--t2);}

/* ── REVIEW ── */
.rev-item{display:flex;align-items:center;gap:12px;padding:12px;background:var(--s2);border-radius:11px;border:1px solid var(--b2);margin-bottom:9px;}
.rev-icon{width:32px;height:32px;border-radius:9px;background:var(--acc-bg);border:1px solid var(--acc2);display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;}
.rev-body{flex:1;min-width:0;}
.rev-title{font-size:13px;font-weight:600;color:var(--t1);font-family:'Syne',sans-serif;}
.rev-val{font-size:12px;color:var(--t3);margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.rev-edit{font-size:12px;color:var(--acc);cursor:pointer;white-space:nowrap;background:none;border:none;}
.rev-edit:hover{text-decoration:underline;}
.pub-center{text-align:center;padding:20px 0 16px;}
.pub-em{font-size:40px;display:block;margin-bottom:10px;}
.pub-title{font-family:'Syne',sans-serif;font-size:18px;font-weight:800;color:var(--t1);margin-bottom:5px;}
@media(min-width:768px){.pub-title{font-size:20px;}}
.pub-sub{font-size:13px;color:var(--t3);}

/* ── ASIDE (desktop only) ── */
.aside-lbl{font-size:10px;font-weight:700;color:var(--t3);letter-spacing:.12em;text-transform:uppercase;margin-bottom:11px;}
.prev-card{background:var(--s1);border:1px solid var(--b2);border-radius:15px;overflow:hidden;}
.prev-cover{height:140px;display:flex;align-items:center;justify-content:center;font-size:50px;position:relative;background:linear-gradient(135deg,#1e1b4b,#4c1d95);}
.prev-cover-tag{position:absolute;top:9px;left:9px;background:rgba(0,0,0,.6);border:1px solid rgba(255,255,255,.12);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:5px;font-family:'Syne',sans-serif;display:none;}
.prev-body{padding:14px;}
.prev-cat{font-size:10px;font-weight:700;color:var(--acc);text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px;font-family:'Syne',sans-serif;}
.prev-title{font-family:'Syne',sans-serif;font-size:14px;font-weight:700;line-height:1.35;margin-bottom:9px;}
.prev-meta-row{display:flex;gap:6px;align-items:center;font-size:12px;color:var(--t2);margin-bottom:4px;}
.prev-divider{height:1px;background:var(--b1);margin:11px 0;}
.prev-footer{display:flex;align-items:center;justify-content:space-between;}
.prev-price{font-family:'Syne',sans-serif;font-size:15px;font-weight:800;color:var(--t1);}
.prev-price-sub{font-size:11px;color:var(--t3);}
.prev-join-btn{padding:6px 14px;border-radius:8px;background:var(--acc);border:none;font-size:12px;font-weight:600;color:#fff;cursor:pointer;font-family:'Syne',sans-serif;}
.tips-card{background:var(--s1);border:1px solid var(--b2);border-radius:15px;padding:18px;margin-top:14px;}
.tip-item{display:flex;gap:9px;align-items:flex-start;margin-bottom:11px;}
.tip-item:last-child{margin-bottom:0;}
.tip-dot{width:5px;height:5px;border-radius:50%;background:var(--acc);flex-shrink:0;margin-top:5px;}
.tip-text{font-size:12px;color:var(--t2);line-height:1.55;}

/* ── PANELS ── */
.step-panel{display:none;}
.step-panel.active{display:block;}
.alert-error{background:rgba(248,113,113,.1);border:1px solid rgba(248,113,113,.3);color:var(--red);border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;}
</style>

<style>main{max-width:100%!important;padding:0!important;}</style>

<div class="criar-page">

    {{-- TOPBAR --}}
    <div class="criar-topbar">
        <div class="logo">
            <div class="logo-k">LT</div>
            <span class="logo-n">Luanda Tickets</span>
        </div>
        <div class="tb-div"></div>
        <a href="{{ route('admin.eventos') }}" class="back-link">← Eventos</a>
        <div class="tb-div"></div>
        <span class="page-title">Criar evento</span>
        <div class="tb-actions">
            <a href="{{ route('admin.eventos') }}" class="btn-ghost">Cancelar</a>
            <button type="button" class="btn-primary" id="btn-next" onclick="nextStep()">
                <span id="btn-next-lbl">Continuar</span> →
            </button>
            <button type="submit" form="form-criar" class="btn-publish" id="btn-publish">
                🚀 Guardar
            </button>
        </div>
    </div>

    {{-- STEPS --}}
    <div class="criar-steps">
        @foreach([1=>'Informações',2=>'Categoria',3=>'Bilhetes',4=>'Definições',5=>'Publicar'] as $n=>$lbl)
            <div class="step-item" onclick="goStep({{ $n }})">
                <div class="step-num {{ $n===1?'active':'' }}" id="sn{{ $n }}">{{ $n }}</div>
                <span class="step-lbl {{ $n===1?'active':'' }}" id="sl{{ $n }}">{{ $lbl }}</span>
            </div>
            @if($n < 5)<div class="step-line" id="ln{{ $n }}"></div>@endif
        @endforeach
    </div>

    {{-- PROGRESS --}}
    <div class="prog-wrap">
        <div class="prog-track"><div class="prog-fill" id="prog-fill" style="width:20%"></div></div>
        <span class="prog-lbl" id="prog-lbl">Passo 1 de 5</span>
    </div>

    {{-- FORM --}}
    <form id="form-criar" method="POST" action="{{ route('admin.eventos.guardar') }}" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="criar-layout">

            {{-- COLUNA FORM --}}
            <div class="form-col">

                @if($errors->any())
                <div class="alert-error">
                    <strong>Corrige os seguintes erros:</strong>
                    <ul style="margin-top:6px;padding-left:16px;">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
                @endif

                {{-- Botão pré-visualização mobile --}}
                <button type="button" class="mob-preview-btn" onclick="abrirDrawerPreview()">
                    👁 Ver pré-visualização
                </button>

                {{-- ══ P1: INFORMAÇÕES ══ --}}
                <div class="step-panel active" id="p1">
                    <div class="form-card">
                        <div class="card-head">
                            <div class="card-icon">📝</div>
                            <div><div class="card-title">Informações básicas</div><div class="card-sub">O que os participantes vão ver primeiro</div></div>
                        </div>
                        <div class="field">
                            <label>Nome do evento <span class="req">*</span></label>
                            <input type="text" name="titulo" id="inp-titulo" maxlength="255" required
                                   value="{{ old('titulo') }}"
                                   placeholder="Ex: Grande Noite de Kuduro — Cine Karl Marx"
                                   oninput="syncPreview()">
                            <div class="char-count" id="titulo-count">0 / 255</div>
                            @error('titulo')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label>Descrição <span class="req">*</span></label>
                            <textarea name="descricao" id="inp-descricao" rows="4" required
                                      placeholder="Artistas, programa, o que esperar..."
                                      oninput="syncPreview()">{{ old('descricao') }}</textarea>
                            @error('descricao')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="field">
                            <label>Link externo</label>
                            <input type="url" name="link_externo" value="{{ old('link_externo') }}" placeholder="https://...">
                            @error('link_externo')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-card">
                        <div class="card-head">
                            <div class="card-icon">📅</div>
                            <div><div class="card-title">Data e hora</div><div class="card-sub">Quando é que o evento acontece?</div></div>
                        </div>
                        <div class="grid2">
                            <div class="field">
                                <label>Data de início <span class="req">*</span></label>
                                <input type="date" name="data_evento" id="inp-data" required
                                       value="{{ old('data_evento') }}" oninput="syncPreview()">
                                @error('data_evento')<div class="error-msg">{{ $message }}</div>@enderror
                            </div>
                            <div class="field">
                                <label>Data de fim</label>
                                <input type="date" name="data_fim" value="{{ old('data_fim') }}">
                            </div>
                        </div>
                        <div class="grid2">
                            <div class="field">
                                <label>Hora de início <span class="req">*</span></label>
                                <input type="time" name="hora_inicio" id="inp-hora" required
                                       value="{{ old('hora_inicio') }}" oninput="syncPreview()">
                                @error('hora_inicio')<div class="error-msg">{{ $message }}</div>@enderror
                            </div>
                            <div class="field">
                                <label>Hora de fim</label>
                                <input type="time" name="hora_fim" value="{{ old('hora_fim') }}">
                            </div>
                        </div>
                        <div class="tog-row">
                            <div class="tog-info">
                                <div class="tog-lbl">Múltiplos dias</div>
                                <div class="tog-desc">Cada dia com programação separada</div>
                            </div>
                            <button type="button" class="tog-btn {{ old('multiplos_dias') ? 'on' : '' }}"
                                    onclick="toggleField(this,'multiplos_dias')"></button>
                            <input type="hidden" name="multiplos_dias" id="multiplos_dias" value="{{ old('multiplos_dias',0) }}">
                        </div>
                    </div>

                    <div class="form-card">
                        <div class="card-head">
                            <div class="card-icon">📍</div>
                            <div><div class="card-title">Localização</div><div class="card-sub">Onde vai acontecer o evento?</div></div>
                        </div>
                        <div class="field">
                            <label>Nome do local <span class="req">*</span></label>
                            <input type="text" name="localizacao" id="inp-local" required
                                   value="{{ old('localizacao') }}"
                                   placeholder="Ex: Cine Karl Marx, Largo da Kinaxixi"
                                   oninput="syncPreview()">
                            @error('localizacao')<div class="error-msg">{{ $message }}</div>@enderror
                        </div>
                        <div class="grid2">
                            <div class="field">
                                <label>Município</label>
                                <select name="municipio">
                                    @foreach(['Luanda','Talatona','Viana','Cacuaco','Cazenga','Kilamba Kiaxi','Belas','Icolo e Bengo'] as $m)
                                        <option value="{{ $m }}" {{ old('municipio','Luanda')===$m?'selected':'' }}>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label>Província</label>
                                <select name="provincia">
                                    @foreach(['Luanda','Benguela','Huíla','Huambo','Cabinda','Namibe','Malanje','Uíge'] as $p)
                                        <option value="{{ $p }}" {{ old('provincia','Luanda')===$p?'selected':'' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="tog-row">
                            <div class="tog-info">
                                <div class="tog-lbl">Evento online</div>
                                <div class="tog-desc">Realizado via streaming ou plataforma virtual</div>
                            </div>
                            <button type="button" class="tog-btn {{ old('online') ? 'on' : '' }}"
                                    onclick="toggleField(this,'online')"></button>
                            <input type="hidden" name="online" id="online" value="{{ old('online',0) }}">
                        </div>
                    </div>
                </div>

                {{-- ══ P2: CATEGORIA + CAPA + GALERIA ══ --}}
                <div class="step-panel" id="p2">
                    <div class="form-card">
                        <div class="card-head">
                            <div class="card-icon">🗂️</div>
                            <div><div class="card-title">Categoria <span class="req">*</span></div><div class="card-sub">Clica numa categoria para ver as subcategorias</div></div>
                        </div>
                        <div class="cat-grid">
                            @foreach($categorias as $cat)
                                <div class="cat-tile {{ old('categoria_id')==$cat->id?'sel':'' }}"
                                     onclick="selectCategoria({{ $cat->id }},'{{ addslashes($cat->nome) }}',{{ $cat->subcategorias->toJson() }},this)">
                                    <span class="cat-em">{{ $cat->emoji }}</span>
                                    <span class="cat-nm">{{ $cat->nome }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="sub-section" id="sub-section">
                            <div class="sub-header">
                                <span class="sub-em" id="sub-em"></span>
                                <span class="sub-name" id="sub-name"></span>
                            </div>
                            <div class="sub-lbl">Escolhe a subcategoria</div>
                            <div class="sub-chips" id="sub-chips"></div>
                        </div>
                        <div class="sel-confirm" id="sel-confirm">
                            <div class="sel-check">✓</div>
                            <span id="sel-confirm-txt"></span>
                        </div>
                        <input type="hidden" name="categoria_id"    id="inp-categoria"    value="{{ old('categoria_id') }}">
                        <input type="hidden" name="subcategoria_id" id="inp-subcategoria" value="{{ old('subcategoria_id') }}">
                        @error('categoria_id')<div class="error-msg" style="margin-top:10px;">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-card">
                        <div class="card-head">
                            <div class="card-icon">🖼️</div>
                            <div><div class="card-title">Imagem de capa</div><div class="card-sub">A primeira impressão conta — 1200 × 630 px recomendado</div></div>
                        </div>
                        <label class="cover-zone" id="cover-zone">
                            <input type="file" name="imagem_capa" accept="image/*" style="display:none" onchange="handleCover(this)">
                            <div class="cover-empty" id="cover-empty">
                                <div class="cover-icon-box">📷</div>
                                <p>Arrasta a imagem ou clica para selecionar</p>
                                <small>PNG, JPG, WEBP · máx. 5 MB</small>
                            </div>
                            <div class="cover-preview" id="cover-preview">
                                <img id="cover-img" src="" alt="Capa">
                                <div class="cover-ov"><button type="button" class="cover-chg" onclick="event.preventDefault()">Alterar imagem</button></div>
                            </div>
                        </label>
                        @error('imagem_capa')<div class="error-msg" style="margin-top:8px;">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-card">
                        <div class="card-head">
                            <div class="card-icon">🖼️</div>
                            <div><div class="card-title">Galeria de fotos</div><div class="card-sub">Fotos adicionais para a página do evento</div></div>
                        </div>
                        <label class="galeria-zone" id="galeria-zone">
                            <input type="file" name="galeria[]" accept="image/*" multiple style="display:none" onchange="handleGaleria(this)">
                            <div class="cover-icon-box" style="margin:0 auto 12px;">🗂️</div>
                            <p>Clica para selecionar várias fotos</p>
                            <small>PNG, JPG, WEBP · máx. 5 MB por foto</small>
                        </label>
                        <div class="galeria-preview" id="galeria-preview"></div>
                        @error('galeria.*')<div class="error-msg" style="margin-top:8px;">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- ══ P3: BILHETES ══ --}}
                <div class="step-panel" id="p3">
                    <div class="form-card">
                        <div class="card-head">
                            <div class="card-icon">🎟️</div>
                            <div><div class="card-title">Tipos de bilhete</div><div class="card-sub">O preço final inclui automaticamente 10% de taxa de serviço</div></div>
                        </div>
                        <div class="tk-headers">
                            <span>Tipo</span>
                            <span>Preço base (Kz)</span>
                            <span>Taxa (10%)</span>
                            <span>Qtd.</span>
                            <span></span>
                        </div>
                        <div id="tk-list">
                            <div class="tk-row" id="tkr-0">
                                <div class="tk-nome-col">
                                    <span class="tk-row-mobile-label">Tipo de bilhete</span>
                                    <input type="text" name="ingressos[0][nome]" placeholder="Ex: Geral">
                                </div>
                                <div>
                                    <span class="tk-row-mobile-label">Preço base (Kz)</span>
                                    <input type="number" name="ingressos[0][preco]" placeholder="0" min="0" oninput="calcTaxa(this,0)">
                                </div>
                                <div>
                                    <span class="tk-row-mobile-label">Taxa (10%)</span>
                                    <div class="tk-taxa" id="taxa-0">0 Kz</div>
                                </div>
                                <div>
                                    <span class="tk-row-mobile-label">Quantidade</span>
                                    <input type="number" name="ingressos[0][quantidade]" placeholder="100" min="1">
                                </div>
                                <button type="button" class="rm-btn rm-btn-mobile" onclick="removeTicket('tkr-0')">✕</button>
                            </div>
                        </div>
                        <button type="button" class="add-tk-btn" onclick="addTicket()">+ Adicionar tipo de bilhete</button>
                        <div class="taxa-note">💡 A taxa de 10% é adicionada automaticamente ao preço que introduzires. Ex: 2.500 Kz → preço final 2.750 Kz.</div>
                    </div>

                    <div class="form-card">
                        <div class="card-head">
                            <div class="card-icon">📊</div>
                            <div><div class="card-title">Capacidade</div><div class="card-sub">Limites gerais de participação no evento</div></div>
                        </div>
                        <div class="grid2">
                            <div class="field">
                                <label>Lotação máxima <span class="req">*</span></label>
                                <input type="number" name="lotacao_maxima" id="inp-lotacao"
                                       value="{{ old('lotacao_maxima') }}" min="1" required placeholder="500"
                                       oninput="syncPreview()">
                                @error('lotacao_maxima')<div class="error-msg">{{ $message }}</div>@enderror
                            </div>
                            <div class="field">
                                <label>Ingressos por pessoa</label>
                                <input type="number" name="ingressos_por_pessoa"
                                       value="{{ old('ingressos_por_pessoa',1) }}" min="1" max="10">
                            </div>
                        </div>
                        <div class="tog-row">
                            <div class="tog-info">
                                <div class="tog-lbl">Lista de espera</div>
                                <div class="tog-desc">Aceitar inscrições após esgotamento</div>
                            </div>
                            <button type="button" class="tog-btn {{ old('lista_espera')?'on':'' }}"
                                    onclick="toggleField(this,'lista_espera')"></button>
                            <input type="hidden" name="lista_espera" id="lista_espera" value="{{ old('lista_espera',0) }}">
                        </div>
                    </div>
                </div>

                {{-- ══ P4: DEFINIÇÕES ══ --}}
                <div class="step-panel" id="p4">
                    <div class="form-card">
                        <div class="card-head">
                            <div class="card-icon">🔒</div>
                            <div><div class="card-title">Visibilidade e acesso</div><div class="card-sub">Controla quem vê e participa no evento</div></div>
                        </div>
                        @foreach([
                            ['privado','Evento privado','Visível apenas com link direto',false],
                            ['aprovacao_manual','Aprovação manual de inscrições','Confirmas cada pedido manualmente',false],
                            ['permitir_comentarios','Permitir comentários','Participantes podem comentar',true],
                            ['participantes_publicos','Lista de participantes pública','Outros utilizadores podem ver quem vai',true],
                        ] as [$field,$label,$desc,$default])
                            <div class="tog-row">
                                <div class="tog-info">
                                    <div class="tog-lbl">{{ $label }}</div>
                                    <div class="tog-desc">{{ $desc }}</div>
                                </div>
                                <button type="button" class="tog-btn {{ old($field,$default?1:0)?'on':'' }}"
                                        onclick="toggleField(this,'{{ $field }}')"></button>
                                <input type="hidden" name="{{ $field }}" id="{{ $field }}"
                                       value="{{ old($field,$default?1:0) }}">
                            </div>
                        @endforeach
                    </div>

                    <div class="form-card">
                        <div class="card-head">
                            <div class="card-icon">🔔</div>
                            <div><div class="card-title">Notificações</div><div class="card-sub">Quando queres receber alertas?</div></div>
                        </div>
                        @foreach([
                            ['notif_nova_inscricao','Nova inscrição','Alerta quando alguém se inscrever',true],
                            ['notif_lembrete_24h','Lembrete 24h antes','Envio automático aos participantes',true],
                            ['notif_resumo_semanal','Resumo semanal','Relatório de inscrições por semana',false],
                        ] as [$field,$label,$desc,$default])
                            <div class="tog-row">
                                <div class="tog-info">
                                    <div class="tog-lbl">{{ $label }}</div>
                                    <div class="tog-desc">{{ $desc }}</div>
                                </div>
                                <button type="button" class="tog-btn {{ old($field,$default?1:0)?'on':'' }}"
                                        onclick="toggleField(this,'{{ $field }}')"></button>
                                <input type="hidden" name="{{ $field }}" id="{{ $field }}"
                                       value="{{ old($field,$default?1:0) }}">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ══ P5: PUBLICAR ══ --}}
                <div class="step-panel" id="p5">
                    <div class="form-card">
                        <div class="pub-center">
                            <span class="pub-em">🚀</span>
                            <div class="pub-title">Tudo pronto!</div>
                            <div class="pub-sub">Revê os detalhes e escolhe o estado do evento</div>
                        </div>
                        <div class="rev-item">
                            <div class="rev-icon">📝</div>
                            <div class="rev-body"><div class="rev-title">Informações</div><div class="rev-val" id="rev-info">—</div></div>
                            <button type="button" class="rev-edit" onclick="goStep(1)">Editar →</button>
                        </div>
                        <div class="rev-item">
                            <div class="rev-icon">🗂️</div>
                            <div class="rev-body"><div class="rev-title">Categoria</div><div class="rev-val" id="rev-cat">—</div></div>
                            <button type="button" class="rev-edit" onclick="goStep(2)">Editar →</button>
                        </div>
                        <div class="rev-item">
                            <div class="rev-icon">🎟️</div>
                            <div class="rev-body"><div class="rev-title">Bilhetes</div><div class="rev-val" id="rev-bilhetes">—</div></div>
                            <button type="button" class="rev-edit" onclick="goStep(3)">Editar →</button>
                        </div>
                        <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--b1);">
                            <div style="font-size:11px;font-weight:700;color:var(--t3);letter-spacing:.07em;text-transform:uppercase;margin-bottom:12px;">Estado do evento</div>
                            <div class="status-grid">
                                <div class="status-tile" id="st-rascunho" onclick="selectStatus('rascunho')">
                                    <span class="status-em">📝</span>
                                    <span class="status-nm" style="color:#facc15;">Rascunho</span>
                                </div>
                                <div class="status-tile sel-publicado" id="st-publicado" onclick="selectStatus('publicado')">
                                    <span class="status-em">✅</span>
                                    <span class="status-nm" style="color:var(--green);">Publicado</span>
                                </div>
                                <div class="status-tile" id="st-encerrado" onclick="selectStatus('encerrado')">
                                    <span class="status-em">🔒</span>
                                    <span class="status-nm" style="color:var(--red);">Encerrado</span>
                                </div>
                            </div>
                            <input type="hidden" name="status" id="inp-status" value="{{ old('status','publicado') }}">
                        </div>
                        <div class="tog-row" style="margin-top:20px;padding-top:20px;border-top:1px solid var(--b1);">
                            <div class="tog-info">
                                <div class="tog-lbl">Concordo com os termos de publicação</div>
                                <div class="tog-desc">O evento cumpre todas as diretrizes da KiLuanda</div>
                            </div>
                            <button type="button" class="tog-btn" id="tog-termos"
                                    onclick="toggleField(this,'termos')"></button>
                            <input type="hidden" name="termos" id="termos" value="0">
                        </div>
                    </div>
                </div>

            </div>

            {{-- ASIDE (desktop) --}}
            <div class="aside-col">
                <div class="aside-lbl">Pré-visualização</div>
                <div class="prev-card">
                    <div class="prev-cover" id="prev-cover">
                        <span id="prev-em">✦</span>
                        <div class="prev-cover-tag" id="prev-cover-tag"></div>
                    </div>
                    <div class="prev-body">
                        <div class="prev-cat" id="prev-cat">Sem categoria</div>
                        <div class="prev-title" id="prev-title" style="color:var(--t3)">Nome do evento</div>
                        <div class="prev-meta-row"><span>📅</span><span id="prev-data" style="color:var(--t3)">Data e hora</span></div>
                        <div class="prev-meta-row"><span>📍</span><span id="prev-local" style="color:var(--t3)">Local do evento</span></div>
                        <div class="prev-divider"></div>
                        <div class="prev-footer">
                            <div>
                                <div class="prev-price" id="prev-price">—</div>
                                <div class="prev-price-sub">por pessoa</div>
                            </div>
                            <button type="button" class="prev-join-btn">Participar</button>
                        </div>
                    </div>
                </div>
                <div class="tips-card">
                    <div class="aside-lbl">💡 Dicas</div>
                    @foreach([
                        'Usa uma capa com boa iluminação e sem texto sobreposto',
                        'Menciona artistas e DJs pelo nome — aumenta o alcance',
                        'Publica com pelo menos 2 semanas de antecedência',
                        'Eventos com preço definido têm mais inscrições antecipadas',
                    ] as $tip)
                        <div class="tip-item">
                            <div class="tip-dot"></div>
                            <div class="tip-text">{{ $tip }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </form>
</div>

{{-- DRAWER PRÉ-VISUALIZAÇÃO (mobile) --}}
<div class="drawer-overlay" id="drawer-preview" onclick="if(event.target===this) fecharDrawerPreview()">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <p style="font-size:11px;font-weight:700;color:var(--t3);letter-spacing:.12em;text-transform:uppercase;margin-bottom:12px;">Pré-visualização</p>
        <div class="prev-card" style="background:var(--s2);">
            <div class="prev-cover" id="prev-cover-mob" style="background:linear-gradient(135deg,#1e1b4b,#4c1d95);">
                <span id="prev-em-mob">✦</span>
            </div>
            <div class="prev-body">
                <div class="prev-cat" id="prev-cat-mob">Sem categoria</div>
                <div class="prev-title" id="prev-title-mob" style="color:var(--t3)">Nome do evento</div>
                <div class="prev-meta-row"><span>📅</span><span id="prev-data-mob" style="color:var(--t3)">Data e hora</span></div>
                <div class="prev-meta-row"><span>📍</span><span id="prev-local-mob" style="color:var(--t3)">Local do evento</span></div>
                <div class="prev-divider"></div>
                <div class="prev-footer">
                    <div>
                        <div class="prev-price" id="prev-price-mob">—</div>
                        <div class="prev-price-sub">por pessoa</div>
                    </div>
                    <button type="button" class="prev-join-btn">Participar</button>
                </div>
            </div>
        </div>
        <button type="button" onclick="fecharDrawerPreview()" style="width:100%;margin-top:16px;padding:11px;border-radius:10px;background:var(--acc2);border:none;color:#fff;font-size:13px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;">
            Fechar
        </button>
    </div>
</div>

<script>
const CAT_CORES = {
    'Shows':        'linear-gradient(135deg,#1e1b4b,#4c1d95)',
    'Festivais':    'linear-gradient(135deg,#78350f,#d97706)',
    'Viagens':      'linear-gradient(135deg,#0c4a6e,#0284c7)',
    'Desporto':     'linear-gradient(135deg,#064e3b,#059669)',
    'Conferências': 'linear-gradient(135deg,#1e3a5f,#1d4ed8)',
    'Workshops':    'linear-gradient(135deg,#3b0764,#7e22ce)',
    'Cultura':      'linear-gradient(135deg,#4a1942,#be185d)',
};

let currentStep = 1;
let selCatNome  = '';
let selCatEmoji = '';
let selSubNome  = '';
let tkCount     = 1;

function goStep(n) {
    document.querySelectorAll('.step-panel').forEach((p,i) => p.classList.toggle('active', i+1===n));
    currentStep = n;
    for (let i=1; i<=5; i++) {
        const sn = document.getElementById('sn'+i);
        const sl = document.getElementById('sl'+i);
        const ln = document.getElementById('ln'+i);
        sn.className = 'step-num' + (i<n?' done':i===n?' active':'');
        sn.textContent = i<n ? '✓' : i;
        sl.className = 'step-lbl' + (i<n?' done':i===n?' active':'');
        if (ln) ln.className = 'step-line' + (i<n?' done':'');
    }
    document.getElementById('prog-fill').style.width = (n/5*100) + '%';
    document.getElementById('prog-lbl').textContent  = 'Passo ' + n + ' de 5';
    const btnNext    = document.getElementById('btn-next');
    const btnPublish = document.getElementById('btn-publish');
    if (n===5) {
        btnNext.style.display    = 'none';
        btnPublish.style.display = 'inline-flex';
        updateReview();
    } else {
        btnNext.style.display    = 'inline-flex';
        btnPublish.style.display = 'none';
        document.getElementById('btn-next-lbl').textContent = n===4 ? 'Rever' : 'Continuar';
    }
    window.scrollTo({top:0, behavior:'smooth'});
}

function nextStep() { if (currentStep < 5) goStep(currentStep+1); }

function toggleField(btn, field) {
    btn.classList.toggle('on');
    const el = document.getElementById(field);
    if (el) el.value = btn.classList.contains('on') ? 1 : 0;
}

function selectCategoria(id, nome, subcategorias, tile) {
    document.querySelectorAll('.cat-tile').forEach(t => t.classList.remove('sel'));
    tile.classList.add('sel');
    selCatNome  = nome;
    selCatEmoji = tile.querySelector('.cat-em').textContent;
    document.getElementById('inp-categoria').value    = id;
    document.getElementById('inp-subcategoria').value = '';
    selSubNome = '';
    const section = document.getElementById('sub-section');
    section.classList.add('show');
    document.getElementById('sub-em').textContent   = selCatEmoji;
    document.getElementById('sub-name').textContent = nome;
    const chipsEl = document.getElementById('sub-chips');
    chipsEl.innerHTML = '';
    if (subcategorias && subcategorias.length > 0) {
        subcategorias.forEach(sub => {
            const chip = document.createElement('div');
            chip.className   = 'sub-chip';
            chip.textContent = sub.nome;
            chip.onclick = () => selectSubcategoria(chip, sub.id, sub.nome);
            chipsEl.appendChild(chip);
        });
    } else {
        chipsEl.innerHTML = '<span style="font-size:13px;color:var(--t3)">Sem subcategorias</span>';
    }
    document.getElementById('sel-confirm').classList.remove('show');
    const cor = CAT_CORES[nome] || 'linear-gradient(135deg,#1e1b4b,#4c1d95)';
    document.getElementById('prev-cover').style.background     = cor;
    document.getElementById('prev-cover-mob').style.background = cor;
    document.getElementById('prev-em').textContent     = selCatEmoji;
    document.getElementById('prev-em-mob').textContent = selCatEmoji;
    document.getElementById('prev-cover-tag').textContent   = nome;
    document.getElementById('prev-cover-tag').style.display = 'block';
    document.getElementById('prev-cat').textContent     = selCatEmoji + ' ' + nome;
    document.getElementById('prev-cat-mob').textContent = selCatEmoji + ' ' + nome;
}

function selectSubcategoria(chip, id, nome) {
    document.querySelectorAll('.sub-chip').forEach(c => c.classList.remove('sel'));
    chip.classList.add('sel');
    selSubNome = nome;
    document.getElementById('inp-subcategoria').value = id;
    const confirm = document.getElementById('sel-confirm');
    confirm.classList.add('show');
    document.getElementById('sel-confirm-txt').textContent = selCatEmoji + ' ' + selCatNome + ' → ' + nome;
    document.getElementById('prev-cat').textContent     = selCatEmoji + ' ' + nome;
    document.getElementById('prev-cat-mob').textContent = selCatEmoji + ' ' + nome;
}

function handleCover(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('cover-img').src             = e.target.result;
        document.getElementById('cover-empty').style.display   = 'none';
        document.getElementById('cover-preview').style.display = 'block';
    };
    reader.readAsDataURL(file);
}

function handleGaleria(input) {
    const preview = document.getElementById('galeria-preview');
    preview.innerHTML = '';
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src       = e.target.result;
            img.className = 'galeria-thumb';
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
}

function calcTaxa(input, idx) {
    const base = parseFloat(input.value) || 0;
    const taxa = Math.round(base * 0.10);
    const el   = document.getElementById('taxa-' + idx);
    if (el) el.textContent = taxa.toLocaleString('pt-PT') + ' Kz';
    if (idx === 0) {
        const final = base + taxa;
        const priceText = final > 0 ? final.toLocaleString('pt-PT') + ' Kz' : '—';
        document.getElementById('prev-price').textContent     = priceText;
        document.getElementById('prev-price-mob').textContent = priceText;
    }
}

function addTicket() {
    const list = document.getElementById('tk-list');
    const idx  = tkCount++;
    const row  = document.createElement('div');
    row.className = 'tk-row';
    row.id        = 'tkr-' + idx;
    row.innerHTML = `
        <div class="tk-nome-col">
            <span class="tk-row-mobile-label">Tipo de bilhete</span>
            <input type="text" name="ingressos[${idx}][nome]" placeholder="Tipo de bilhete">
        </div>
        <div>
            <span class="tk-row-mobile-label">Preço base (Kz)</span>
            <input type="number" name="ingressos[${idx}][preco]" placeholder="0" min="0" oninput="calcTaxa(this,${idx})">
        </div>
        <div>
            <span class="tk-row-mobile-label">Taxa (10%)</span>
            <div class="tk-taxa" id="taxa-${idx}">0 Kz</div>
        </div>
        <div>
            <span class="tk-row-mobile-label">Quantidade</span>
            <input type="number" name="ingressos[${idx}][quantidade]" placeholder="100" min="1">
        </div>
        <button type="button" class="rm-btn rm-btn-mobile" onclick="removeTicket('tkr-${idx}')">✕</button>
    `;
    list.appendChild(row);
}

function removeTicket(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
}

function selectStatus(status) {
    ['rascunho','publicado','encerrado'].forEach(s => {
        const tile = document.getElementById('st-' + s);
        tile.className = 'status-tile' + (s === status ? ' sel-' + s : '');
    });
    document.getElementById('inp-status').value = status;
}

function syncPreview() {
    const titulo = document.getElementById('inp-titulo').value;
    const data   = document.getElementById('inp-data').value;
    const hora   = document.getElementById('inp-hora').value;
    const local  = document.getElementById('inp-local').value;
    document.getElementById('titulo-count').textContent = titulo.length + ' / 255';

    ['prev-title','prev-title-mob'].forEach(id => {
        const el = document.getElementById(id);
        el.textContent = titulo || 'Nome do evento';
        el.style.color = titulo ? 'var(--t1)' : 'var(--t3)';
    });
    if (data) {
        const d  = new Date(data + 'T00:00:00');
        const ds = d.toLocaleDateString('pt-PT', {day:'numeric',month:'short',year:'numeric'});
        const txt = ds + (hora ? ' · ' + hora : '');
        ['prev-data','prev-data-mob'].forEach(id => {
            const el = document.getElementById(id);
            el.textContent = txt; el.style.color = 'var(--t2)';
        });
    }
    if (local) {
        ['prev-local','prev-local-mob'].forEach(id => {
            const el = document.getElementById(id);
            el.textContent = local; el.style.color = 'var(--t2)';
        });
    }
}

function updateReview() {
    const titulo = document.getElementById('inp-titulo').value;
    const data   = document.getElementById('inp-data').value;
    document.getElementById('rev-info').textContent =
        titulo ? titulo.slice(0,40) + (titulo.length>40?'…':'') + (data?' · '+data:'') : 'Preenche no passo 1';
    document.getElementById('rev-cat').textContent =
        selCatNome ? selCatEmoji+' '+selCatNome+(selSubNome?' → '+selSubNome:'') : 'Preenche no passo 2';
    const rows = document.querySelectorAll('#tk-list .tk-row');
    document.getElementById('rev-bilhetes').textContent =
        rows.length > 0 ? rows.length + ' tipo(s) configurado(s)' : 'Nenhum bilhete adicionado';
}

function abrirDrawerPreview()  { document.getElementById('drawer-preview').classList.add('open'); }
function fecharDrawerPreview() { document.getElementById('drawer-preview').classList.remove('open'); }

document.getElementById('form-criar').addEventListener('submit', function(e) {
    if (document.getElementById('termos').value !== '1') {
        e.preventDefault();
        alert('Tens de aceitar os termos de publicação para continuar.');
        return;
    }
    if (!document.getElementById('inp-categoria').value) {
        e.preventDefault();
        goStep(2);
        alert('Seleciona uma categoria para o evento.');
    }
});
</script>
@endsection