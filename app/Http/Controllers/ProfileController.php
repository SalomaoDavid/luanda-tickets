<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function show($id): View
    {
        $user = User::findOrFail($id);
        $isOwner = auth()->id() === $user->id;
        
        $postagens = $user->postagens()->latest()->get();

        if ($user->role === 'admin') {
            $eventos = \App\Models\Evento::latest()->get();
            $statsLabel = 'Total Eventos';
            $statsCount = $eventos->count();
            $statsLabel2 = 'Utilizadores';
            $statsCount2 = User::count();
        } elseif ($user->role === 'creator') {
            $eventos = $user->eventos()->latest()->get();
            $statsLabel = 'Eventos';
            $statsCount = $eventos->count();
            $statsLabel2 = 'Seguidores';
            $statsCount2 = 0;
        } else {
            $eventos = $user->eventosCurtidos()->latest()->get();
            $statsLabel = 'Curtidos';
            $statsCount = $eventos->count();
            $statsLabel2 = 'Seguidores';
            $statsCount2 = 0;
        }

        return view('profile.show', compact('user', 'isOwner', 'postagens', 'eventos', 'statsLabel', 'statsCount', 'statsLabel2', 'statsCount2'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Lógica para Salvar a Foto (Avatar)
        if ($request->hasFile('avatar')) {
            // Se já tiver foto antiga, deleta para não encher o servidor
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    
}
