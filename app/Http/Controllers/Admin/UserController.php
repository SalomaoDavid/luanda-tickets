<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::select(
                'id','name','email','role','avatar',
                'is_verified','last_seen','created_at',
                'suspended_at','is_blocked'
            )
            ->orderBy('name','asc')
            ->paginate(20);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function updateRole(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'Não podes alterar o teu próprio cargo!');
        }

        $request->validate(['role' => 'required|in:user,creator,admin']);
        $user->forceFill(['role' => $request->role])->saveQuietly();

        return redirect()->back()->with('success', "O cargo de {$user->name} foi atualizado para {$request->role}!");
    }

    public function toggleVerify(User $user)
    {
        $user->forceFill(['is_verified' => !$user->is_verified])->saveQuietly();
        $estado = $user->is_verified ? 'verificado' : 'verificação removida';

        return redirect()->back()->with('success', "{$user->name} foi {$estado}.");
    }

    public function suspend(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'Não podes suspender a tua própria conta!');
        }

        // Já suspenso — remove suspensão
        if ($user->suspended_at && $user->suspended_at > now()) {
            $user->forceFill(['suspended_at' => null])->saveQuietly();
            \Illuminate\Support\Facades\Cache::forget("user_status_check_{$user->id}");

            return redirect()->back()->with('success', "{$user->name} foi reativado com sucesso.");
        }

        $dias = intval($request->input('dias', 30));
        $user->forceFill(['suspended_at' => now()->addDays($dias)])->saveQuietly();

        return redirect()->back()->with('success', "{$user->name} foi suspenso por {$dias} dias.");
    }

    public function bloquear($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'Não podes bloquear a tua própria conta!');
        }

        $novoEstado = !$user->is_blocked;
        $user->forceFill(['is_blocked' => $novoEstado])->saveQuietly();
        \Illuminate\Support\Facades\Cache::forget("user_status_check_{$user->id}");
        $estado = $novoEstado ? 'bloqueado' : 'desbloqueado';

        return redirect()->back()->with('success', "{$user->name} foi {$estado} com sucesso.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'Não podes eliminar a tua própria conta!');
        }

        $nome = $user->name;

        DB::transaction(function () use ($user) {
            // Pedidos
            Pedido::where('user_id', $user->id)->delete();

            // Reservas
            DB::table('reservas')->where('user_id', $user->id)->delete();

            // Postagens e comentários
            $postagemIds = DB::table('postagens')->where('user_id', $user->id)->pluck('id');
            if ($postagemIds->isNotEmpty()) {
                DB::table('postagem_comentarios')->whereIn('postagem_id', $postagemIds)->delete();
                DB::table('postagem_reacoes')->whereIn('postagem_id', $postagemIds)->delete();
                DB::table('postagens')->whereIn('id', $postagemIds)->delete();
            }

            // Comentários e curtidas
            DB::table('comentarios')->where('user_id', $user->id)->delete();
            DB::table('curtidas')->where('user_id', $user->id)->delete();

            // Seguidores / seguindo
            DB::table('seguidores')
                ->where('seguidor_id', $user->id)
                ->orWhere('seguido_id', $user->id)
                ->delete();

            // Notificações
            DB::table('notifications')
                ->where('notifiable_id', $user->id)
                ->where('notifiable_type', User::class)
                ->delete();

            // Avatar do storage (fora da transacção — ficheiros não fazem rollback)
            if ($user->avatar) {
                try {
                    Storage::disk('public')->delete($user->avatar);
                } catch (\Exception $e) {}
            }

            $user->delete();
        });

        return redirect()->back()->with('success', "O utilizador {$nome} foi eliminado permanentemente.");
    }
}