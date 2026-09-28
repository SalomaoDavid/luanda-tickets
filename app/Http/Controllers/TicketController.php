<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Bilhete;
use App\Services\BilheteService;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function download($pedidoId)
    {
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
        $bilhete = Bilhete::with([
            'evento:id,titulo,localizacao,data_evento,hora_inicio,hora_fim,imagem_capa,user_id,descricao,categoria_id,meta',
            'evento.user:id,name,avatar',
            'evento.categoria:id,nome',
            'tipoIngresso:id,nome,preco,dias_validos',
            'pedido:id,user_id',
        ])
            ->select('id', 'pedido_id', 'evento_id', 'tipo_ingressos_id', 'codigo_unico', 'validado_em', 'numero_dia')
            ->findOrFail($id);

        if (!$bilhete->pedido || $bilhete->pedido->user_id !== auth()->id()) {
            abort(403, 'Este bilhete não pertence à sua conta.');
        }

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

        // ✅ Foto/ícone do criador do evento — mesmo padrão de base64 usado na
        // capa acima, porque o motor de PDF não consegue carregar URLs remotas.
        $criadorAvatarBase64 = null;
        $criadorAvatarMime   = null;

        if (
            $bilhete->evento &&
            $bilhete->evento->user &&
            $bilhete->evento->user->avatar &&
            Storage::disk('public')->exists($bilhete->evento->user->avatar)
        ) {
            try {
                $caminhoAvatar        = Storage::disk('public')->path($bilhete->evento->user->avatar);
                $conteudoAvatar       = file_get_contents($caminhoAvatar);
                $criadorAvatarBase64  = base64_encode($conteudoAvatar);
                $criadorAvatarMime    = Storage::disk('public')->mimeType($bilhete->evento->user->avatar);
            } catch (\Exception $e) {
                \Log::error("Erro ao processar avatar do criador no PDF: " . $e->getMessage());
            }
        }

        $bilhete->qr_code = base64_encode(
            QrCode::format('svg')->size(200)->margin(1)->errorCorrection('H')->generate($bilhete->codigo_unico)
        );

        $pdf = Pdf::loadView('pdf.bilhete-unico', compact(
            'bilhete',
            'capaBase64',
            'tipoMime',
            'criadorAvatarBase64',
            'criadorAvatarMime'
        ));
        $pdf->setPaper([0, 0, 750, 310], 'landscape');

        return $pdf->download("bilhete-{$bilhete->codigo_unico}.pdf");
    }

    public function eliminar($id)
    {
        // Eager load — evita N+1, só colunas necessárias
        $bilhete = Bilhete::with('pedido:id,user_id')
            ->select('id', 'pedido_id', 'codigo_unico', 'validado_em')
            ->findOrFail($id);

        // Verificação de autorização
        if (!$bilhete->pedido || $bilhete->pedido->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não tens permissão para eliminar este bilhete.',
            ], 403);
        }

        // Só permite eliminar bilhetes já utilizados
        if (!$bilhete->validado_em) {
            return response()->json([
                'success' => false,
                'message' => 'Só podes eliminar bilhetes que já foram utilizados.',
            ], 422);
        }

        // Registar na auditoria antes de eliminar
        BilheteService::registarAuditoria(
            $bilhete->id,
            'eliminado_pelo_utilizador',
            $bilhete->codigo_unico,
            [
                'motivo'      => 'Utilizador eliminou o bilhete após utilização',
                'user_id'     => auth()->id(),
                'validado_em' => $bilhete->validado_em,
            ]
        );

        // Soft delete — registo mantido na BD para auditoria do admin
        $bilhete->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bilhete eliminado com sucesso.',
        ]);
    }
}