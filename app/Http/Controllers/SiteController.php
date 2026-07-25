<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SiteController extends Controller
{
    public function adminDashboard()
    {
        $user    = auth()->user();
        $isAdmin = $user->role === 'admin';

        $queryReservas  = Reserva::where('status', 'pago');
        $queryPendentes = Reserva::where('status', 'pendente');
        $queryEventos   = Evento::query();

        if (!$isAdmin) {
            $queryReservas->whereHas('tipoIngresso.evento',  fn($q) => $q->where('user_id', $user->id));
            $queryPendentes->whereHas('tipoIngresso.evento', fn($q) => $q->where('user_id', $user->id));
            $queryEventos->where('user_id', $user->id);
        }

        // ── TOTAIS GERAIS ──────────────────────────────────────────
        $receitaTotal   = (clone $queryReservas)->sum('total');
        $pendentesCount = (clone $queryPendentes)->count();
        $eventosAtivos  = (clone $queryEventos)->where('status', 'publicado')->count();

        // ── VENDAS POR TIPO ────────────────────────────────────────
        $vendasPorTipo = (clone $queryReservas)
            ->join('tipo_ingressos', 'reservas.tipo_ingresso_id', '=', 'tipo_ingressos.id')
            ->whereIn('tipo_ingressos.nome', ['Normal', 'VIP'])
            ->select(
                'tipo_ingressos.nome',
                DB::raw('SUM(reservas.quantidade) as total_qtd'),
                DB::raw('SUM(reservas.total) as total_valor')
            )
            ->groupBy('tipo_ingressos.nome')
            ->get()->keyBy('nome');

        $vendasNormalQtd  = $vendasPorTipo->get('Normal')?->total_qtd  ?? 0;
        $valorTotalNormal = $vendasPorTipo->get('Normal')?->total_valor ?? 0;
        $vendasVipQtd     = $vendasPorTipo->get('VIP')?->total_qtd     ?? 0;
        $valorTotalVip    = $vendasPorTipo->get('VIP')?->total_valor    ?? 0;

        $totalIngressos = $vendasNormalQtd + $vendasVipQtd;
        $percNormal = $totalIngressos > 0 ? round(($vendasNormalQtd / $totalIngressos) * 100) : 0;
        $percVip    = $totalIngressos > 0 ? round(($vendasVipQtd    / $totalIngressos) * 100) : 0;

        // ── VENDAS RECENTES ────────────────────────────────────────
        $vendasDetalhadas = (clone $queryReservas)
            ->with([
                'tipoIngresso:id,evento_id,nome,preco',
                'tipoIngresso.evento:id,titulo,user_id',
            ])
            ->select('id','tipo_ingresso_id','user_id','nome_cliente','whatsapp','quantidade','total','updated_at','comprovativo_path')
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        // ── PERFORMANCE POR EVENTO ────────────────────────────────
        $eventosPerformance = (clone $queryEventos)
            ->with([
                'tiposIngresso:id,evento_id,nome,quantidade_total',
                'tiposIngresso.reservas' => fn($q) => $q->where('status','pago')->select('id','tipo_ingresso_id','quantidade','total'),
            ])
            ->select('id','titulo','lotacao_maxima')
            ->get()
            ->map(function ($evento) {
                $resN = $evento->tiposIngresso->where('nome','Normal')->first();
                $resV = $evento->tiposIngresso->where('nome','VIP')->first();
                $valN = $resN ? $resN->reservas->sum('total')      : 0;
                $valV = $resV ? $resV->reservas->sum('total')      : 0;
                $qtdN = $resN ? $resN->reservas->sum('quantidade') : 0;
                $qtdV = $resV ? $resV->reservas->sum('quantidade') : 0;
                return (object)[
                    'titulo'          => $evento->titulo,
                    'qtd_normal'      => $qtdN, 'total_normal'    => $valN,
                    'qtd_vip'         => $qtdV, 'total_vip'       => $valV,
                    'total_geral'     => $valN + $valV,
                    'perc_vendas'     => $evento->lotacao_maxima > 0 ? (($qtdN+$qtdV)/$evento->lotacao_maxima)*100 : 0,
                    'perc_vip_vendas' => ($qtdN+$qtdV) > 0 ? ($qtdV/($qtdN+$qtdV))*100 : 0,
                ];
            });

        // ── SEGURANÇA — dados novos ───────────────────────────────
        $totalBilhetes     = DB::table('bilhetes')->count();
        $bilhetesComHmac   = DB::table('bilhetes')->whereNotNull('hmac_assinatura')->count();
        $bilhetesBloqueados= DB::table('bilhetes')->where('bloqueado', true)->count();
        $tentativasInvalidas = DB::table('bilhetes_auditoria')->where('acao','tentativa_invalida')->count() +
                               DB::table('bilhetes_auditoria')->where('acao','hmac_invalido')->count();
        $bilhetesValidados  = DB::table('bilhetes')->whereNotNull('validado_em')->count();
        $lotesEmitidos      = DB::table('bilhetes_lotes')->count();

        // ── UTILIZADORES (só admin vê) ────────────────────────────
        $totalUtilizadores = $isAdmin ? User::count() : null;
        $novosUtilizadores = $isAdmin ? User::where('created_at','>=',now()->subDays(30))->count() : null;

        // ── EVENTOS POR STATUS ────────────────────────────────────
        $eventosPorStatus = (clone $queryEventos)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total','status');

        return view('admin-dashboard', compact(
            'receitaTotal','valorTotalNormal','valorTotalVip',
            'vendasNormalQtd','vendasVipQtd','pendentesCount',
            'eventosAtivos','percNormal','percVip',
            'vendasDetalhadas','eventosPerformance',
            'totalBilhetes','bilhetesComHmac','bilhetesBloqueados',
            'tentativasInvalidas','bilhetesValidados','lotesEmitidos',
            'totalUtilizadores','novosUtilizadores','eventosPorStatus',
            'isAdmin'
        ));
    }

    public function analisesSistema()
    {
        $user    = auth()->user();
        $isAdmin = $user->role === 'admin';

        // ── AUDITORIA RECENTE ─────────────────────────────────────
        $auditoria = DB::table('bilhetes_auditoria as ba')
            ->join('bilhetes as b', 'ba.bilhete_id', '=', 'b.id')
            ->leftJoin('users as u', 'ba.validado_por', '=', 'u.id')
            ->select(
                'ba.id','ba.acao','ba.codigo_unico','ba.scanner_ip',
                'ba.created_at','u.name as scanner_nome',
                'b.evento_id','b.lote_id'
            )
            ->orderBy('ba.created_at','desc')
            ->limit(50)
            ->get();

        // ── BILHETES BLOQUEADOS ───────────────────────────────────
        $bilhetesBloqueados = DB::table('bilhetes as b')
            ->leftJoin('pedidos as p', 'b.pedido_id', '=', 'p.id')
            ->leftJoin('eventos as e', 'b.evento_id', '=', 'e.id')
            ->where('b.bloqueado', true)
            ->select('b.id','b.codigo_unico','b.tentativas_invalidas','b.lote_id','e.titulo as evento','b.updated_at')
            ->orderBy('b.updated_at','desc')
            ->limit(20)
            ->get();

        // ── LOTES EMITIDOS ────────────────────────────────────────
        $lotes = DB::table('bilhetes_lotes as bl')
            ->join('eventos as e', 'bl.evento_id', '=', 'e.id')
            ->leftJoin('users as u', 'bl.user_id', '=', 'u.id')
            ->select('bl.lote_id','bl.quantidade','bl.total','bl.emitido_em','bl.emitido_por_ip','e.titulo as evento','u.name as cliente')
            ->orderBy('bl.emitido_em','desc')
            ->limit(30)
            ->get();

        // ── STATS DE SEGURANÇA ────────────────────────────────────
        $statsSeguranca = [
            'total'           => DB::table('bilhetes')->count(),
            'com_hmac'        => DB::table('bilhetes')->whereNotNull('hmac_assinatura')->count(),
            'validados'       => DB::table('bilhetes')->whereNotNull('validado_em')->count(),
            'bloqueados'      => DB::table('bilhetes')->where('bloqueado',true)->count(),
            'tentativas'      => DB::table('bilhetes_auditoria')->whereIn('acao',['tentativa_invalida','hmac_invalido'])->count(),
            'lotes'           => DB::table('bilhetes_lotes')->count(),
        ];

        // ── ACTIVIDADE POR HORA (últimas 24h) ────────────────────
        $actividadeHoras = DB::table('bilhetes_auditoria')
            ->where('created_at','>=',now()->subHours(24))
            ->select(DB::raw('HOUR(created_at) as hora'), DB::raw('count(*) as total'))
            ->groupBy('hora')
            ->orderBy('hora')
            ->pluck('total','hora');

        // ── TOP EVENTOS POR BILHETES VENDIDOS ────────────────────
        $topEventos = DB::table('bilhetes as b')
            ->join('eventos as e','b.evento_id','=','e.id')
            ->select('e.titulo', DB::raw('count(b.id) as total_bilhetes'), DB::raw('count(CASE WHEN b.validado_em IS NOT NULL THEN 1 END) as validados'))
            ->groupBy('e.id','e.titulo')
            ->orderBy('total_bilhetes','desc')
            ->limit(10)
            ->get();

        return view('admin-analises', compact(
            'auditoria','bilhetesBloqueados','lotes',
            'statsSeguranca','actividadeHoras','topEventos','isAdmin'
        ));
    }

    public function relatorioPdf()
    {
        $auditoria = \Illuminate\Support\Facades\DB::table('bilhetes_auditoria as ba')
            ->leftJoin('users as u', 'ba.validado_por', '=', 'u.id')
            ->select('ba.*', 'u.name as scanner_nome')
            ->orderBy('ba.created_at', 'desc')
            ->get();

        $bilhetesBloqueados = \Illuminate\Support\Facades\DB::table('bilhetes as b')
            ->leftJoin('eventos as e', 'b.evento_id', '=', 'e.id')
            ->where('b.bloqueado', true)
            ->select('b.codigo_unico','b.tentativas_invalidas','b.lote_id','b.updated_at','e.titulo as evento')
            ->orderBy('b.updated_at', 'desc')
            ->get();

        $stats = [
            'total'      => \Illuminate\Support\Facades\DB::table('bilhetes')->count(),
            'com_hmac'   => \Illuminate\Support\Facades\DB::table('bilhetes')->whereNotNull('hmac_assinatura')->count(),
            'validados'  => \Illuminate\Support\Facades\DB::table('bilhetes')->whereNotNull('validado_em')->count(),
            'bloqueados' => \Illuminate\Support\Facades\DB::table('bilhetes')->where('bloqueado', true)->count(),
            'tentativas' => \Illuminate\Support\Facades\DB::table('bilhetes_auditoria')->whereIn('acao', ['tentativa_invalida','hmac_invalido'])->count(),
            'lotes'      => \Illuminate\Support\Facades\DB::table('bilhetes_lotes')->count(),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('relatorio-auditoria', compact('auditoria','bilhetesBloqueados','stats'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('relatorio-auditoria-'.now()->format('Ymd-His').'.pdf');
    }

    public function relatorioBilhete(string $codigo)
    {
        $bilhete = \App\Models\Bilhete::with(['evento:id,titulo','tipoIngresso:id,nome'])
            ->where('codigo_unico', $codigo)
            ->firstOrFail();

        $historico = \Illuminate\Support\Facades\DB::table('bilhetes_auditoria as ba')
            ->leftJoin('users as u', 'ba.validado_por', '=', 'u.id')
            ->where('ba.codigo_unico', $codigo)
            ->select('ba.*', 'u.name as scanner_nome')
            ->orderBy('ba.created_at', 'asc')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('relatorio-bilhete', compact('bilhete','historico'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('bilhete-'.$codigo.'.pdf');
    }

}