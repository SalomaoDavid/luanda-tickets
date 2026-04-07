<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
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
        // Quando o middleware 'auth' barra o acesso (curtir, comentar, publicar)
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
        // Quando user tenta eliminar post/comentário de outro user (403)
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
        // Evento::findOrFail, Comentario::findOrFail, Postagem::findOrFail
        $exceptions->render(function (ModelNotFoundException $e, $request) {
            $modelos = [
                'Evento'            => 'O evento não foi encontrado.',
                'Comentario'        => 'O comentário não foi encontrado.',
                'Postagem'          => 'A publicação não foi encontrada.',
                'PostagemComentario'=> 'O comentário não foi encontrado.',
                'PostagemReacao'    => 'A reação não foi encontrada.',
                'User'              => 'O utilizador não foi encontrado.',
                'Categoria'         => 'A categoria não foi encontrada.',      // ← novo
                'Subcategoria'      => 'A subcategoria não foi encontrada.',   // ← novo
                'TipoIngresso'      => 'O tipo de ingresso não foi encontrado.', // ← novo
                'Noticia'           => 'A notícia não foi encontrada.',        // ← novo
                'Reserva'       => 'A reserva não foi encontrada.',           // ← novo
                'Pedido'        => 'O pedido não foi encontrado.',            // ← novo
                'Bilhete'       => 'O bilhete não foi encontrado.',           // ← novo
                'EventoFoto'    => 'As fotos do evento não foram encontradas.', // ← novo
            ];

            $modelo    = class_basename($e->getModel());
            $mensagem  = $modelos[$modelo] ?? 'O recurso solicitado não foi encontrado.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => $mensagem], 404);
            }

            // Rota de evento não encontrado → volta ao feed sem quebrar
            return redirect()->route('home')
                ->with('error', $mensagem);
        });

        // ── 4. ROTA NÃO ENCONTRADA (404 HTTP) ────────────────────────
        // URL inexistente no web.php
        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Página não encontrada.'], 404);
            }
            // Se tiveres uma view errors/404.blade.php usa-a, senão redireciona
            if (view()->exists('errors.404')) {
                return response()->view('errors.404', [], 404);
            }
            return redirect()->route('home')
                ->with('error', 'A página que procuras não existe.');
        });

        // ── 5. VALIDAÇÃO ─────────────────────────────────────────────
        // publicar (conteudo required), comentar (corpo required), reagir (tipo inválido)
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

        // ── 7. ERROS HTTP GENÉRICOS (403, 500, etc.) ─────────────────
        $exceptions->render(function (HttpException $e, $request) {
            $codigo    = $e->getStatusCode();
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

        $exceptions->render(function (PostTooLargeException $e, $request) {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'error' => 'O ficheiro enviado é demasiado grande. Máximo permitido: 5MB.',
            ], 413);
            }
            return redirect()->back()
                ->withErrors(['comprovativo' => 'O ficheiro é demasiado grande. Máximo: 5MB.'])
                ->withInput();
        });

        $exceptions->render(function (QueryException $e, $request) {
            // Deadlock MySQL (código 1213) ou duplicate entry (1062)
        $codigo = $e->errorInfo[1] ?? null;

            $mensagem = match($codigo) {
                1213 => 'Conflito ao processar a reserva. Tenta novamente.',
                1062 => 'Este registo já existe.',
                default => 'Erro ao comunicar com a base de dados.'
            };

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => $mensagem], 500);
            }

            return redirect()->back()
                ->with('error', $mensagem)
                ->withInput();
        });

            // ── NOVO: ERRO DE CONEXÃO EXTERNA (RSS / file_get_contents) ──
                // Quando o feed do AngoRussia está inacessível e lança exception
                $exceptions->render(function (\ErrorException $e, $request) {
                    // Só captura erros de rede/stream — deixa outros ErrorException passarem
                    if (!str_contains($e->getMessage(), 'file_get_contents') &&
                        !str_contains($e->getMessage(), 'simplexml') &&
                        !str_contains($e->getMessage(), 'SSL') &&
                        !str_contains($e->getMessage(), 'Connection')) {
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

        // ── 8. FALLBACK GERAL ─────────────────────────────────────────
        // Qualquer erro não capturado acima — site nunca quebra
        $exceptions->render(function (\Throwable $e, $request) {
            // Em produção nunca expõe detalhes do erro
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
            // Em desenvolvimento deixa o Laravel mostrar o erro completo
            return null;
        });

    })->create();