<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <style>
        /* Reset para o DomPDF ocupar o papel personalizado */
        @page { 
            margin: 0; 
        }
        
        body { 
            font-family: 'Helvetica', sans-serif; 
            margin: 0; 
            padding: 0; 
            background-color: #020617; /* Azul quase preto */
            color: #ffffff;
        }

        .ticket-wrapper {
            width: 100%;
            height: 100%;
            position: relative;
            /* Simulação do brilho azul da foto */
            background: radial-gradient(circle at 100% 0%, #1e3a8a 0%, #020617 70%);
        }

        /* --- Imagem de Capa do Evento --- */
        .event-banner {
            width: 100%;
            height: 220px;
            overflow: hidden;
            position: relative;
            border-bottom: 3px solid #fbbf24; /* Linha dourada divisória */
        }
        
        .event-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* --- Conteúdo do Bilhete --- */
        .content {
            padding: 25px;
            position: relative;
        }

        .event-title {
            font-size: 28px;
            font-weight: bold;
            color: #fbbf24; /* Dourado Premium */
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }

        .event-subtitle {
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }

        /* --- Tabela de Detalhes (Grid) --- */
        .info-table {
            width: 70%; /* Abre espaço para o QR Code à direita */
            border-collapse: collapse;
        }

        .info-label {
            font-size: 10px;
            color: #fbbf24;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 14px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 15px;
        }

        /* --- Secção do QR Code --- */
        .qr-container {
            position: absolute;
            right: 25px;
            top: 40px;
            background: #ffffff;
            padding: 12px;
            border-radius: 15px;
            text-align: center;
        }

        .qr-container img {
            display: block;
        }

        .qr-container span {
            display: block;
            color: #020617;
            font-size: 9px;
            font-weight: bold;
            margin-top: 8px;
        }

        /* --- Rodapé --- */
        .footer {
            position: absolute;
            bottom: 15px;
            left: 25px;
            width: 90%;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 10px;
            font-size: 9px;
            color: #64748b;
        }

        .ticket-type-badge {
            background-color: #2563eb;
            color: white;
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="ticket-wrapper">
        
        <div class="event-banner">
            @if($capaBase64)
                <img src="data:{{ $tipoMime }};base64,{{ $capaBase64 }}">
            @else
                <div style="background: #1e293b; width: 100%; height: 100%;"></div>
            @endif
        </div>

        <div class="content">
            <div class="ticket-type-badge">
                {{ $bilhete->tipoIngresso->nome ?? 'INGRESSO GERAL' }}
            </div>

            <h1 class="event-title">{{ $bilhete->evento->titulo }}</h1>
            <p class="event-subtitle">LUANDA TICKETS - EXPERIÊNCIA EXCLUSIVA</p>

            <table class="info-table">
                <tr>
                    <td>
                        <div class="info-label">Data e Hora</div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($bilhete->evento->data)->format('d/m/Y - H:i') }}
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="info-label">Localização</div>
                        <div class="info-value">{{ $bilhete->evento->localizacao }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="info-label">Titular do Bilhete</div>
                        <div class="info-value">{{ $bilhete->pedido->user->name ?? 'N/A' }}</div>
                    </td>
                </tr>
            </table>

            <div class="qr-container">
                <img src="data:image/svg+xml;base64,{{ $bilhete->qr_code }}" width="110">
                <span>VALIDAR ENTRADA</span>
            </div>

            <div class="footer">
                Código Único: {{ $bilhete->codigo_unico }} | Emitido por Luanda Tickets em {{ date('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</body>
</html>