<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // ✅ Select específico — só colunas necessárias para a listagem
        $usuarios = User::select('id', 'name', 'email', 'role', 'avatar', 'is_verified', 'last_seen', 'created_at')
            ->orderBy('name', 'asc')
            ->paginate(20);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function updateRole(Request $request, User $user)
    {
        // Impede que você tire o seu próprio acesso de Admin por acidente — lógica intacta
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('error', 'Você não pode alterar seu próprio cargo!');
        }

        // ✅ updateQuietly — alterar role não precisa de disparar eventos do modelo
        $user->updateQuietly(['role' => $request->role]);

        return redirect()->back()->with('success', "O cargo de {$user->name} foi atualizado!");
    }

    public function toggleVerify(User $user)
    {
        // ✅ updateQuietly — toggle de verificação não precisa de disparar eventos
        $user->updateQuietly(['is_verified' => !$user->is_verified]);

        return redirect()->back()->with('success', 'Status de verificação atualizado!');
    }
}