<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking; // Assumindo que sua tabela de reservas é Booking
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    // Renderiza a página da câmera
    public function index()
    {
        return view('admin.scanner-visual');
    }

    // Processa a validação do QR Code
    public function validar(Request $request)
    {
        $codigo = $request->codigo;

        // Procura a reserva pelo código único
        $reserva = Booking::where('codigo_bilhete', $codigo)->first();

        if (!$reserva) {
            return response()->json(['status' => 'error', 'message' => 'Bilhete Inválido ou Inexistente!'], 404);
        }

        if ($reserva->status === 'utilizado') {
            return response()->json(['status' => 'warning', 'message' => 'Este bilhete já foi usado em: ' . $reserva->updated_at->format('d/m H:i')], 200);
        }

        if ($reserva->status !== 'pago') {
            return response()->json(['status' => 'error', 'message' => 'Pagamento não confirmado para este bilhete.'], 400);
        }

        // Marca como utilizado
        $reserva->update(['status' => 'utilizado']);

        return response()->json([
            'status' => 'success',
            'message' => 'Entrada Liberada! Bem-vindo ao evento.',
            'cliente' => $reserva->user->name ?? 'Convidado',
            'evento' => $reserva->evento->titulo
        ]);
    }
}