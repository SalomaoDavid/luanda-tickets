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
        // 1. Cria a usuária Creator (você)
        $user1 = \App\Models\User::factory()->create([
            'name' => 'Salomao',
            'email' => 'salomaodavid70@gmail.com',
            'password' => bcrypt('123456') // senha padrão para teste
        ]);

        // 2. Cria um contato para você conversar
        $user2 = \App\Models\User::factory()->create([
            'name' => 'João Silva',
            'email' => 'joao@teste.com'
        ]);

        // 3. Cria a conversa entre vocês dois
        $conv = \App\Models\Conversation::create([
            'sender_id' => $user1->id,
            'receiver_id' => $user2->id,
            'tipo' => 'pessoal'
        ]);

        // 4. Cria a primeira mensagem para a conversa não ficar vazia
        \App\Models\Message::create([
            'conversation_id' => $conv->id,
            'user_id' => $user1->id,
            'body' => 'Olá João, esta é uma mensagem de teste da Creator!'
        ]);
    }
}
