<?php
namespace App\Http\Controllers;

use App\Models\Curtida;
use App\Models\Dislike;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SocialController extends Controller
{
    // Toggle Curtida: Lógica espelhada do SiteController
    public function toggleCurtida($id) 
    {
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Faz login para interagir!');
        }
        
        // Remove o dislike se existir (regra de negócio original)
        Dislike::where('user_id', Auth::id())->where('evento_id', $id)->delete();
        
        $curtida = Curtida::where('user_id', Auth::id())->where('evento_id', $id)->first();
        
        if ($curtida) { 
            $curtida->delete(); 
        } else { 
            Curtida::create([
                'user_id' => Auth::id(), 
                'evento_id' => $id
            ]); 
        }
        
        return redirect()->back();
    }

    // Toggle Dislike: Recuperado do SiteController
    public function toggleDislike($id) 
    {
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Faz login para interagir!');
        }

        // Remove a curtida se existir
        Curtida::where('user_id', Auth::id())->where('evento_id', $id)->delete();
        
        $dislike = Dislike::where('user_id', Auth::id())->where('evento_id', $id)->first();
        
        if ($dislike) { 
            $dislike->delete(); 
        } else { 
            Dislike::create([
                'user_id' => Auth::id(), 
                'evento_id' => $id
            ]); 
        }
        
        return redirect()->back();
        }
        public function publicar(Request $request)
        {
            $request->validate([
                'conteudo' => 'required|string|max:5000',
            ]);

            Postagem::create([
                'user_id' => auth()->id(),
                'conteudo' => $request->conteudo,
            ]);

            return redirect()->back()->with('success', 'Post publicado com sucesso!');
        }
}
