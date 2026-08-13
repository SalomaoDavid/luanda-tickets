@extends('layouts.app')

@section('title', 'Scanner de Acesso — Luanda Tickets')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">

{{-- html5-qrcode: para câmera ao vivo --}}
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
{{-- jsQR: para leitura de imagens e PDFs estáticos --}}
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
{{-- PDF.js para converter PDFs em imagem --}}
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>

<style>
:root {
  --bg:#04060d;--s1:#080d1a;--s2:#0d1426;--s3:#111c35;
  --cyan:#00d4ff;--cyan2:#0ea5e9;--green:#00ff88;--red:#ff2d55;
  --gold:#ffbe00;--purple:#bf5af2;--text:#e8edf5;
  --muted:#4a5568;--muted2:#718096;
  --border:rgba(0,212,255,0.12);--border2:rgba(0,212,255,0.28);
}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Outfit',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;}
.mono{font-family:'JetBrains Mono',monospace;}

.sc-bg{position:fixed;inset:0;z-index:0;pointer-events:none;background:radial-gradient(ellipse at 20% 20%,rgba(0,212,255,.06) 0%,transparent 50%),radial-gradient(ellipse at 80% 80%,rgba(191,90,242,.05) 0%,transparent 50%);}
.sc-grid{position:fixed;inset:0;z-index:0;pointer-events:none;opacity:.025;background-image:linear-gradient(rgba(0,212,255,1) 1px,transparent 1px),linear-gradient(90deg,rgba(0,212,255,1) 1px,transparent 1px);background-size:36px 36px;}

.sc-wrap{position:relative;z-index:1;max-width:520px;margin:0 auto;padding:24px 16px 60px;}

.sc-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;}
.sc-logo{display:flex;align-items:center;gap:10px;}
.sc-logo-icon{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--cyan),var(--cyan2));display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:900;color:#000;box-shadow:0 0 20px rgba(0,212,255,.4);}
.sc-logo-text{font-family:'JetBrains Mono',monospace;font-size:13px;font-weight:800;color:var(--text);letter-spacing:1px;}
.sc-logo-text span{color:var(--cyan);}
.sc-back-btn{display:flex;align-items:center;gap:6px;padding:7px 14px;border-radius:10px;background:var(--s2);border:1px solid var(--border2);color:var(--muted2);font-size:12px;font-weight:600;text-decoration:none;transition:all .2s;}
.sc-back-btn:hover{color:var(--text);border-color:var(--cyan);}

.sc-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:20px;}
.sc-stat{background:var(--s1);border:1px solid var(--border);border-radius:14px;padding:12px 10px;text-align:center;}
.sc-stat-num{font-family:'JetBrains Mono',monospace;font-size:22px;font-weight:800;line-height:1;margin-bottom:3px;}
.sc-stat-lbl{font-size:9px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:1px;}

.sc-box{background:var(--s1);border:1px solid var(--border2);border-radius:24px;padding:24px;margin-bottom:16px;position:relative;overflow:hidden;}
.sc-box-glow{position:absolute;inset:0;pointer-events:none;background:radial-gradient(ellipse at 50% 0%,rgba(0,212,255,.06) 0%,transparent 60%);}

/* Frame decorativo */
.sc-qr-frame{position:relative;width:100%;margin:0 auto 20px;}
.sc-qr-corners{position:relative;padding:14px;}
.sc-qr-corner{position:absolute;width:24px;height:24px;}
.sc-qr-corner.tl{top:0;left:0;border-top:3px solid var(--cyan);border-left:3px solid var(--cyan);border-radius:4px 0 0 0;}
.sc-qr-corner.tr{top:0;right:0;border-top:3px solid var(--cyan);border-right:3px solid var(--cyan);border-radius:0 4px 0 0;}
.sc-qr-corner.bl{bottom:0;left:0;border-bottom:3px solid var(--cyan);border-left:3px solid var(--cyan);border-radius:0 0 0 4px;}
.sc-qr-corner.br{bottom:0;right:0;border-bottom:3px solid var(--cyan);border-right:3px solid var(--cyan);border-radius:0 0 4px 0;}

/* Área do scanner html5-qrcode */
#qr-reader{width:100%;border-radius:12px;overflow:hidden;background:var(--s2);}
/* Esconder elementos internos feios do html5-qrcode */
#qr-reader__header_message{display:none!important;}
#qr-reader__status_span{color:var(--cyan)!important;font-size:11px!important;font-family:'Outfit',sans-serif!important;}
#qr-reader__dashboard_section_csr button{
    background:linear-gradient(135deg,var(--cyan),var(--cyan2))!important;
    color:#000!important;font-weight:800!important;border:none!important;
    border-radius:10px!important;padding:10px 20px!important;cursor:pointer!important;
    font-family:'Outfit',sans-serif!important;font-size:12px!important;
}
#qr-reader__camera_permission_button{
    background:linear-gradient(135deg,var(--cyan),var(--cyan2))!important;
    color:#000!important;font-weight:800!important;border:none!important;
    border-radius:10px!important;padding:10px 20px!important;cursor:pointer!important;
}
#qr-reader select{
    background:var(--s2)!important;color:var(--text)!important;
    border:1px solid var(--border2)!important;border-radius:8px!important;padding:6px!important;
}
#qr-reader img[alt="Info icon"]{display:none!important;}
#qr-reader__scan_region{border-radius:8px!important;overflow:hidden!important;}
#qr-reader__scan_region img{display:none!important;}

.sc-scan-line{
    position:absolute;left:14px;right:14px;
    height:2px;background:var(--cyan);
    box-shadow:0 0 12px var(--cyan),0 0 24px rgba(0,212,255,.5);
    animation:scanLine 2.5s ease-in-out infinite;border-radius:1px;
    pointer-events:none;z-index:10;
}
@keyframes scanLine{0%{top:14px;opacity:1}50%{top:calc(100% - 16px);opacity:.8}100%{top:14px;opacity:1}}

.sc-input-wrap{position:relative;margin-bottom:12px;margin-top:16px;}
.sc-input-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:14px;pointer-events:none;}
.sc-input{width:100%;background:var(--s2);border:1.5px solid var(--border2);border-radius:12px;padding:13px 14px 13px 40px;color:var(--text);font-size:13px;font-weight:600;font-family:'JetBrains Mono',monospace;outline:none;transition:all .2s;letter-spacing:1px;text-transform:uppercase;}
.sc-input:focus{border-color:var(--cyan);box-shadow:0 0 0 3px rgba(0,212,255,.1);}
.sc-input::placeholder{color:var(--muted);font-size:11px;}

.sc-btn-primary{width:100%;padding:14px;border-radius:14px;background:linear-gradient(135deg,var(--cyan),var(--cyan2));color:#000;font-size:13px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;border:none;cursor:pointer;transition:all .2s;box-shadow:0 4px 20px rgba(0,212,255,.35);margin-bottom:8px;}
.sc-btn-primary:hover{transform:translateY(-1px);box-shadow:0 6px 28px rgba(0,212,255,.5);}
.sc-btn-camera{width:100%;padding:12px;border-radius:14px;background:var(--s2);border:1.5px solid var(--border2);color:var(--muted2);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;}
.sc-btn-camera:hover{border-color:var(--cyan);color:var(--cyan);}
.sc-btn-camera.ativo{background:rgba(255,45,85,.1);border-color:rgba(255,45,85,.4);color:var(--red);}
.sc-btn-camera .pulse{width:7px;height:7px;border-radius:50%;background:var(--red);animation:blink 1s infinite;}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}
.sc-btn-upload{width:100%;padding:12px;border-radius:14px;background:var(--s2);border:1.5px solid var(--border2);color:var(--muted2);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px;margin-top:8px;position:relative;overflow:hidden;}
.sc-btn-upload:hover{border-color:var(--purple);color:var(--purple);}
.sc-btn-upload input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}

.sc-result{border-radius:20px;padding:20px;margin-bottom:16px;display:none;border:1px solid;animation:popIn .3s ease;}
@keyframes popIn{from{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}
.sc-result.success{background:rgba(0,255,136,.08);border-color:rgba(0,255,136,.3);}
.sc-result.error{background:rgba(255,45,85,.08);border-color:rgba(255,45,85,.3);}
.sc-result.warning{background:rgba(255,190,0,.08);border-color:rgba(255,190,0,.3);}
.sc-result-header{display:flex;align-items:center;gap:12px;margin-bottom:14px;}
.sc-result-icon{font-size:28px;}
.sc-result-title{font-size:16px;font-weight:800;}
.sc-result-sub{font-size:11px;font-weight:600;opacity:.7;margin-top:2px;}
.sc-result.success .sc-result-title{color:var(--green);}
.sc-result.error .sc-result-title{color:var(--red);}
.sc-result.warning .sc-result-title{color:var(--gold);}
.sc-result-info{display:flex;flex-direction:column;gap:8px;}
.sc-result-row{display:flex;justify-content:space-between;align-items:center;padding:9px 12px;border-radius:10px;background:rgba(255,255,255,.04);font-size:12px;}
.sc-result-row-lbl{color:var(--muted2);font-weight:600;}
.sc-result-row-val{color:var(--text);font-weight:700;text-align:right;}

.sc-loading{display:none;text-align:center;padding:20px 0;}
.sc-loading-spinner{width:32px;height:32px;border:2px solid var(--border2);border-top-color:var(--cyan);border-radius:50%;animation:spin .7s linear infinite;margin:0 auto 10px;}
@keyframes spin{to{transform:rotate(360deg)}}
.sc-loading-text{font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:1px;}

.sc-history{margin-top:4px;}
.sc-history-title{font-size:10px;font-weight:800;color:var(--muted);text-transform:uppercase;letter-spacing:2px;margin-bottom:10px;display:flex;align-items:center;justify-content:space-between;}
.sc-history-clear{font-size:10px;color:var(--red);cursor:pointer;font-weight:700;background:none;border:none;padding:0;font-family:'Outfit',sans-serif;}
.sc-history-list{display:flex;flex-direction:column;gap:6px;}
.sc-history-item{display:flex;align-items:center;justify-content:space-between;background:var(--s1);border:1px solid var(--border);border-radius:12px;padding:10px 14px;animation:fadeIn .3s ease;}
@keyframes fadeIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
.sc-history-code{font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:700;color:var(--text);}
.sc-history-name{font-size:10px;color:var(--muted2);margin-top:2px;}
.sc-history-right{display:flex;align-items:center;gap:8px;}
.sc-history-time{font-size:9px;color:var(--muted);font-family:'JetBrains Mono',monospace;}
.sc-badge{font-size:9px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;padding:3px 8px;border-radius:6px;}
.sc-badge.ok{background:rgba(0,255,136,.15);color:var(--green);border:1px solid rgba(0,255,136,.3);}
.sc-badge.err{background:rgba(255,45,85,.15);color:var(--red);border:1px solid rgba(255,45,85,.3);}
.sc-badge.warn{background:rgba(255,190,0,.15);color:var(--gold);border:1px solid rgba(255,190,0,.3);}
</style>

<div class="sc-bg"></div>
<div class="sc-grid"></div>

<div class="sc-wrap">

    <div class="sc-header">
        <div class="sc-logo">
            <div class="sc-logo-icon">LT</div>
            <div class="sc-logo-text">Validator<span>PRO</span></div>
        </div>
        <a href="{{ route('admin.eventos') }}" class="sc-back-btn">← Voltar</a>
    </div>

    <div class="sc-stats">
        <div class="sc-stat"><div class="sc-stat-num" id="statTotal" style="color:var(--cyan)">0</div><div class="sc-stat-lbl">Validados</div></div>
        <div class="sc-stat"><div class="sc-stat-num" id="statOk" style="color:var(--green)">0</div><div class="sc-stat-lbl">Aceites</div></div>
        <div class="sc-stat"><div class="sc-stat-num" id="statErr" style="color:var(--red)">0</div><div class="sc-stat-lbl">Inválidos</div></div>
    </div>

    <div class="sc-box">
        <div class="sc-box-glow"></div>

        {{-- Frame decorativo + scanner html5-qrcode --}}
        <div class="sc-qr-frame">
            <div class="sc-qr-corners">
                <div class="sc-qr-corner tl"></div>
                <div class="sc-qr-corner tr"></div>
                <div class="sc-qr-corner bl"></div>
                <div class="sc-qr-corner br"></div>
                <div id="qr-reader"></div>
            </div>
            <div class="sc-scan-line" id="scanLine"></div>
        </div>

        <div class="sc-input-wrap">
            <span class="sc-input-icon">🔍</span>
            <input type="text" id="codigoInput" class="sc-input"
                   placeholder="Ou digita o código manualmente..."
                   autocomplete="off" autocorrect="off" spellcheck="false">
        </div>

        <div class="sc-loading" id="loadingState">
            <div class="sc-loading-spinner"></div>
            <div class="sc-loading-text">A validar bilhete...</div>
        </div>

        <div class="sc-result" id="resultBox">
            <div class="sc-result-header">
                <div class="sc-result-icon" id="resultIcon"></div>
                <div>
                    <div class="sc-result-title" id="resultTitle"></div>
                    <div class="sc-result-sub" id="resultSub"></div>
                </div>
            </div>
            <div class="sc-result-info" id="resultInfo"></div>
        </div>

        <button class="sc-btn-primary" id="btnValidar" onclick="validarCodigo()">
            ✓ Verificar Acesso
        </button>
        <button class="sc-btn-camera" id="btnCamera" onclick="toggleCamera()">
            <span class="pulse"></span>
            <span id="cameraBtnText">Ativar Câmera (Scanner QR)</span>
        </button>

        {{-- Upload de imagem com QR Code --}}
        <div class="sc-btn-upload" title="Carregar imagem ou PDF com QR Code">
            <input type="file" accept="image/*,application/pdf" id="uploadQR" onchange="lerImagemQR(this)">
            🖼 Carregar Imagem / PDF com QR Code
        </div>
    </div>

    <div class="sc-history">
        <div class="sc-history-title">
            Últimas Validações
            <button class="sc-history-clear" onclick="limparHistorico()">Limpar</button>
        </div>
        <div class="sc-history-list" id="historyList">
            <div style="text-align:center;padding:20px 0;font-size:11px;color:var(--muted);">
                Nenhuma validação ainda nesta sessão
            </div>
        </div>
    </div>
</div>

<script>
const CSRF    = '{{ csrf_token() }}';
const API_URL = '{{ route("admin.scanner.validar") }}';

let stats     = { total: 0, ok: 0, err: 0 };
let historico = [];
let cameraAtiva = false;
let html5QrCode = null;

// ── Validar por código manual ─────────────────────────────
async function validarCodigo() {
    const codigo = document.getElementById('codigoInput').value.trim();
    if (!codigo) {
        mostrarResultado('error', '⚠️', 'Campo vazio', 'Digita ou lê um código QR primeiro.', []);
        return;
    }
    await enviarValidacao(codigo);
}

// ── Enviar para o servidor ────────────────────────────────
async function enviarValidacao(codigo) {
    mostrarLoading(true);
    esconderResultado();

    try {
        const resp = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ codigo })
        });
        const data = await resp.json();
        mostrarLoading(false);

        if (data.status === 'success') {
            mostrarResultado('success', '✅', 'Entrada Autorizada!', data.message, [
                { lbl: 'Cliente', val: data.cliente ?? '—' },
                { lbl: 'Evento',  val: data.evento  ?? '—' },
                { lbl: 'Local',   val: data.local   ?? '—' },
                { lbl: 'Data',    val: data.data    ?? '—' },
                { lbl: 'Hora',    val: data.hora    ?? '—' },
                { lbl: 'Tipo',    val: data.tipo    ?? '—' },
                { lbl: 'Preço',   val: data.preco   ?? '—' },
                { lbl: 'Código',  val: data.codigo  ?? '—' },
            ]);
            adicionarHistorico(codigo, data.cliente ?? 'Convidado', 'ok');
            stats.ok++;
            vibrar([200]);
        } else if (data.status === 'warning') {
            mostrarResultado('warning', '⚠️', 'Bilhete Já Utilizado', data.message, [{ lbl: 'Código', val: codigo }]);
            adicionarHistorico(codigo, 'Já utilizado', 'warn');
            stats.err++;
            vibrar([100, 50, 100]);
        } else {
            mostrarResultado('error', '❌', 'Acesso Negado', data.message, [{ lbl: 'Código', val: codigo }]);
            adicionarHistorico(codigo, 'Inválido', 'err');
            stats.err++;
            vibrar([300]);
        }

        stats.total++;
        atualizarStats();
        document.getElementById('codigoInput').value = '';

    } catch (e) {
        mostrarLoading(false);
        mostrarResultado('error', '🔌', 'Erro de conexão', 'Não foi possível contactar o servidor.', []);
    }
}

// ── Câmera com html5-qrcode ───────────────────────────────
async function toggleCamera() {
    if (cameraAtiva) {
        await pararCamera();
    } else {
        await iniciarCamera();
    }
}

async function iniciarCamera() {
    const btn = document.getElementById('btnCamera');
    const scanLine = document.getElementById('scanLine');

    try {
        html5QrCode = new Html5Qrcode('qr-reader');

        const config = {
            fps: 15,
            qrbox: { width: 220, height: 220 },
            aspectRatio: 1.0,
            experimentalFeatures: { useBarCodeDetectorIfSupported: true }
        };

        await html5QrCode.start(
            { facingMode: 'environment' }, // câmera traseira
            config,
            async (decodedText) => {
                // QR lido com sucesso — para câmera e valida
                await pararCamera();
                document.getElementById('codigoInput').value = decodedText;
                await enviarValidacao(decodedText);
            },
            (errorMessage) => {
                // Erros de leitura são normais — ignorar
            }
        );

        cameraAtiva = true;
        btn.classList.add('ativo');
        document.getElementById('cameraBtnText').textContent = '⏹ Parar Câmera';
        scanLine.style.display = 'block';

    } catch (err) {
        mostrarResultado('error', '📷', 'Câmera indisponível',
            'Não foi possível aceder à câmera: ' + (err.message || err), []);
    }
}

async function pararCamera() {
    const btn = document.getElementById('btnCamera');
    const scanLine = document.getElementById('scanLine');

    if (html5QrCode) {
        try {
            await html5QrCode.stop();
            html5QrCode.clear();
        } catch (e) {}
        html5QrCode = null;
    }

    cameraAtiva = false;
    btn.classList.remove('ativo');
    document.getElementById('cameraBtnText').textContent = 'Ativar Câmera (Scanner QR)';
    scanLine.style.display = 'none';
}

// ── UI Helpers ────────────────────────────────────────────
function mostrarLoading(show) {
    document.getElementById('loadingState').style.display = show ? 'block' : 'none';
    document.getElementById('btnValidar').disabled = show;
}
function esconderResultado() {
    document.getElementById('resultBox').style.display = 'none';
}
function mostrarResultado(tipo, icon, titulo, sub, rows) {
    const box = document.getElementById('resultBox');
    box.className = 'sc-result ' + tipo;
    box.style.display = 'block';
    document.getElementById('resultIcon').textContent  = icon;
    document.getElementById('resultTitle').textContent = titulo;
    document.getElementById('resultSub').textContent   = sub;
    document.getElementById('resultInfo').innerHTML = rows.map(r =>
        `<div class="sc-result-row"><span class="sc-result-row-lbl">${r.lbl}</span><span class="sc-result-row-val mono">${r.val}</span></div>`
    ).join('');
}
function atualizarStats() {
    document.getElementById('statTotal').textContent = stats.total;
    document.getElementById('statOk').textContent    = stats.ok;
    document.getElementById('statErr').textContent   = stats.err;
}
function adicionarHistorico(codigo, nome, tipo) {
    historico.unshift({ codigo, nome, tipo, hora: new Date().toLocaleTimeString('pt-PT', { hour:'2-digit', minute:'2-digit', second:'2-digit' }) });
    renderHistorico();
}
function renderHistorico() {
    const list = document.getElementById('historyList');
    if (!historico.length) {
        list.innerHTML = '<div style="text-align:center;padding:20px 0;font-size:11px;color:var(--muted);">Nenhuma validação ainda nesta sessão</div>';
        return;
    }
    list.innerHTML = historico.slice(0, 10).map(h =>
        `<div class="sc-history-item">
            <div><div class="sc-history-code">${h.codigo.substring(0,18)}${h.codigo.length>18?'…':''}</div><div class="sc-history-name">${h.nome}</div></div>
            <div class="sc-history-right"><span class="sc-history-time">${h.hora}</span><span class="sc-badge ${h.tipo}">${h.tipo==='ok'?'OK':h.tipo==='warn'?'USADO':'ERRO'}</span></div>
        </div>`
    ).join('');
}
function limparHistorico() {
    historico = []; stats = { total:0, ok:0, err:0 };
    atualizarStats(); renderHistorico(); esconderResultado();
}
function vibrar(pattern) {
    if (navigator.vibrate) navigator.vibrate(pattern);
}

document.getElementById('codigoInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') validarCodigo();
});

// ── Upload de imagem ou PDF com QR Code ──────────────────
async function lerImagemQR(input) {
    const file = input.files[0];
    if (!file) return;

    if (cameraAtiva) await pararCamera();
    mostrarLoading(true);
    esconderResultado();

    try {
        // Converter ficheiro para canvas
        const canvas  = file.type === 'application/pdf'
            ? await pdfParaCanvas(file)
            : await imagemParaCanvas(file);

        // Procurar QR em múltiplas regiões com jsQR
        const resultado = procurarQRnoCanvas(canvas);

        mostrarLoading(false);

        if (resultado) {
            document.getElementById('codigoInput').value = resultado;
            await enviarValidacao(resultado);
        } else {
            mostrarResultado('error', '🖼', 'QR não detectado',
                'Não foi possível encontrar o QR Code. Tenta uma imagem mais nítida ou digita o código manualmente.', []);
        }

    } catch (err) {
        mostrarLoading(false);
        mostrarResultado('error', '📄', 'Erro ao processar ficheiro',
            'Ocorreu um erro: ' + err.message, []);
    }

    input.value = '';
}

// ── Ler QR com jsQR directamente no canvas ───────────────
// Mais fiável que Html5Qrcode para imagens estáticas
function lerQRdoCanvas(canvas) {
    const ctx       = canvas.getContext('2d');
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    // Tenta com e sem inversão
    const resultado =
        jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'attemptBoth' });
    return resultado ? resultado.data : null;
}

// ── Recortar região do canvas ─────────────────────────────
function recortarCanvas(src, x, y, w, h) {
    const c = document.createElement('canvas');
    c.width = w; c.height = h;
    c.getContext('2d').drawImage(src, x, y, w, h, 0, 0, w, h);
    return c;
}

// ── Procurar QR por regiões no canvas ────────────────────
function procurarQRnoCanvas(canvas) {
    const W = canvas.width;
    const H = canvas.height;
    const q = Math.floor(Math.min(W, H) * 0.45); // 45% do menor lado

    // Ordem: inteira → cantos → faixas
    const regioes = [
        { x: 0,     y: 0,     w: W,     h: H     }, // imagem inteira
        { x: W - q, y: 0,     w: q,     h: q     }, // canto superior direito
        { x: 0,     y: 0,     w: q,     h: q     }, // canto superior esquerdo
        { x: W - q, y: H - q, w: q,     h: q     }, // canto inferior direito
        { x: 0,     y: H - q, w: q,     h: q     }, // canto inferior esquerdo
        { x: 0,     y: 0,     w: W,     h: H / 2 }, // metade superior
        { x: W / 2, y: 0,     w: W / 2, h: H     }, // metade direita
        { x: 0,     y: 0,     w: W / 2, h: H     }, // metade esquerda
    ];

    for (const r of regioes) {
        const recorte = recortarCanvas(canvas, r.x, r.y, r.w, r.h);
        const qr      = lerQRdoCanvas(recorte);
        if (qr) return qr;
    }
    return null;
}

// ── Imagem → canvas ───────────────────────────────────────
function imagemParaCanvas(file) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        const url = URL.createObjectURL(file);
        img.onload = () => {
            URL.revokeObjectURL(url);
            const canvas = document.createElement('canvas');
            // Escala para pelo menos 1200px no lado maior (melhor leitura)
            const escala  = Math.max(1, 1200 / Math.max(img.naturalWidth, img.naturalHeight));
            canvas.width  = img.naturalWidth  * escala;
            canvas.height = img.naturalHeight * escala;
            canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
            resolve(canvas);
        };
        img.onerror = reject;
        img.src = url;
    });
}

// ── PDF → canvas (página 1) ───────────────────────────────
async function pdfParaCanvas(file) {
    pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
    const buffer   = await file.arrayBuffer();
    const pdf      = await pdfjsLib.getDocument({ data: buffer }).promise;
    const page     = await pdf.getPage(1);
    const viewport = page.getViewport({ scale: 3.0 });
    const canvas   = document.createElement('canvas');
    canvas.width   = viewport.width;
    canvas.height  = viewport.height;
    await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
    return canvas;
}

// Ocultar linha de scan inicialmente
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('scanLine').style.display = 'none';
    document.getElementById('codigoInput').focus();
});
</script>

@endsection