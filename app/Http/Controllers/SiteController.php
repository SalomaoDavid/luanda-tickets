<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Reserva;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function adminDashboard()
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'admin';

        // 1. DEFINIÇÃO DAS QUERIES BASE
        // Se não for admin, filtramos tudo para mostrar apenas o que pertence ao usuário logado
        $queryReservas = Reserva::where('status', 'pago');
        $queryPendentes = Reserva::where('status', 'pendente');
        $queryEventos = Evento::query(); // Pega todos os status para o contador de eventos

        if (!$isAdmin) {
            // Filtra Reservas Pagas: Somente dos eventos onde eventos.user_id é o ID do criador
            $queryReservas->whereHas('tipoIngresso.evento', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });

            // Filtra Reservas Pendentes: Somente dos eventos deste criador
            $queryPendentes->whereHas('tipoIngresso.evento', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });

            // Filtra Eventos: Somente eventos criados por este usuário
            $queryEventos->where('user_id', $user->id);
        }

        // 2. TOTAIS GERAIS (USANDO CLONE PARA NÃO ESTRAGAR A QUERY ORIGINAL)
        $receitaTotal = (clone $queryReservas)->sum('total');
        $pendentesCount = (clone $queryPendentes)->count();
        $eventosAtivos = (clone $queryEventos)->where('status', 'publicado')->count();

        // 3. TOTAIS POR CATEGORIA (NORMAL VS VIP)
        $vendasNormalQtd = (clone $queryReservas)->whereHas('tipoIngresso', function($q) { 
            $q->where('nome', 'Normal'); 
        })->sum('quantidade');
        
        $vendasVipQtd = (clone $queryReservas)->whereHas('tipoIngresso', function($q) { 
            $q->where('nome', 'VIP'); 
        })->sum('quantidade');
        
        $valorTotalNormal = (clone $queryReservas)->whereHas('tipoIngresso', function($q) { 
            $q->where('nome', 'Normal'); 
        })->sum('total');
        
        $valorTotalVip = (clone $queryReservas)->whereHas('tipoIngresso', function($q) { 
            $q->where('nome', 'VIP'); 
        })->sum('total');

        // 4. CÁLCULOS PARA O GRÁFICO
        $totalIngressos = $vendasNormalQtd + $vendasVipQtd;
        $percNormal = $totalIngressos > 0 ? round(($vendasNormalQtd / $totalIngressos) * 100) : 0;
        $percVip = $totalIngressos > 0 ? round(($vendasVipQtd / $totalIngressos) * 100) : 0;

        // 5. LISTA DE VENDAS RECENTES (DETALHADAS)
        $vendasDetalhadas = (clone $queryReservas)
            ->with(['tipoIngresso.evento'])
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        // 6. PERFORMANCE POR SHOW
        $eventosPerformance = (clone $queryEventos)
            ->with(['tiposIngresso.reservas' => function($q) {
                $q->where('status', 'pago');
            }])
            ->get()->map(function($evento) {
                $resN = $evento->tiposIngresso->where('nome', 'Normal')->first();
                $resV = $evento->tiposIngresso->where('nome', 'VIP')->first();

                $valN = $resN ? $resN->reservas->sum('total') : 0;
                $valV = $resV ? $resV->reservas->sum('total') : 0;
                $qtdN = $resN ? $resN->reservas->sum('quantidade') : 0;
                $qtdV = $resV ? $resV->reservas->sum('quantidade') : 0;

                return (object)[
                    'titulo' => $evento->titulo,
                    'qtd_normal' => $qtdN,
                    'total_normal' => $valN,
                    'qtd_vip' => $qtdV,
                    'total_vip' => $valV,
                    'total_geral' => $valN + $valV,
                    'perc_vendas' => $evento->lotacao_maxima > 0 ? (($qtdN + $qtdV) / $evento->lotacao_maxima) * 100 : 0,
                    'perc_vip_vendas' => ($qtdN + $qtdV) > 0 ? ($qtdV / ($qtdN + $qtdV)) * 100 : 0,
                ];
            });

        return view('admin-dashboard', compact(
            'receitaTotal', 'valorTotalNormal', 'valorTotalVip', 'vendasNormalQtd', 
            'vendasVipQtd', 'pendentesCount', 'eventosAtivos', 'percNormal', 'percVip', 
            'vendasDetalhadas', 'eventosPerformance'
        ));
    }
}