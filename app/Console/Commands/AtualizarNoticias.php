<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AtualizarNoticias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:atualizar-noticias';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        // Exemplo com o feed do AngoRussia (ou outro portal disponível)
    $url = "https://angorussia.com/feed/"; 
    $rss = simplexml_load_file($url);

    foreach ($rss->channel->item as $item) {
        \App\Models\Noticia::updateOrCreate(
            ['link_original' => (string)$item->link],
            [
                'titulo' => (string)$item->title,
                'conteudo' => (string)$item->description,
                'fonte' => 'AngoRussia',
                'created_at' => date('Y-m-d H:i:s', strtotime($item->pubDate)),
            ]
        );
    }
    $this->info('Notícias atualizadas com sucesso!');
    }
}
