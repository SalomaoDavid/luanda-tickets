<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\AdminEventoController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. ROTAS PÚBLICAS (Acessíveis por qualquer pessoa)
|--------------------------------------------------------------------------
*/
Route::get('/', [EventController::class, 'index'])->name('home');
Route::get('/evento/{id}', [EventController::class, 'show'])->name('evento.detalhes');
Route::get('/explorar', [EventController::class, 'todosEventos'])->name('eventos.todos');

// Notícias
Route::get('/noticias', [NewsController::class, 'index'])->name('noticias.index');
Route::get('/noticia/{slug}', [NewsController::class, 'show'])->name('noticias.detalhes');

/*
|--------------------------------------------------------------------------
| 2. ROTAS PARA USUÁRIOS LOGADOS (Qualquer conta)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Redirecionamento do Dashboard do Breeze
    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->name('dashboard');

    // Interações
    Route::post('/evento/{id}/curtir', [SocialController::class, 'toggleCurtida'])->name('evento.curtir');
    Route::post('/evento/{id}/dislike', [SocialController::class, 'toggleDislike'])->name('evento.dislike');
    Route::post('/evento-detalhes', [BookingController::class, 'guardarReserva'])->name('reserva.guardar');
    Route::post('/social/publicar', [SocialController::class, 'publicar'])->name('social.publicar');

    // Perfil do Usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/mensagens/{conversation?}', \App\Livewire\Messages\MessagesIndex::class)->name('mensagens.index');
});

/*
|--------------------------------------------------------------------------
| 3. ÁREA RESTRITA: ADMIN E GESTORES (Apenas Role 'admin' ou 'creator')
|--------------------------------------------------------------------------
*/
// Usamos o middleware 'admin' que criamos para proteger estas rotas
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    
    // Dashboard Administrativo
    Route::get('/dashboard', [SiteController::class, 'adminDashboard'])->name('admin.dashboard');
    
    // Gestão de Membros (Selo e Cargos)
    Route::get('/usuarios', [UserController::class, 'index'])->name('admin.usuarios.index');
    Route::patch('/usuarios/{user}/role', [UserController::class, 'updateRole'])->name('admin.usuarios.role');
    Route::patch('/usuarios/{user}/verify', [UserController::class, 'toggleVerify'])->name('admin.usuarios.verify');

    // Sincronizador de Notícias
    Route::get('/noticias/sincronizar', [NewsController::class, 'sincronizar'])->name('noticias.sincronizar');

    // Gestão de Eventos (Onde você e os promotores trabalham)
    Route::get('/eventos', [AdminEventoController::class, 'index'])->name('admin.eventos');
    Route::get('/eventos/criar', [AdminEventoController::class, 'create'])->name('admin.eventos.criar');
    Route::post('/eventos/guardar', [AdminEventoController::class, 'store'])->name('admin.eventos.guardar');
    Route::get('/eventos/{id}/editar', [AdminEventoController::class, 'edit'])->name('admin.eventos.editar');
    Route::put('/eventos/{id}/atualizar', [AdminEventoController::class, 'update'])->name('admin.eventos.atualizar');
    Route::delete('/eventos/{id}/eliminar', [AdminEventoController::class, 'destroy'])->name('admin.eventos.eliminar');

    // Gestão de Reservas e Pagamentos
    Route::get('/reservas', [BookingController::class, 'adminReservas'])->name('admin.reservas');
    Route::get('/admin-pagos', [BookingController::class, 'adminPagos'])->name('admin.pagos');
    Route::post('/reserva/{id}/confirmar', [BookingController::class, 'confirmarReserva'])->name('reserva.confirmar');
    Route::delete('/reserva/{id}/eliminar', [BookingController::class, 'eliminarReserva'])->name('reserva.eliminar');
    Route::get('/scanner', [App\Http\Controllers\Admin\ScannerController::class, 'index'])->name('admin.scanner');

    // Rota POST para validar o QR Code via AJAX
    Route::post('/scanner/validar', [App\Http\Controllers\Admin\ScannerController::class, 'validar'])->name('admin.scanner.validar');
});

/*
|--------------------------------------------------------------------------
| 4. AUTENTICAÇÃO (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';