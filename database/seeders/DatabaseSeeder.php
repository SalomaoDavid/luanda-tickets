<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

    $this->call([
        CategoriaSeeder::class,
        SubcategoriaSeeder::class,
    ]);
        // 1. Cria a usuária Creator (você)
        $user1 = \App\Models\User::factory()->create([
            'name' => 'Salomao David',
            'email' => 'salomaodavid70@gmail.com',
            'password' => bcrypt('outu10bro@@'), // senha padrão para teste
            'role' => 'admin'
        ]);
    }
}
