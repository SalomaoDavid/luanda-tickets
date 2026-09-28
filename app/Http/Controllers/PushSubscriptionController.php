<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    /**
     * Guarda (ou atualiza) a subscrição push devolvida pelo navegador
     * (PushManager.subscribe()) para o utilizador autenticado.
     */
    public function store(Request $request)
    {
        $dados = $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint_hash' => hash('sha256', $dados['endpoint'])],
            [
                'user_id'          => auth()->id(),
                'endpoint'         => $dados['endpoint'],
                'public_key'       => $dados['keys']['p256dh'],
                'auth_token'       => $dados['keys']['auth'],
                'content_encoding' => 'aes128gcm',
            ]
        );

        return response()->json(['ok' => true]);
    }

    /**
     * Remove a subscrição — chamado quando o utilizador desativa as
     * notificações neste dispositivo, ou quando o navegador invalida a
     * subscrição (endpoint deixou de existir).
     */
    public function destroy(Request $request)
    {
        $dados = $request->validate(['endpoint' => 'required|string']);

        PushSubscription::where('endpoint_hash', hash('sha256', $dados['endpoint']))
            ->where('user_id', auth()->id())
            ->delete();

        return response()->json(['ok' => true]);
    }
}