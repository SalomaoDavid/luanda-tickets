<?php

namespace App\Channels;

use App\Models\PushSubscription;
use Illuminate\Notifications\Notification;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

/**
 * Canal genérico de Web Push — usado por QUALQUER Notification que
 * implemente toWebPush(). Não sabe nada sobre o "conteúdo" de cada
 * notificação; só sabe como entregar o array que toWebPush() devolver
 * a todas as subscrições daquele utilizador.
 *
 * Para ligar uma notificação existente a isto, basta:
 *   1. Acrescentar 'webpush' ao array devolvido por via().
 *   2. Implementar toWebPush($notifiable): array, devolvendo
 *      ['title' => ..., 'body' => ..., 'url' => ..., 'icon' => ...].
 */
class WebPushChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        // Blindagem total: qualquer erro aqui dentro (uma rota que ainda não
        // existe, uma subscrição corrompida, o serviço de push em baixo)
        // NUNCA deve impedir os canais 'database'/'broadcast' de gravarem a
        // notificação normalmente. Por isso tudo corre dentro de um try/catch
        // que só regista o erro no log, nunca deixa a exceção subir.
        try {
            $this->tentarEnviar($notifiable, $notification);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function tentarEnviar(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toWebPush')) {
            return;
        }

        $payload = $notification->toWebPush($notifiable);
        if (empty($payload)) {
            return;
        }

        $subscricoes = PushSubscription::where('user_id', $notifiable->id)->get();
        if ($subscricoes->isEmpty()) {
            return;
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject'    => config('app.url'),
                'publicKey'  => config('services.webpush.public_key'),
                'privateKey' => config('services.webpush.private_key'),
            ],
        ]);

        $body = json_encode([
            'title' => $payload['title'] ?? config('app.name'),
            'body'  => $payload['body'] ?? '',
            'url'   => $payload['url'] ?? '/',
            'icon'  => $payload['icon'] ?? '/logos.png',
        ]);

        foreach ($subscricoes as $sub) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                    'contentEncoding' => $sub->content_encoding ?? 'aes128gcm',
                ]),
                $body
            );
        }

        // flush(): envia tudo de uma vez e diz-nos quais falharam
        foreach ($webPush->flush() as $report) {
            if (!$report->isSuccess() && $report->isSubscriptionExpired()) {
                // O navegador invalidou este endpoint (app desinstalada,
                // permissão revogada, etc.) — deixamos de tentar enviar
                // para ele.
                PushSubscription::where('endpoint', $report->getRequest()->getUri())->delete();
            }
        }
    }
}