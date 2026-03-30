<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteController extends Controller
{
    public function adminDashboard()
    {
        $user    = auth()->user();
        $isAdmin = $user->role === 'admin';

        // 1. QUERIES BASE — lógica intacta
        $queryReservas  = Reserva::where('status', 'pago');
        $queryPendentes = Reserva::where('status', 'pendente');
        $queryEventos   = Evento::query();

        if (!$isAdmin) {
            $queryReservas->whereHas('tipoIngresso.evento', fn($q) => $q->where('user_id', $user->id));
            $queryPendentes->whereHas('tipoIngresso.evento', fn($q) => $q->where('user_id', $user->id));
            $queryEventos->where('user_id', $user->id);
        }

        // 2. TOTAIS GERAIS
        $receitaTotal  = (clone $queryReservas)->sum('total');
        $pendentesCount = (clone $queryPendentes)->count();
        $eventosAtivos = (clone $queryEventos)->where('status', 'publicado')->count();

        // ✅ Substituir 4 queries separadas por 1 única query agrupada
        // Antes: 4 queries (whereHas Normal qtd, VIP qtd, Normal total, VIP total)
        // Depois: 1 query com GROUP BY
        $vendasPorTipo = (clone $queryReservas)
            ->join('tipo_ingressos', 'reservas.tipo_ingresso_id', '=', 'tipo_ingressos.id')
            ->whereIn('tipo_ingressos.nome', ['Normal', 'VIP'])
            ->select(
                'tipo_ingressos.nome',
                DB::raw('SUM(reservas.quantidade) as total_qtd'),
                DB::raw('SUM(reservas.total) as total_valor')
            )
            ->groupBy('tipo_ingressos.nome')
            ->get()
            ->keyBy('nome');

        $vendasNormalQtd  = $vendasPorTipo->get('Normal')?->total_qtd  ?? 0;
        $valorTotalNormal = $vendasPorTipo->get('Normal')?->total_valor ?? 0;
        $vendasVipQtd     = $vendasPorTipo->get('VIP')?->total_qtd     ?? 0;
        $valorTotalVip    = $vendasPorTipo->get('VIP')?->total_valor    ?? 0;

        // 4. CÁLCULOS PARA O GRÁFICO — lógica intacta
        $totalIngressos = $vendasNormalQtd + $vendasVipQtd;
        $percNormal = $totalIngressos > 0 ? round(($vendasNormalQtd / $totalIngressos) * 100) : 0;
        $percVip    = $totalIngressos > 0 ? round(($vendasVipQtd    / $totalIngressos) * 100) : 0;

        // 5. VENDAS RECENTES — select específico nas relações
        $vendasDetalhadas = (clone $queryReservas)
            ->with([
                'tipoIngresso:id,evento_id,nome,preco',
                'tipoIngresso.evento:id,titulo,user_id',
            ])
            ->select('id', 'tipo_ingresso_id', 'user_id', 'nome_cliente', 'quantidade', 'total', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        // 6. PERFORMANCE POR EVENTO
        // ✅ Select específico + eager loading otimizado
        $eventosPerformance = (clone $queryEventos)
            ->with([
                'tiposIngresso:id,evento_id,nome,quantidade_total',
                'tiposIngresso.reservas' => function ($q) {
                    $q->where('status', 'pago')
                      ->select('id', 'tipo_ingresso_id', 'quantidade', 'total');
                },
            ])
            ->select('id', 'titulo', 'lotacao_maxima')
            ->get()
            ->map(function ($evento) {
                $resN = $evento->tiposIngresso->where('nome', 'Normal')->first();
                $resV = $evento->tiposIngresso->where('nome', 'VIP')->first();

                $valN = $resN ? $resN->reservas->sum('total')      : 0;
                $valV = $resV ? $resV->reservas->sum('total')      : 0;
                $qtdN = $resN ? $resN->reservas->sum('quantidade') : 0;
                $qtdV = $resV ? $resV->reservas->sum('quantidade') : 0;

                return (object) [
                    'titulo'          => $evento->titulo,
                    'qtd_normal'      => $qtdN,
                    'total_normal'    => $valN,
                    'qtd_vip'         => $qtdV,
                    'total_vip'       => $valV,
                    'total_geral'     => $valN + $valV,
                    'perc_vendas'     => $evento->lotacao_maxima > 0
                        ? (($qtdN + $qtdV) / $evento->lotacao_maxima) * 100 : 0,
                    'perc_vip_vendas' => ($qtdN + $qtdV) > 0
                        ? ($qtdV / ($qtdN + $qtdV)) * 100 : 0,
                ];
            });

        return view('admin-dashboard', compact(
            'receitaTotal', 'valorTotalNormal', 'valorTotalVip', 'vendasNormalQtd',
            'vendasVipQtd', 'pendentesCount', 'eventosAtivos', 'percNormal', 'percVip',
            'vendasDetalhadas', 'eventosPerformance'
        ));
    }
}