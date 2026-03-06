@extends('layouts.app')

@section('content')
<div class="px-4">
    <div class="max-w-7xl mx-auto py-10">
        <div class="post-card p-8">
            <h2 class="text-2xl font-black text-white italic uppercase tracking-tighter mb-8">
                <span class="text-sky-500">//</span> Gestão de Membros
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/10">
                            <th class="py-4 text-[10px] font-black uppercase text-slate-500">Usuário</th>
                            <th class="py-4 text-[10px] font-black uppercase text-slate-500">Email</th>
                            <th class="py-4 text-[10px] font-black uppercase text-slate-500">Selo</th> {{-- Nova Coluna --}}
                            <th class="py-4 text-[10px] font-black uppercase text-slate-500">Cargo Atual</th>
                            <th class="py-4 text-[10px] font-black uppercase text-slate-500">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $user)
                        <tr class="border-b border-white/5 hover:bg-white/5 transition">
                            {{-- Nome e Avatar --}}
                            <td class="py-4 flex items-center gap-3">
                                <div class="relative">
                                    <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" class="w-8 h-8 rounded-full border border-sky-500/30">
                                    @if($user->is_verified)
                                        <div class="absolute -top-1 -right-1 verified-badge w-3 h-3 text-[6px]">✓</div>
                                    @endif
                                </div>
                                <span class="text-xs font-bold text-white">{{ $user->name }}</span>
                            </td>

                            {{-- Email --}}
                            <td class="py-4 text-xs text-slate-400">{{ $user->email }}</td>

                            {{-- Botão de Verificação (O que você pediu) --}}
                            <td class="py-4">
                                <form action="{{ route('admin.usuarios.verify', $user->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="flex items-center gap-1 {{ $user->is_verified ? 'text-sky-500' : 'text-slate-600' }} hover:opacity-80 transition">
                                        <span class="text-[10px] font-black uppercase">{{ $user->is_verified ? 'Verificado' : 'Verificar' }}</span>
                                        @if($user->is_verified)
                                            <div class="verified-badge">✓</div>
                                        @endif
                                    </button>
                                </form>
                            </td>

                            {{-- Badge de Cargo --}}
                            <td class="py-4">
                                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase {{ $user->role == 'admin' ? 'bg-red-500/20 text-red-500' : ($user->role == 'creator' ? 'bg-sky-500/20 text-sky-500' : 'bg-slate-500/20 text-slate-400') }}">
                                    {{ $user->role }}
                                </span>
                            </td>

                            {{-- Select de Alterar Cargo --}}
                            <td class="py-4">
                                <form action="{{ route('admin.usuarios.role', $user->id) }}" method="POST" class="flex gap-2">
                                    @csrf @method('PATCH')
                                    <select name="role" onchange="this.form.submit()" class="bg-[#020617] border border-white/10 text-[10px] rounded-lg px-2 py-1 outline-none focus:border-sky-500 text-slate-400">
                                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                        <option value="creator" {{ $user->role == 'creator' ? 'selected' : '' }}>Criador</option>
                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                                                {{-- Dentro do seu loop de usuários no Admin --}}
                               <a href="{{ route('mensagens.index', ['user_id' => $user->id]) }}" 
                                class="p-2 text-sky-500 hover:bg-sky-500/10 rounded-lg transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-6 text-slate-500">
                {{ $usuarios->links() }}
            </div>
        </div>
    </div>
</div>
@endsection