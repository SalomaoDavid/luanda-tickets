<?php

namespace App\Http\Controllers;

use App\Models\ContaBancaria;
use App\Models\DadosBancariosCriador;
use App\Models\SaldoCriador;
use App\Models\Reserva;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\DadosBancariosRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SaldoController extends Controller
{
    // ── Registar divisão quando reserva é confirmada ─────────────
    // Chamado pelo BookingController quando o admin confirma
    public static function registarDivisao(Reserva $reserva): void
    {
        // Evitar duplicados
        if (SaldoCriador::where('reserva_id', $reserva->id)->exists()) return;

        $evento  = $reserva->evento;
        $criador = $evento->user;
        $total   = $reserva->total ?? ($reserva->quantidade * $reserva->preco_unitario);

        $valorAdmin   = round($total * 0.10, 2);
        $valorCriador = round($total * 0.90, 2);

        SaldoCriador::create([
            'reserva_id'    => $reserva->id,
            'criador_id'    => $criador->id,
            'evento_id'     => $evento->id,
            'valor_total'   => $total,
            'valor_admin'   => $valorAdmin,
            'valor_criador' => $valorCriador,
            'estado'        => 'pendente',
        ]);
    }

    // ── PAINEL ADMIN — listar saldos pendentes ────────────────────
    public function index()
    {
        $saldosPendentes = SaldoCriador::with(['criador:id,name','evento:id,titulo'])
            ->where('estado', 'pendente')
            ->latest()
            ->paginate(20);

        $totalPendente = SaldoCriador::where('estado','pendente')->sum('valor_criador');
        $totalPago     = SaldoCriador::where('estado','pago')->sum('valor_criador');
        $totalAdmin    = SaldoCriador::sum('valor_admin');

        // Agrupar por criador
        $porCriador = SaldoCriador::with([
                'criador:id,name',
                'criador.dadosBancarios:user_id,nome_banco,titular,iban,numero_conta',
            ])
            ->where('estado','pendente')
            ->select('criador_id', DB::raw('SUM(valor_criador) as total_devido'), DB::raw('COUNT(*) as num_reservas'))
            ->groupBy('criador_id')
            ->orderByDesc('total_devido')
            ->get();

        return view('admin.saldos', compact(
            'saldosPendentes','porCriador',
            'totalPendente','totalPago','totalAdmin'
        ));
    }

    // ── Marcar como pago ─────────────────────────────────────────
    public function marcarPago(Request $request, $criadorId)
    {
        $request->validate([
            'referencia' => 'nullable|string|max:100',
        ]);

        SaldoCriador::where('criador_id', $criadorId)
            ->where('estado', 'pendente')
            ->update([
                'estado'                   => 'pago',
                'pago_em'                  => now(),
                'referencia_transferencia' => $request->referencia,
            ]);

        return back()->with('success', 'Pagamento registado com sucesso!');
    }

    // ── Gerir contas bancárias (CRUD) ─────────────────────────────
    public function contasBancarias()
    {
        $contas = ContaBancaria::orderBy('ordem')->get();
        return view('admin.contas-bancarias', compact('contas'));
    }

    public function storeConta(Request $request)
    {
        $request->validate([
            'nome_banco'   => 'required|string|max:100',
            'titular'      => 'required|string|max:150',
            'iban'         => 'required|string|max:50|unique:contas_bancarias',
            'numero_conta' => 'nullable|string|max:50',
            'logo'         => 'nullable|image|mimes:png,jpg,webp|max:512',
            'ordem'        => 'nullable|integer',
        ]);

        $logo = null;
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo')->getClientOriginalName();
            $request->file('logo')->move(public_path('images/bancos'), $logo);
        }

        ContaBancaria::create([
            'nome_banco'   => $request->nome_banco,
            'titular'      => $request->titular,
            'iban'         => strtoupper($request->iban),
            'numero_conta' => $request->numero_conta,
            'logo'         => $logo,
            'ordem'        => $request->ordem ?? 0,
            'activa'       => true,
        ]);

        return back()->with('success', 'Conta bancária adicionada!');
    }

    public function updateConta(Request $request, $id)
    {
        $conta = ContaBancaria::findOrFail($id);

        $request->validate([
            'nome_banco'   => 'required|string|max:100',
            'titular'      => 'required|string|max:150',
            'iban'         => 'required|string|max:50|unique:contas_bancarias,iban,'.$id,
            'numero_conta' => 'nullable|string|max:50',
            'logo'         => 'nullable|image|mimes:png,jpg,webp|max:512',
            'activa'       => 'boolean',
            'ordem'        => 'nullable|integer',
        ]);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo')->getClientOriginalName();
            $request->file('logo')->move(public_path('images/bancos'), $logo);
            $conta->logo = $logo;
        }

        $conta->update([
            'nome_banco'   => $request->nome_banco,
            'titular'      => $request->titular,
            'iban'         => strtoupper($request->iban),
            'numero_conta' => $request->numero_conta,
            'activa'       => $request->boolean('activa'),
            'ordem'        => $request->ordem ?? $conta->ordem,
        ]);

        return back()->with('success', 'Conta actualizada!');
    }

    public function destroyConta($id)
    {
        ContaBancaria::findOrFail($id)->delete();
        return back()->with('success', 'Conta removida.');
    }

    // ── Criador guarda os seus dados bancários ────────────────────
    public function guardarDadosCriador(DadosBancariosRequest $request)
    {
        $validated = $request->validated();

        DadosBancariosCriador::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'nome_banco'   => $validated['nome_banco'],
                'titular'      => $validated['titular'],
                'iban'         => strtoupper($validated['iban']),
                'numero_conta' => $validated['numero_conta'] ?? null,
            ]
        );

        return redirect()->route('profile.edit')
            ->with('status', 'dados-bancarios-guardados');
    }
}