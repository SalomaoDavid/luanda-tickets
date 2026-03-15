<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubcategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    $dados = [
        'Shows' => [
            'Show de Rap / Hip Hop', 'Show de Kuduro', 'Show de Afro House', 
            'Show Gospel', 'DJ Party', 'Concerto ao Vivo', 'Festival de Música', 'Noite de Karaoke'
        ],
        'Festivais' => [
            'Festival Cultural', 'Festival Gastronómico', 'Festival de Música', 
            'Festival de Dança', 'Festival de Cinema', 'Festival de Arte', 'Festival de Rua'
        ],
        'Viagens' => [
            'Excursão Turística', 'Viagem de Fim de Semana', 'Tour de Praia', 
            'Safari / Aventura', 'Turismo Cultural', 'Viagem Religiosa', 'Passeio de Grupo'
        ],
        'Desportos' => [
            'Torneio de Futebol', 'Maratona / Corrida', 'Campeonato de Basquete', 'Evento de Fitness',
            'Competição de Artes Marciais', 'Torneio de E-sports'
        ],
        'conferências' => [
            'Conferência Empresarial', 'Palestra Motivacional', 'Fórum de Negócios', 'Seminário', 
            'Debate Público', 'Painel de Especialistas'
        ],
        'Workshops' => [
            'Workshop de Programação', 'Workshop de Marketing Digital', 'Workshop de Fotografia',
            'Workshop de Música', 'Curso Intensivo', 'Treinamento Profissional'
        ],
        'Cultura' => [
            'Teatro', 'Exposição de Arte', 'Exposição Fotográfica', 'Cinema / Exibição de Filme',
            'Apresentação de Dança', 'Stand-up Comedy'
        ]
        
    ];

    foreach ($dados as $catNome => $subs) {
        // Encontra a categoria pai pelo nome
        $categoria = \App\Models\Categoria::where('nome', $catNome)->first();

        if ($categoria) {
            foreach ($subs as $subNome) {
                \App\Models\Subcategoria::create([
                    'categoria_id' => $categoria->id,
                    'nome' => $subNome,
                    'slug' => \Illuminate\Support\Str::slug($subNome) . '-' . $categoria->id,
                ]);
            }
        }
    }
    }
}
