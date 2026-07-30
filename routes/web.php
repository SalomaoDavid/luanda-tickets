<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminUsuarioController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\PostagemController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminEventoController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\SaldoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. ROTAS PÚBLICAS (Acessíveis por qualquer pessoa)
|--------------------------------------------------------------------------
*/
Route::get('/meus-bilhetes/{pedido_id}', [TicketController::class, 'download'])
->name('bilhetes.download')
->middleware('auth');
Route::get('/bilhete/download/{id}', [TicketController::class, 'downloadIndividual'])
->name('bilhete.individual.download')
->middleware('auth');
Route::delete('/bilhete/{id}', [TicketController::class, 'eliminar'])->name('bilhete.eliminar')->middleware('auth');

Route::get('/', [EventController::class, 'index'])->name('home');
Route::get('/evento/{id}', [EventController::class, 'show'])->name('evento.detalhes');
Route::get('/explorar', [EventController::class, 'todosEventos'])->name('eventos.todos');

// Perfil público
Route::get('/u/{id}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/perfil/{id}/seguidores', [ProfileController::class, 'seguidores'])->name('perfil.seguidores');
Route::get('/perfil/{id}/seguindo',   [ProfileController::class, 'seguindo'])->name('perfil.seguindo');

// Notícias
Route::get('/noticias', [NewsController::class, 'index'])->name('noticias.index');
Route::get('/noticia/{slug}', [NewsController::class, 'show'])->name('noticias.detalhes');

/*
|--------------------------------------------------------------------------
| 2. ROTAS PARA USUÁRIOS LOGADOS (Qualquer conta)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

   
    Route::get('/dashboard', function () {
        return redirect()->route('home'); // Uma rota redirecionando para outra
    })->name('dashboard');
    
    // Interações sociais
    Route::post('/evento/{id}/comentar',   [SocialController::class, 'comentar'])->name('evento.comentar');
    Route::post('/comentario/{id}/like',   [SocialController::class, 'toggleLikeComentario'])->name('comentario.like');
    Route::delete('/comentario/{id}',      [SocialController::class, 'eliminarComentario'])->name('comentario.eliminar');
    Route::post('/evento/{id}/curtir',     [SocialController::class, 'toggleCurtida'])->name('evento.curtir');
    Route::post('/evento/{id}/dislike',    [SocialController::class, 'toggleDislike'])->name('evento.dislike');
    Route::post('/social/publicar',        [SocialController::class, 'publicar'])->name('social.publicar');
    Route::delete('/post/{id}/eliminar',   [SocialController::class, 'eliminarPost'])->name('post.eliminar');

    // Reservas
    Route::post('/evento-detalhes', [ReservaController::class, 'store'])->name('reserva.guardar');

    // Postagens
    Route::post('/postagens/{id}/reagir/{tipo}', [PostagemController::class, 'toggleReacao'])->name('postagem.reagir');
    Route::post('/postagens/{id}/comentar',      [PostagemController::class, 'comentar'])->name('postagem.comentar');
    Route::delete('/postagens/comentarios/{id}', [PostagemController::class, 'eliminarComentario'])->name('postagem.comentario.eliminar');

    // Notificações
    Route::get('/notificacoes',                   [NotificacaoController::class, 'index'])->name('notificacoes.index');
    Route::post('/notificacoes/{id}/lida',        [NotificacaoController::class, 'marcarLida'])->name('notificacoes.marcarLida');
    Route::post('/notificacoes/marcar-todas',     [NotificacaoController::class, 'marcarTodas'])->name('notificacoes.marcarTodas');

    // Perfil
    Route::post('/perfil/{id}/seguir',    [ProfileController::class, 'toggleSeguir'])->name('perfil.seguir');
    Route::post('/perfil/{id}/bloquear',  [ProfileController::class, 'toggleBloquear'])->name('perfil.bloquear');
    Route::post('/perfil/{id}/denunciar', [ProfileController::class, 'denunciar'])->name('perfil.denunciar');
    Route::get('/profile',                [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',              [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',             [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dados bancários (criadores guardam o seu IBAN)
    Route::post('/dados-bancarios', [SaldoController::class, 'guardarDadosCriador'])->name('dados-bancarios.guardar');

    // Mensagens
    Route::get('/mensagens/{conversation?}', \App\Livewire\Messages\MessagesIndex::class)
        ->where('conversation', '[0-9]+')
        ->name('mensagens.index');
});

/*
|--------------------------------------------------------------------------
| 3. ÁREA RESTRITA: ADMIN E GESTORES
|--------------------------------------------------------------------------
*/
// ── ADMIN PURO — só administradores ────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [SiteController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/admin/analises', [SiteController::class, 'analisesSistema'])->name('admin.analises');
    Route::get('/admin/analises/relatorio/pdf', [SiteController::class, 'relatorioPdf'])->name('admin.analises.pdf');
    Route::get('/admin/analises/bilhete/{codigo}/pdf', [SiteController::class, 'relatorioBilhete'])->name('admin.analises.bilhete.pdf');

    // Utilizadores — só admin
    Route::get('/usuarios', [UserController::class, 'index'])->name('admin.usuarios.index');
    Route::patch('/usuarios/{user}/role', [UserController::class, 'updateRole'])->name('admin.usuarios.role');
    Route::patch('/usuarios/{user}/verify', [UserController::class, 'toggleVerify'])->name('admin.usuarios.verify');
    Route::patch('/usuarios/{id}/suspend', [UserController::class, 'suspend'])->name('admin.usuarios.suspend');
    Route::delete('/usuarios/{id}', [UserController::class, 'destroy'])->name('admin.usuarios.destroy');
    Route::patch('/usuarios/{id}/bloquear', [UserController::class, 'bloquear'])->name('admin.usuarios.bloquear');

    // Notícias — só admin
    Route::get('/noticias/sincronizar', [NewsController::class, 'sincronizar'])->name('noticias.sincronizar');

    // Saldos e contas bancárias — só admin
    Route::get('/saldos', [SaldoController::class, 'index'])->name('admin.saldos');
    Route::post('/saldos/{criadorId}/pagar', [SaldoController::class, 'marcarPago'])->name('admin.saldos.pagar');
    Route::get('/contas-bancarias', [SaldoController::class, 'contasBancarias'])->name('admin.contas-bancarias');
    Route::post('/contas-bancarias', [SaldoController::class, 'storeConta'])->name('admin.contas-bancarias.store');
    Route::patch('/contas-bancarias/{id}', [SaldoController::class, 'updateConta'])->name('admin.contas-bancarias.update');
    Route::delete('/contas-bancarias/{id}', [SaldoController::class, 'destroyConta'])->name('admin.contas-bancarias.destroy');

    // Scanner — só admin
    Route::get('/scanner', [App\Http\Controllers\Admin\ScannerController::class, 'index'])->name('admin.scanner');
    Route::post('/scanner/validar', [App\Http\Controllers\Admin\ScannerController::class, 'validar'])->name('admin.scanner.validar');
});

// ── ADMIN + CREATOR — rotas partilhadas ─────────────────────
Route::middleware(['auth', 'creator'])->prefix('admin')->group(function () {

    // Eventos — criador gere os seus, admin gere todos
    Route::get('/eventos', [AdminEventoController::class, 'index'])->name('admin.eventos');
    Route::get('/eventos/criar', [AdminEventoController::class, 'create'])->name('admin.eventos.criar');
    Route::post('/eventos/guardar', [AdminEventoController::class, 'store'])->name('admin.eventos.guardar');
    Route::get('/eventos/{id}/editar', [AdminEventoController::class, 'edit'])->name('admin.eventos.editar');
    Route::put('/eventos/{id}/atualizar', [AdminEventoController::class, 'update'])->name('admin.eventos.atualizar');
    Route::delete('/eventos/{id}/eliminar', [AdminEventoController::class, 'destroy'])->name('admin.eventos.eliminar');

    // Reservas — criador vê as suas, admin vê todas
    Route::get('/reservas', [BookingController::class, 'adminReservas'])->name('admin.reservas');
    Route::get('/admin-pagos', [BookingController::class, 'adminPagos'])->name('admin.pagos');
    Route::post('/reserva/{id}/confirmar', [BookingController::class, 'confirmarReserva'])->name('reserva.confirmar');
    Route::delete('/reserva/{id}/eliminar', [BookingController::class, 'eliminarReserva'])->name('reserva.eliminar');
});

/*
|--------------------------------------------------------------------------
| 4. AUTENTICAÇÃO (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';