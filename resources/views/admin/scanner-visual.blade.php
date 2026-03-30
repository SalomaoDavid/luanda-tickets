@extends('layouts.app')

@section('title', 'Scanner de Acesso — Luanda Tickets')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700;800&family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">

<style>
:root {
  --bg:      #04060d;
  --s1:      #080d1a;
  --s2:      #0d1426;
  --s3:      #111c35;
  --cyan:    #00d4ff;
  --cyan2:   #0ea5e9;
  --green:   #00ff88;
  --red:     #ff2d55;
  --gold:    #ffbe00;
  --purple:  #bf5af2;
  --text:    #e8edf5;
  --muted:   #4a5568;
  --muted2:  #718096;
  --border:  rgba(0,212,255,0.12);
  --border2: rgba(0,212,255,0.28);
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'Outfit', sans-serif;
  background: var(--bg);
  color: var(--text);
  min-height: 100vh;
}

.mono { font-family: 'JetBrains Mono', monospace; }

/* ── BACKGROUND ── */
.sc-bg {
  position: fixed; inset: 0; z-index: 0; pointer-events: none;
  background:
    radial-gradient(ellipse at 20% 20%, rgba(0,212,255,.06) 0%, transparent 50%),
    radial-gradient(ellipse at 80% 80%, rgba(191,90,242,.05) 0%, transparent 50%);
}
.sc-grid {
  position: fixed; inset: 0; z-index: 0; pointer-events: none;
  opacity: .025;
  background-image:
    linear-gradient(rgba(0,212,255,1) 1px, transparent 1px),
    linear-gradient(90deg, rgba(0,212,255,1) 1px, transparent 1px);
  background-size: 36px 36px;
}

/* ── WRAPPER ── */
.sc-wrap {
  position: relative; z-index: 1;
  max-width: 520px; margin: 0 auto;
  padding: 20px 16px 60px;
}

/* ── HEADER ── */
.sc-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 28px;
}
.sc-logo {
  display: flex; align-items: center; gap: 10px;
}
.sc-logo-icon {
  width: 36px; height: 36px; border-radius: 10px;
  background: linear-gradient(135deg, var(--cyan), var(--cyan2));
  display: flex; align-items: center; justify-content: center;
  font-size: 16px; font-weight: 900; color: #000;
  box-shadow: 0 0 20px rgba(0,212,255,.4);
}
.sc-logo-text {
  font-family: 'JetBrains Mono', monospace;
  font-size: 13px; font-weight: 800;
  color: var(--text); letter-spacing: 1px;
}
.sc-logo-text span { color: var(--cyan); }
.sc-header-right {
  display: flex; align-items: center; gap: 8px;
}
.sc-back-btn {
  display: flex; align-items: center; gap: 6px;
  padding: 7px 14px; border-radius: 10px;
  background: var(--s2); border: 1px solid var(--border2);
  color: var(--muted2); font-size: 12px; font-weight: 600;
  text-decoration: none; transition: all .2s;
}
.sc-back-btn:hover { color: var(--text); border-color: var(--cyan); }

/* ── STATS ROW ── */
.sc-stats {
  display: grid; grid-template-columns: repeat(3, 1fr);
  gap: 8px; margin-bottom: 20px;
}
.sc-stat {
  background: var(--s1); border: 1px solid var(--border);
  border-radius: 14px; padding: 12px 10px; text-align: center;
  transition: border-color .2s;
}
.sc-stat-num {
  font-family: 'JetBrains Mono', monospace;
  font-size: 22px; font-weight: 800; line-height: 1; margin-bottom: 3px;
}
.sc-stat-lbl { font-size: 9px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; }

/* ── SCANNER BOX ── */
.sc-box {
  background: var(--s1); border: 1px solid var(--border2);
  border-radius: 24px; padding: 24px; margin-bottom: 16px;
  position: relative; overflow: hidden;
}
.sc-box-glow {
  position: absolute; inset: 0; pointer-events: none;
  background: radial-gradient(ellipse at 50% 0%, rgba(0,212,255,.06) 0%, transparent 60%);
}

/* QR Frame */
.sc-qr-frame {
  position: relative; width: 200px; height: 200px;
  margin: 0 auto 20px;
}
.sc-qr-corner {
  position: absolute; width: 24px; height: 24px;
}
.sc-qr-corner.tl { top:0; left:0;  border-top: 3px solid var(--cyan); border-left:  3px solid var(--cyan); border-radius: 4px 0 0 0; }
.sc-qr-corner.tr { top:0; right:0; border-top: 3px solid var(--cyan); border-right: 3px solid var(--cyan); border-radius: 0 4px 0 0; }
.sc-qr-corner.bl { bottom:0; left:0;  border-bottom: 3px solid var(--cyan); border-left:  3px solid var(--cyan); border-radius: 0 0 0 4px; }
.sc-qr-corner.br { bottom:0; right:0; border-bottom: 3px solid var(--cyan); border-right: 3px solid var(--cyan); border-radius: 0 0 4px 0; }
.sc-qr-inner {
  position: absolute; inset: 14px;
  border-radius: 8px; overflow: hidden;
  background: var(--s2);
  display: flex; align-items: center; justify-content: center;
}
.sc-qr-placeholder { text-align: center; }
.sc-qr-placeholder svg { opacity: .2; margin-bottom: 6px; }
.sc-qr-placeholder p { font-size: 9px; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
.sc-scan-line {
  position: absolute; left: 14px; right: 14px;
  height: 2px; background: var(--cyan);
  box-shadow: 0 0 12px var(--cyan), 0 0 24px rgba(0,212,255,.5);
  animation: scanLine 2.5s ease-in-out infinite;
  border-radius: 1px;
}
@keyframes scanLine {
  0%   { top: 14px; opacity: 1; }
  50%  { top: calc(100% - 16px); opacity: .8; }
  100% { top: 14px; opacity: 1; }
}

/* Input */
.sc-input-wrap {
  position: relative; margin-bottom: 12px;
}
.sc-input-icon {
  position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
  color: var(--muted); font-size: 14px; pointer-events: none;
}
.sc-input {
  width: 100%; background: var(--s2);
  border: 1.5px solid var(--border2); border-radius: 12px;
  padding: 13px 14px 13px 40px;
  color: var(--text); font-size: 13px; font-weight: 600;
  font-family: 'JetBrains Mono', monospace;
  outline: none; transition: all .2s; letter-spacing: 1px;
  text-transform: uppercase;
}
.sc-input:focus { border-color: var(--cyan); box-shadow: 0 0 0 3px rgba(0,212,255,.1); }
.sc-input::placeholder { color: var(--muted); font-size: 11px; }

/* Buttons */
.sc-btn-primary {
  width: 100%; padding: 14px; border-radius: 14px;
  background: linear-gradient(135deg, var(--cyan), var(--cyan2));
  color: #000; font-size: 13px; font-weight: 800;
  letter-spacing: 1.5px; text-transform: uppercase;
  border: none; cursor: pointer; transition: all .2s;
  box-shadow: 0 4px 20px rgba(0,212,255,.35);
  margin-bottom: 8px;
}
.sc-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 28px rgba(0,212,255,.5); }
.sc-btn-primary:active { transform: translateY(0); }
.sc-btn-camera {
  width: 100%; padding: 12px; border-radius: 14px;
  background: var(--s2); border: 1.5px solid var(--border2);
  color: var(--muted2); font-size: 12px; font-weight: 700;
  letter-spacing: 1px; text-transform: uppercase;
  cursor: pointer; transition: all .2s; display: flex;
  align-items: center; justify-content: center; gap: 8px;
}
.sc-btn-camera:hover { border-color: var(--cyan); color: var(--cyan); }
.sc-btn-camera .pulse { width: 7px; height: 7px; border-radius: 50%; background: var(--red); animation: blink 1s infinite; }
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

/* ── RESULTADO ── */
.sc-result {
  border-radius: 20px; padding: 20px;
  margin-bottom: 16px; display: none;
  border: 1px solid; animation: popIn .3s ease;
}
@keyframes popIn { from{opacity:0;transform:scale(.95)} to{opacity:1;transform:scale(1)} }
.sc-result.success { background: rgba(0,255,136,.08); border-color: rgba(0,255,136,.3); }
.sc-result.error   { background: rgba(255,45,85,.08);  border-color: rgba(255,45,85,.3); }
.sc-result.warning { background: rgba(255,190,0,.08);  border-color: rgba(255,190,0,.3); }

.sc-result-header { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
.sc-result-icon { font-size: 28px; }
.sc-result-title { font-size: 16px; font-weight: 800; }
.sc-result-sub { font-size: 11px; font-weight: 600; opacity: .7; margin-top: 2px; }
.sc-result.success .sc-result-title { color: var(--green); }
.sc-result.error   .sc-result-title { color: var(--red); }
.sc-result.warning .sc-result-title { color: var(--gold); }

.sc-result-info { display: flex; flex-direction: column; gap: 8px; }
.sc-result-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: 9px 12px; border-radius: 10px;
  background: rgba(255,255,255,.04);
  font-size: 12px;
}
.sc-result-row-lbl { color: var(--muted2); font-weight: 600; }
.sc-result-row-val { color: var(--text); font-weight: 700; text-align: right; }

/* ── HISTÓRICO ── */
.sc-history { margin-top: 4px; }
.sc-history-title {
  font-size: 10px; font-weight: 800; color: var(--muted);
  text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px;
  display: flex; align-items: center; justify-content: space-between;
}
.sc-history-clear {
  font-size: 10px; color: var(--red); cursor: pointer;
  font-weight: 700; background: none; border: none; padding: 0;
  font-family: 'Outfit', sans-serif;
}
.sc-history-list { display: flex; flex-direction: column; gap: 6px; }
.sc-history-item {
  display: flex; align-items: center; justify-content: space-between;
  background: var(--s1); border: 1px solid var(--border);
  border-radius: 12px; padding: 10px 14px;
  animation: fadeIn .3s ease;
}
@keyframes fadeIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
.sc-history-code {
  font-family: 'JetBrains Mono', monospace;
  font-size: 11px; font-weight: 700; color: var(--text);
}
.sc-history-name { font-size: 10px; color: var(--muted2); margin-top: 2px; }
.sc-history-right { display: flex; align-items: center; gap: 8px; }
.sc-history-time { font-size: 9px; color: var(--muted); font-family: 'JetBrains Mono', monospace; }
.sc-badge {
  font-size: 9px; font-weight: 800; letter-spacing: .8px; text-transform: uppercase;
  padding: 3px 8px; border-radius: 6px;
}
.sc-badge.ok   { background: rgba(0,255,136,.15); color: var(--green); border: 1px solid rgba(0,255,136,.3); }
.sc-badge.err  { background: rgba(255,45,85,.15);  color: var(--red);   border: 1px solid rgba(255,45,85,.3); }
.sc-badge.warn { background: rgba(255,190,0,.15);  color: var(--gold);  border: 1px solid rgba(255,190,0,.3); }

/* ── LOADING ── */
.sc-loading { display: none; text-align: center; padding: 20px 0; }
.sc-loading-spinner {
  width: 32px; height: 32px; border: 2px solid var(--border2);
  border-top-color: var(--cyan); border-radius: 50%;
  animation: spin .7s linear infinite; margin: 0 auto 10px;
}
@keyframes spin { to { transform: rotate(360deg); } }
.sc-loading-text { font-size: 11px; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }

/* ── CAMERA VIEW ── */
.sc-camera-wrap {
  display: none; position: relative;
  border-radius: 16px; overflow: hidden;
  aspect-ratio: 1; background: #000;
  margin-bottom: 12px;
}
.sc-camera-wrap.active { display: block; }
#cameraPreview { width: 100%; height: 100%; object-fit: cover; }
.sc-camera-overlay {
  position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
  pointer-events: none;
}
.sc-camera-frame {
  width: 60%; height: 60%; position: relative;
}
.sc-camera-frame::before, .sc-camera-frame::after {
  content: ''; position: absolute;
  width: 20px; height: 20px;
}
.sc-camera-scan-line {
  position: absolute; left: 0; right: 0; height: 2px;
  background: var(--cyan); box-shadow: 0 0 10px var(--cyan);
  animation: scanLine 2s ease-in-out infinite;
}
</style>

<div class="sc-bg"></div>
<div class="sc-grid"></div>

<div class="sc-wrap">

    {{-- HEADER --}}
    <div class="sc-header">
        <div class="sc-logo">
            <div class="sc-logo-icon">LT</div>
            <div class="sc-logo-text">Validator<span>PRO</span></div>
        </div>
        <div class="sc-header-right">
            <a href="{{ route('admin.dashboard') }}" class="sc-back-btn">← Dashboard</a>
        </div>
    </div>

    {{-- STATS --}}
    <div class="sc-stats" id="statsRow">
        <div class="sc-stat">
            <div class="sc-stat-num" id="statTotal" style="color:var(--cyan)">0</div>
            <div class="sc-stat-lbl">Validados</div>
        </div>
        <div class="sc-stat">
            <div class="sc-stat-num" id="statOk" style="color:var(--green)">0</div>
            <div class="sc-stat-lbl">Aceites</div>
        </div>
        <div class="sc-stat">
            <div class="sc-stat-num" id="statErr" style="color:var(--red)">0</div>
            <div class="sc-stat-lbl">Inválidos</div>
        </div>
    </div>

    {{-- SCANNER BOX --}}
    <div class="sc-box">
        <div class="sc-box-glow"></div>

        {{-- QR Frame --}}
        <div class="sc-qr-frame" id="qrFrame">
            <div class="sc-qr-corner tl"></div>
            <div class="sc-qr-corner tr"></div>
            <div class="sc-qr-corner bl"></div>
            <div class="sc-qr-corner br"></div>
            <div class="sc-qr-inner">
                <div class="sc-qr-placeholder" id="qrPlaceholder">
                    <svg width="60" height="60" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    <p>Aponta a câmera<br>ou digita o código</p>
                </div>
            </div>
            <div class="sc-scan-line"></div>

            {{-- Camera preview dentro do frame --}}
            <div class="sc-camera-wrap" id="cameraWrap" style="position:absolute;inset:14px;border-radius:8px;">
                <video id="cameraPreview" autoplay playsinline muted style="width:100%;height:100%;object-fit:cover;border-radius:8px;"></video>
            </div>
        </div>

        {{-- Input --}}
        <div class="sc-input-wrap">
            <span class="sc-input-icon">🔍</span>
            <input type="text" id="codigoInput" class="sc-input"
                   placeholder="Digite ou cole o código do bilhete..."
                   autocomplete="off" autocorrect="off" spellcheck="false">
        </div>

        {{-- Loading --}}
        <div class="sc-loading" id="loadingState">
            <div class="sc-loading-spinner"></div>
            <div class="sc-loading-text">A validar bilhete...</div>
        </div>

        {{-- Resultado --}}
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

        {{-- Buttons --}}
        <button class="sc-btn-primary" id="btnValidar" onclick="validarCodigo()">
            ✓ Verificar Acesso
        </button>
        <button class="sc-btn-camera" id="btnCamera" onclick="toggleCamera()">
            <span class="pulse"></span>
            <span id="cameraBtnText">Ativar Câmera (Scanner QR)</span>
        </button>
    </div>

    {{-- HISTÓRICO --}}
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

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
const CSRF    = '{{ csrf_token() }}';
const API_URL = '{{ route("admin.scanner.validar") }}';

let stats     = { total: 0, ok: 0, err: 0 };
let historico = [];
let cameraAtiva = false;
let videoStream = null;
let scanInterval = null;

// ── Validar por código ────────────────────────────────────
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
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ codigo })
        });

        const data = await resp.json();
        mostrarLoading(false);

        if (data.status === 'success') {
            mostrarResultado('success', '✅', 'Entrada Autorizada!', data.message, [
                { lbl: 'Cliente',  val: data.cliente ?? '—' },
                { lbl: 'Evento',   val: data.evento  ?? '—' },
                { lbl: 'Local',    val: data.local   ?? '—' },
                { lbl: 'Data',     val: data.data    ?? '—' },
                { lbl: 'Hora',     val: data.hora    ?? '—' },
                { lbl: 'Tipo',     val: data.tipo    ?? '—' },
                { lbl: 'Preço',    val: data.preco   ?? '—' },
                { lbl: 'Código',   val: data.codigo  ?? '—' },
            ]);
            adicionarHistorico(codigo, data.cliente ?? 'Convidado', 'ok');
            stats.ok++;
            vibrar([200]);
        } else if (data.status === 'warning') {
            mostrarResultado('warning', '⚠️', 'Bilhete Já Utilizado', data.message, [
                { lbl: 'Código', val: codigo },
            ]);
            adicionarHistorico(codigo, 'Já utilizado', 'warn');
            stats.err++;
            vibrar([100, 50, 100]);
        } else {
            mostrarResultado('error', '❌', 'Acesso Negado', data.message, [
                { lbl: 'Código', val: codigo },
            ]);
            adicionarHistorico(codigo, 'Inválido', 'err');
            stats.err++;
            vibrar([300]);
        }

        stats.total++;
        atualizarStats();

        // Limpa o input após validar
        document.getElementById('codigoInput').value = '';

    } catch (e) {
        mostrarLoading(false);
        mostrarResultado('error', '🔌', 'Erro de conexão', 'Não foi possível contactar o servidor.', []);
    }
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

    const info = document.getElementById('resultInfo');
    info.innerHTML = rows.map(r => `
        <div class="sc-result-row">
            <span class="sc-result-row-lbl">${r.lbl}</span>
            <span class="sc-result-row-val mono">${r.val}</span>
        </div>
    `).join('');
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
    if (historico.length === 0) {
        list.innerHTML = '<div style="text-align:center;padding:20px 0;font-size:11px;color:var(--muted);">Nenhuma validação ainda nesta sessão</div>';
        return;
    }
    list.innerHTML = historico.slice(0, 10).map(h => `
        <div class="sc-history-item">
            <div>
                <div class="sc-history-code">${h.codigo.substring(0, 18)}${h.codigo.length > 18 ? '…' : ''}</div>
                <div class="sc-history-name">${h.nome}</div>
            </div>
            <div class="sc-history-right">
                <span class="sc-history-time">${h.hora}</span>
                <span class="sc-badge ${h.tipo}">${h.tipo === 'ok' ? 'OK' : h.tipo === 'warn' ? 'USADO' : 'ERRO'}</span>
            </div>
        </div>
    `).join('');
}

function limparHistorico() {
    historico = [];
    stats = { total: 0, ok: 0, err: 0 };
    atualizarStats();
    renderHistorico();
    esconderResultado();
}

function vibrar(pattern) {
    if (navigator.vibrate) navigator.vibrate(pattern);
}

// ── Enter no input ────────────────────────────────────────
document.getElementById('codigoInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') validarCodigo();
});

// ── Camera QR ────────────────────────────────────────────
async function toggleCamera() {
    if (cameraAtiva) {
        pararCamera();
    } else {
        await iniciarCamera();
    }
}

async function iniciarCamera() {
    try {
        videoStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment', width: { ideal: 640 }, height: { ideal: 640 } }
        });

        const video = document.getElementById('cameraPreview');
        video.srcObject = videoStream;

        document.getElementById('cameraWrap').style.display = 'block';
        document.getElementById('qrPlaceholder').style.display = 'none';
        document.getElementById('cameraBtnText').textContent = '⏹ Desativar Câmera';
        cameraAtiva = true;

        // Scan QR a cada 500ms usando jsQR
        scanInterval = setInterval(() => scanFrame(video), 500);

    } catch (err) {
        mostrarResultado('error', '📷', 'Câmera indisponível', 'Não foi possível aceder à câmera. Verifica as permissões do browser.', []);
    }
}

function pararCamera() {
    if (videoStream) {
        videoStream.getTracks().forEach(t => t.stop());
        videoStream = null;
    }
    clearInterval(scanInterval);
    scanInterval = null;
    cameraAtiva  = false;

    document.getElementById('cameraWrap').style.display = 'none';
    document.getElementById('qrPlaceholder').style.display = 'block';
    document.getElementById('cameraBtnText').textContent = 'Ativar Câmera (Scanner QR)';
}

function scanFrame(video) {
    if (!video.videoWidth) return;

    const canvas = document.createElement('canvas');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const qrCode    = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'dontInvert' });

    if (qrCode && qrCode.data) {
        pararCamera();
        document.getElementById('codigoInput').value = qrCode.data;
        enviarValidacao(qrCode.data);
    }
}

// Foco automático no input ao carregar
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('codigoInput').focus();
});
</script>

@endsection