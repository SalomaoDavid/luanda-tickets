<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Bilhete;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function download($pedidoId)
    {
        // ✅ Select específico nas relações — só colunas necessárias para o PDF
        $pedido = Pedido::with([
            'bilhetes:id,pedido_id,evento_id,tipo_ingressos_id,codigo_unico',
            'bilhetes.evento:id,titulo,localizacao,data_evento,imagem_capa',
            'bilhetes.tipoIngresso:id,nome,preco',
            'user:id,name,email',
        ])
            ->select('id', 'user_id', 'total_pago', 'metodo_pagamento', 'created_at')
            ->where('id', $pedidoId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Geração de QR Codes — lógica intacta
        foreach ($pedido->bilhetes as $bilhete) {
            $bilhete->qr_code = base64_encode(
                QrCode::format('svg')->size(150)->errorCorrection('H')->generate($bilhete->codigo_unico)
            );
        }

        $pdf = Pdf::loadView('pdf.meus-bilhetes', compact('pedido'));

        return $pdf->download("Bilhetes_LuandaTickets_{$pedido->id}.pdf");
    }

    public function downloadIndividual($id)
    {
        // ✅ Select específico nas relações
        $bilhete = Bilhete::with([
            'evento:id,titulo,localizacao,data_evento,imagem_capa',
            'tipoIngresso:id,nome,preco',
            'pedido:id,user_id',
        ])
            ->select('id', 'pedido_id', 'evento_id', 'tipo_ingressos_id', 'codigo_unico', 'validado_em')
            ->findOrFail($id);

        // Segurança — lógica intacta
        if (!$bilhete->pedido || $bilhete->pedido->user_id !== auth()->id()) {
            abort(403, 'Este bilhete não pertence à sua conta.');
        }

        // Processar imagem de capa — lógica intacta
        $capaBase64 = null;
        $tipoMime   = null;

        if (
            $bilhete->evento &&
            $bilhete->evento->imagem_capa &&
            Storage::disk('public')->exists($bilhete->evento->imagem_capa)
        ) {
            try {
                $caminhoImagem  = Storage::disk('public')->path($bilhete->evento->imagem_capa);
                $conteudoImagem = file_get_contents($caminhoImagem);
                $capaBase64     = base64_encode($conteudoImagem);
                $tipoMime       = Storage::disk('public')->mimeType($bilhete->evento->imagem_capa);
            } catch (\Exception $e) {
                \Log::error("Erro ao processar imagem de capa no PDF: " . $e->getMessage());
            }
        }

        // Geração de QR Code — lógica intacta
        $bilhete->qr_code = base64_encode(
            QrCode::format('svg')->size(200)->margin(1)->errorCorrection('H')->generate($bilhete->codigo_unico)
        );

        $pdf = Pdf::loadView('pdf.bilhete-unico', compact('bilhete', 'capaBase64', 'tipoMime'));
        $pdf->setPaper([0, 0, 750, 310], 'landscape');

        return $pdf->download("bilhete-{$bilhete->codigo_unico}.pdf");
    }
    public function eliminar($id) {
    $bilhete = Bilhete::findOrFail($id);
    if ($bilhete->pedido->user_id !== auth()->id()) abort(403);
    $bilhete->delete();
    return back()->with('success', 'Bilhete eliminado.');
}
}