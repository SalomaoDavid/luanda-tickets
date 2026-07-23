<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;
use Illuminate\Support\Str;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lista de categorias baseada nas chaves do teu SubcategoriaSeeder
        $categorias = [
            'Shows',
            'Festivais',
            'Viagens',
            'Desportos',
            'conferências', // Mantido em minúsculo para bater exatamente com o teu código
            'Workshops',
            'Cultura'
        ];

        foreach ($categorias as $nome) {
            Categoria::create([
                'nome' => $nome,
                'slug' => Str::slug($nome),
            ]);
        }
    }
}