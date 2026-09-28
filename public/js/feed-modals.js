// resources/../public/js/feed-modals.js
// Funções partilhadas entre welcome.blade.php e todos-eventos.blade.php.
// Só contém código que era 100% idêntico nas duas páginas — nada de comportamento novo.

// ── MODAIS GENÉRICOS (curtidas / comentários / comentários de post) ──
function abrirModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;display:flex;align-items:center;justify-content:center;z-index:99999;';
    m.classList.remove('hidden');
    document.body.appendChild(m);
}
function fecharModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.style.display = 'none';
    m.classList.add('hidden');
}
document.addEventListener('click', function (e) {
    ['modal-curtidas-', 'modal-comentarios-', 'modal-comentarios-post-'].forEach(function (p) {
        document.querySelectorAll(`[id^="${p}"]`).forEach(function (m) {
            if (e.target === m) fecharModal(m.id);
        });
    });
});

// ── RESPONDER A COMENTÁRIO ──
function toggleResposta(id) {
    const f = document.getElementById(id);
    if (!f) return;
    f.classList.toggle('hidden');
    if (!f.classList.contains('hidden')) f.querySelector('input')?.focus();
}

// ── MODAL DE VÍDEO ──
function abrirVideoModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    // iframe URL externa
    const iframe = modal.querySelector('iframe');
    if (iframe && iframe.dataset.src && !iframe.src) iframe.src = iframe.dataset.src;
    // video local — só define src ao abrir (evita autoplay escondido)
    const video = modal.querySelector('video[data-local]');
    if (video && video.dataset.local) {
        video.src = video.dataset.local;
        video.style.display = 'block';
        video.muted = false;
        video.play().catch(function () {});
    }
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function fecharVideoModal(id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    // Parar iframe
    const iframe = modal.querySelector('iframe');
    if (iframe) iframe.src = '';
    // Parar e limpar video local
    const video = modal.querySelector('video[data-local]');
    if (video) {
        video.pause();
        video.src = '';
        video.style.display = 'none';
    }
    modal.classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('click', function (e) {
    document.querySelectorAll('.ev-video-modal.open').forEach(function (m) {
        if (e.target === m) fecharVideoModal(m.id);
    });
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.ev-video-modal.open').forEach(function (m) { fecharVideoModal(m.id); });
    }
});