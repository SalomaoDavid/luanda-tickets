<?php
namespace App\Http\Controllers;

use App\Models\Noticia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    // Listagem de notícias com paginação (Igual ao SiteController)
    public function index() 
    {
        $noticias = Noticia::orderBy('created_at', 'desc')->paginate(12);
        return view('noticias', compact('noticias'));
    }

    // Leitura da notícia individual (slug)
    public function show($slug)
    {
        $noticia = Noticia::where('slug', $slug)->firstOrFail();
        return view('noticia-detalhes', compact('noticia'));
    }

    // Sincronizador Automático: Lógica RSS do AngoRussia recuperada
    public function sincronizar()
    {
        $url = "https://angorussia.com/feed/";
        
        // Contexto para evitar erros de SSL ao buscar imagens/HTML
        $context = stream_context_create([
            "ssl" => [
                "verify_peer" => false, 
                "verify_peer_name" => false
            ]
        ]);

        $xml = @simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);

        if (!$xml) {
            return redirect()->back()->with('error', 'Não foi possível conectar ao feed do AngoRussia.');
        }

        foreach ($xml->channel->item as $item) {
            $linkOriginal = (string)$item->link;
            $imagemUrl = null;

            // Busca o HTML da página original para extrair a imagem de destaque (og:image)
            $htmlNoticia = @file_get_contents($linkOriginal, false, $context);

            if ($htmlNoticia) {
                if (preg_match('/<meta property="og:image" content="(?P<src>.+?)"/i', $htmlNoticia, $matches)) {
                    $imagemUrl = $matches['src'];
                }
            }

            Noticia::updateOrCreate(
                ['link_original' => $linkOriginal],
                [
                    'titulo' => (string)$item->title,
                    // Slug único usando o título e timestamp para evitar conflitos
                    'slug' => Str::slug((string)$item->title) . '-' . time(),
                    'conteudo' => (string)$item->description,
                    'imagem_destaque' => $imagemUrl,
                    'fonte' => 'AngoRussia',
                    'publicado_em' => date('Y-m-d H:i:s', strtotime($item->pubDate)),
                    'autor_id' => 1,
                    'categoria_id' => 1
                ]
            );
        }

        return redirect()->back()->with('success', 'Notícias sincronizadas com imagens reais!');
    }
}
