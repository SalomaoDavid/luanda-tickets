<?php
namespace App\Http\Controllers;

use App\Models\Curtida;
use App\Models\Dislike;
use App\Models\Postagem;
use App\Models\Comentario;
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

        public function eliminarPost($id)
        {
            $post = \App\Models\Postagem::findOrFail($id);

            // Só o dono pode eliminar
            if ($post->user_id !== auth()->id()) {
                return redirect()->back()->with('error', 'Não tens permissão para eliminar este post.');
            }

            $post->delete();

            return redirect()->back()->with('success', 'Post eliminado com sucesso!');
        }   
        public function comentar(Request $request, $eventoId)
    {
        $request->validate(['corpo' => 'required|string|max:500']);

        Comentario::create([
            'evento_id' => $eventoId,
            'user_id'   => auth()->id(),
            'parent_id' => $request->parent_id ?? null,
            'corpo'     => $request->corpo,
        ]);

        return back();
    }

    public function toggleLikeComentario($id)
    {
        $comentario = \App\Models\Comentario::findOrFail($id);
        $comentario->likes()->toggle(auth()->id());
        return back();
    }

    public function eliminarComentario($id)
    {
        $comentario = \App\Models\Comentario::findOrFail($id);
        if ($comentario->user_id === auth()->id()) {
            $comentario->delete();
        }
        return back();
    }
}
