// Service worker — regista o site como instalável (Chrome/Android) e agora
// também recebe e mostra notificações push, mesmo com o site fechado.
self.addEventListener('install', function (e) {
    self.skipWaiting();
});

self.addEventListener('activate', function (e) {
    self.clients.claim();
});

self.addEventListener('fetch', function (e) {
    // Vazio de propósito — sem cache offline por agora.
});

// Recebe o push enviado pelo servidor (WebPushChannel) e mostra a
// notificação do sistema operativo — funciona com o site/app fechado.
self.addEventListener('push', function (event) {
    let data = {};
    try {
        data = event.data ? event.data.json() : {};
    } catch (e) {
        data = { title: 'Luanda Tickets', body: event.data ? event.data.text() : '' };
    }

    const title = data.title || 'Luanda Tickets';
    const options = {
        body: data.body || '',
        icon: data.icon || '/logos.png',
        badge: '/logos.png',
        data: { url: data.url || '/' },
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

// Ao clicar na notificação: foca uma aba já aberta no site, ou abre uma nova
// diretamente no link relevante (ex: o bilhete aprovado).
self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    const url = (event.notification.data && event.notification.data.url) || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            for (const client of clientList) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    client.navigate(url);
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});