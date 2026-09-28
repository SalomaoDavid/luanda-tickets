@extends('layouts.app')
@section('title', 'Definições — Luanda Tickets')
@section('content')

<style>
.def-wrap{max-width:640px;margin:0 auto;padding:8px 4px 60px;}

.def-eyebrow{font-size:10px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#06b6d4;margin-bottom:14px;display:flex;align-items:center;gap:8px;}
.def-eyebrow::before{content:'';width:16px;height:2px;background:#06b6d4;border-radius:1px;}
.def-title{font-size:22px;font-weight:800;color:#fff;margin-bottom:18px;}

.def-menu{display:flex;flex-direction:column;gap:10px;}
.def-menu-item{
    display:flex;align-items:center;gap:12px;
    padding:16px 18px;border-radius:16px;
    background:#0d1526;border:1px solid rgba(6,182,212,.15);
    color:#e2e8f0;width:100%;text-align:left;cursor:pointer;
    font-family:inherit;transition:all .2s;
}
.def-menu-item:hover{border-color:rgba(6,182,212,.4);background:#111c2d;}
.def-menu-icon{font-size:20px;flex-shrink:0;}
.def-menu-text{flex:1;}
.def-menu-label{font-size:14px;font-weight:700;color:#fff;}
.def-menu-sub{font-size:11.5px;color:#64748b;margin-top:2px;}
.def-menu-arrow{color:#475569;font-size:16px;flex-shrink:0;}

/* ── DRAWERS (mesmo padrão do perfil) ── */
.drawer-overlay{display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.75);backdrop-filter:blur(6px);}
.drawer-overlay.open{display:flex;align-items:flex-end;justify-content:center;}
@media(min-width:640px){.drawer-overlay.open{align-items:center;}}
.drawer-box{width:100%;background:#0d1526;border:1px solid rgba(6,182,212,.2);border-radius:24px 24px 0 0;padding:20px 20px 32px;max-height:85vh;overflow-y:auto;scrollbar-width:none;}
.drawer-box::-webkit-scrollbar{display:none;}
@media(min-width:640px){.drawer-box{border-radius:24px;max-width:520px;max-height:80vh;}}
.drawer-handle{width:36px;height:4px;border-radius:2px;background:rgba(6,182,212,.25);margin:0 auto 16px;}
@media(min-width:640px){.drawer-handle{display:none;}}
.drawer-title{font-size:16px;font-weight:800;color:#fff;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;}
.drawer-close{background:none;border:none;font-size:20px;color:#64748b;cursor:pointer;}
.drawer-close:hover{color:#fff;}

.def-block{margin-bottom:16px;}
.def-block:last-child{margin-bottom:0;}
.def-block h3{font-size:12.5px;font-weight:800;color:#22d3ee;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;}
.def-block p{font-size:13.5px;color:#cbd5e1;line-height:1.7;}

.def-values{display:flex;flex-direction:column;gap:10px;margin-top:4px;}
.def-value-item{display:flex;gap:10px;align-items:flex-start;}
.def-value-dot{width:6px;height:6px;border-radius:50%;background:#06b6d4;flex-shrink:0;margin-top:7px;}
.def-value-item strong{color:#e2e8f0;font-weight:700;}
.def-value-item span{color:#94a3b8;}

.def-textarea{width:100%;background:#0a121f;border:1px solid rgba(6,182,212,.25);border-radius:12px;padding:12px 14px;color:#e2e8f0;font-size:13.5px;font-family:inherit;resize:vertical;min-height:110px;outline:none;transition:border-color .2s;}
.def-textarea:focus{border-color:#06b6d4;}
.def-textarea::placeholder{color:#475569;}
.def-submit{margin-top:12px;padding:10px 22px;border-radius:11px;background:linear-gradient(135deg,#06b6d4,#0ea5e9);color:#fff;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:transform .15s;}
.def-submit:hover{transform:translateY(-1px);}
.def-hint{font-size:11.5px;color:#64748b;margin-top:8px;line-height:1.6;}
</style>

<div class="def-wrap">
    <div class="def-eyebrow">⚙️ Definições</div>
    <div class="def-title">Configurações</div>

    <div class="def-menu">
        <button type="button" class="def-menu-item" onclick="abrirDrawer('drawer-sobre')">
            <span class="def-menu-icon">ℹ️</span>
            <span class="def-menu-text">
                <div class="def-menu-label">Sobre Nós</div>
                <div class="def-menu-sub">Missão, visão e valores da plataforma</div>
            </span>
            <span class="def-menu-arrow">›</span>
        </button>

        <button type="button" class="def-menu-item" onclick="abrirDrawer('drawer-fale-connosco')">
            <span class="def-menu-icon">💬</span>
            <span class="def-menu-text">
                <div class="def-menu-label">Fale Connosco</div>
                <div class="def-menu-sub">Sugestões, problemas ou dúvidas</div>
            </span>
            <span class="def-menu-arrow">›</span>
        </button>
    </div>
</div>

{{-- DRAWER: SOBRE NÓS --}}
<div class="drawer-overlay" id="drawer-sobre" onclick="if(event.target===this) fecharDrawer('drawer-sobre')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">ℹ️ Sobre Nós <button class="drawer-close" onclick="fecharDrawer('drawer-sobre')">✕</button></div>

        <div class="def-block">
            <p>O Luanda Tickets nasceu para aproximar quem organiza eventos de quem quer vivê-los. Mais do que uma plataforma de venda de bilhetes, somos um espaço onde a vida social de Luanda ganha visibilidade — concertos, festas, conferências, eventos gastronómicos e tudo o que junta pessoas.</p>
        </div>

        <div class="def-block">
            <h3>Missão</h3>
            <p>Facilitar o acesso à cultura e ao entretenimento em Angola, dando a criadores de eventos as ferramentas para chegar ao seu público, e a cada pessoa a forma mais simples de descobrir e garantir o seu lugar nos eventos que lhe interessam.</p>
        </div>

        <div class="def-block">
            <h3>Visão</h3>
            <p>Ser a plataforma de referência para eventos em Angola — o primeiro sítio onde alguém procura "o que se passa esta semana em Luanda", e o primeiro sítio onde um criador de eventos pensa em publicar o seu próximo evento.</p>
        </div>

        <div class="def-block">
            <h3>Valores</h3>
            <div class="def-values">
                <div class="def-value-item">
                    <div class="def-value-dot"></div>
                    <div><strong>Confiança</strong> — <span>bilhetes seguros, pagamentos claros, sem surpresas</span></div>
                </div>
                <div class="def-value-item">
                    <div class="def-value-dot"></div>
                    <div><strong>Comunidade</strong> — <span>mais do que transações, queremos ligar pessoas através dos eventos que partilham</span></div>
                </div>
                <div class="def-value-item">
                    <div class="def-value-dot"></div>
                    <div><strong>Acessibilidade</strong> — <span>a plataforma deve ser simples de usar, para qualquer pessoa, em qualquer telemóvel</span></div>
                </div>
                <div class="def-value-item">
                    <div class="def-value-dot"></div>
                    <div><strong>Proximidade com Angola</strong> — <span>feito a pensar na realidade local, não uma cópia genérica de outra plataforma</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- DRAWER: FALE CONNOSCO --}}
<div class="drawer-overlay" id="drawer-fale-connosco" onclick="if(event.target===this) fecharDrawer('drawer-fale-connosco')">
    <div class="drawer-box">
        <div class="drawer-handle"></div>
        <div class="drawer-title">💬 Fale Connosco <button class="drawer-close" onclick="fecharDrawer('drawer-fale-connosco')">✕</button></div>

        <p style="font-size:13px;color:#94a3b8;margin-bottom:14px;line-height:1.6;">
            Tens uma sugestão de melhoria, encontraste algo que não funciona bem, ou só queres dizer-nos o que achas? Escreve aqui — a nossa equipa lê tudo.
        </p>

        <form method="POST" action="{{ route('sugestoes.guardar') }}">
            @csrf
            <textarea name="mensagem" class="def-textarea" placeholder="Escreve a tua sugestão ou comentário..." required minlength="5" maxlength="2000"></textarea>
            <button type="submit" class="def-submit">Enviar sugestão</button>
        </form>

        <p class="def-hint">Se precisares de resposta, o admin pode contactar-te diretamente pelas mensagens da plataforma.</p>
    </div>
</div>

@endsection