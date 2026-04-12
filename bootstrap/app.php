<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;                          // ← estava em falta
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\UpdateUserLastSeen::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // ── 1. NÃO AUTENTICADO ───────────────────────────────────────
        // Middleware 'auth' barra o acesso: curtir, comentar, publicar
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error'    => 'Sessão expirada. Faz login para continuar.',
                    'redirect' => route('login'),
                ], 401);
            }
            return redirect()->route('login')
                ->with('error', 'Tens de iniciar sessão para realizar esta ação.');
        });

        // ── 2. SEM PERMISSÃO ─────────────────────────────────────────
        // Eliminar post/comentário alheio, aceder a bilhetes de outro user
        $exceptions->render(function (AuthorizationException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'Não tens permissão para realizar esta ação.',
                ], 403);
            }
            return redirect()->back()
                ->with('error', 'Não tens permissão para realizar esta ação.');
        });

        // ── 3. MODELO NÃO ENCONTRADO ─────────────────────────────────
        // findOrFail em qualquer model — mensagem específica por modelo
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            $modelos = [
                'Evento'             => 'O evento não foi encontrado.',
                'Comentario'         => 'O comentário não foi encontrado.',
                'Postagem'           => 'A publicação não foi encontrada.',
                'PostagemComentario' => 'O comentário não foi encontrado.',
                'PostagemReacao'     => 'A reação não foi encontrada.',
                'User'               => 'O utilizador não foi encontrado.',
                'Categoria'          => 'A categoria não foi encontrada.',
                'Subcategoria'       => 'A subcategoria não foi encontrada.',
                'TipoIngresso'       => 'O tipo de ingresso não foi encontrado.',
                'Noticia'            => 'A notícia não foi encontrada.',
                'Reserva'            => 'A reserva não foi encontrada.',
                'Pedido'             => 'O pedido não foi encontrado.',
                'Bilhete'            => 'O bilhete não foi encontrado.',
                'EventoFoto'         => 'As fotos do evento não foram encontradas.',
            ];

            $modelo   = class_basename($e->getModel());
            $mensagem = $modelos[$modelo] ?? 'O recurso solicitado não foi encontrado.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => $mensagem], 404);
            }

            return redirect()->route('home')
                ->with('error', $mensagem);
        });

        // ── 4. ROTA NÃO ENCONTRADA (404 HTTP) ────────────────────────
        // URL inexistente no web.php
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Página não encontrada.'], 404);
            }
            if (view()->exists('errors.404')) {
                return response()->view('errors.404', [], 404);
            }
            return redirect()->route('home')
                ->with('error', 'A página que procuras não existe.');
        });

        // ── 5. VALIDAÇÃO ─────────────────────────────────────────────
        // publicar, comentar, reagir, criar/editar evento, reserva, perfil
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error'  => 'Dados inválidos.',
                    'errors' => $e->errors(),
                ], 422);
            }
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        });

        // ── 6. THROTTLE / RATE LIMIT ─────────────────────────────────
        // Spam de likes, comentários, publicações em sequência
        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'Estás a agir demasiado rápido. Aguarda uns segundos.',
                ], 429);
            }
            return redirect()->back()
                ->with('error', 'Estás a agir demasiado rápido. Tenta novamente em breve.');
        });

        // ── 7. UPLOAD DEMASIADO GRANDE ───────────────────────────────
        // Comprovativo de pagamento, avatar, capa de evento > 5MB
        $exceptions->render(function (PostTooLargeException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'O ficheiro enviado é demasiado grande. Máximo permitido: 5MB.',
                ], 413);
            }
            return redirect()->back()
                ->withErrors(['ficheiro' => 'O ficheiro é demasiado grande. Máximo: 5MB.'])
                ->withInput();
        });

        // ── 8. FALHA NA BASE DE DADOS ────────────────────────────────
        // Deadlock na transação de reserva, duplicate entry, falha de conexão
        $exceptions->render(function (QueryException $e, $request) {
            $codigo = $e->errorInfo[1] ?? null;

            $mensagem = match($codigo) {
                1213    => 'Conflito ao processar a reserva. Tenta novamente.',
                1062    => 'Este registo já existe.',
                default => 'Erro ao comunicar com a base de dados.',
            };

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => $mensagem], 500);
            }

            return redirect()->back()
                ->with('error', $mensagem)
                ->withInput();
        });

        // ── 9. ERRO DE CONEXÃO EXTERNA (RSS / file_get_contents) ─────
        // Feed AngoRussia inacessível, SSL inválido, timeout de rede
        $exceptions->render(function (\ErrorException $e, $request) {
            if (
                !str_contains($e->getMessage(), 'file_get_contents') &&
                !str_contains($e->getMessage(), 'simplexml') &&
                !str_contains($e->getMessage(), 'SSL') &&
                !str_contains($e->getMessage(), 'Connection')
            ) {
                return null; // passa para o próximo handler
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'Não foi possível conectar ao serviço externo. Tenta novamente.',
                ], 503);
            }

            return redirect()->back()
                ->with('error', 'Não foi possível conectar ao feed de notícias. Tenta novamente mais tarde.');
        });

        // ── 10. ERROS HTTP GENÉRICOS (403, 500, 503, etc.) ───────────
        // abort(403) nos controllers, erros de servidor genéricos
        $exceptions->render(function (HttpException $e, $request) {
            $codigo = $e->getStatusCode();

            $mensagens = [
                403 => 'Acesso proibido.',
                500 => 'Erro interno do servidor. Tenta novamente.',
                503 => 'O serviço está temporariamente indisponível.',
            ];
            $mensagem = $mensagens[$codigo] ?? 'Ocorreu um erro inesperado.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => $mensagem], $codigo);
            }
            if (view()->exists("errors.{$codigo}")) {
                return response()->view("errors.{$codigo}", [], $codigo);
            }
            return redirect()->route('home')
                ->with('error', $mensagem);
        });

        // ── 11. FALLBACK GERAL ────────────────────────────────────────
        // Qualquer erro não capturado acima — site nunca quebra
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => app()->isProduction()
                        ? 'Ocorreu um erro inesperado. Tenta novamente.'
                        : $e->getMessage(),
                ], 500);
            }
            if (app()->isProduction()) {
                return redirect()->route('home')
                    ->with('error', 'Algo correu mal. Tenta novamente.');
            }
            // Em desenvolvimento mostra o erro completo do Laravel
            return null;
        });

    })->create();