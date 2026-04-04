<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
            size: 750pt 310pt landscape;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Helvetica', Arial, sans-serif;
            background: #020c1b;
            color: #ffffff;
            width: 750pt;
            height: 310pt;
            overflow: hidden;
        }

        /* ── FUNDO PRINCIPAL ── */
        .ticket {
            width: 750pt;
            height: 310pt;
            position: relative;
            background: #020c1b;
            overflow: hidden;
        }

        /* Camada de brilho azul lateral esquerdo */
        .glow-left {
            position: absolute;
            left: -60pt;
            top: -40pt;
            width: 320pt;
            height: 320pt;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(30,60,150,0.55) 0%, transparent 70%);
        }

        /* Camada de brilho azul direito */
        .glow-right {
            position: absolute;
            right: 80pt;
            top: -60pt;
            width: 250pt;
            height: 250pt;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(20,80,180,0.35) 0%, transparent 70%);
        }

        /* Linha azul brilhante no topo */
        .line-top {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2pt;
            background: linear-gradient(to right, transparent, #1e6abf, #38bdf8, #1e6abf, transparent);
        }

        /* Linha dourada no fundo */
        .line-bottom {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 2pt;
            background: linear-gradient(to right, transparent, #b8860b, #fbbf24, #b8860b, transparent);
        }

        /* Linha vertical separadora (picote) */
        .separator {
            position: absolute;
            right: 175pt;
            top: 10pt;
            bottom: 10pt;
            width: 1pt;
            border-left: 1.5pt dashed rgba(255,255,255,0.18);
        }

        /* ── CABEÇALHO ── */
        .header {
            position: absolute;
            top: 14pt;
            left: 0; right: 180pt;
            text-align: center;
            z-index: 10;
        }

        .header-brand {
            font-size: 10pt;
            font-weight: bold;
            letter-spacing: 4pt;
            color: #fbbf24;
            text-transform: uppercase;
            border-bottom: 0.5pt solid rgba(251,191,36,0.35);
            display: inline-block;
            padding-bottom: 4pt;
            padding-left: 12pt;
            padding-right: 12pt;
        }

        /* Decoração de tracinhos ao lado do LUANDA TICKETS */
        .brand-line {
            display: inline-block;
            width: 22pt;
            height: 1pt;
            background: #fbbf24;
            vertical-align: middle;
            margin: 0 5pt;
        }

        /* ── TÍTULO DO EVENTO ── */
        .event-title-wrap {
            position: absolute;
            top: 38pt;
            left: 10pt;
            right: 185pt;
            text-align: center;
            z-index: 10;
        }

        .event-title {
            font-size: 30pt;
            font-weight: bold;
            color: #fbbf24;
            text-transform: uppercase;
            letter-spacing: 1.5pt;
            line-height: 1.1;
            text-shadow: 0 0 20pt rgba(251,191,36,0.4);
        }

        .event-subtitle {
            font-size: 8.5pt;
            color: #94a3b8;
            font-style: italic;
            margin-top: 4pt;
            letter-spacing: 0.5pt;
        }

        /* ── IMAGEM FUNDO (capa do evento) ── */
        .bg-image {
            position: absolute;
            top: 0; left: 0; right: 180pt; bottom: 0;
            z-index: 1;
            overflow: hidden;
        }

        .bg-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.18;
        }

        /* ── COLUNA ESQUERDA: informações ── */
        .col-left {
            position: absolute;
            left: 14pt;
            top: 106pt;
            width: 200pt;
            z-index: 10;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 8pt;
            gap: 5pt;
        }

        .info-icon {
            font-size: 10pt;
            width: 14pt;
            flex-shrink: 0;
            margin-top: 1pt;
        }

        .info-label {
            font-size: 7.5pt;
            color: #fbbf24;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
        }

        .info-value {
            font-size: 10pt;
            color: #ffffff;
            font-weight: bold;
            margin-top: 1pt;
        }

        /* ── COLUNA CENTRO: tipo + nome + regras ── */
        .col-center {
            position: absolute;
            left: 225pt;
            top: 106pt;
            width: 195pt;
            z-index: 10;
        }

        .badge-tipo {
            background: transparent;
            border: 1pt solid #fbbf24;
            color: #fbbf24;
            font-size: 8.5pt;
            font-weight: bold;
            letter-spacing: 1pt;
            text-transform: uppercase;
            padding: 3pt 10pt;
            border-radius: 20pt;
            display: inline-block;
            margin-bottom: 8pt;
        }

        .center-label {
            font-size: 7.5pt;
            color: #fbbf24;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            margin-bottom: 1pt;
            margin-top: 6pt;
        }

        .center-value {
            font-size: 10pt;
            color: #ffffff;
            font-weight: bold;
        }

        .rules-title {
            font-size: 7.5pt;
            color: #fbbf24;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
            margin-top: 10pt;
            margin-bottom: 4pt;
        }

        .rule-item {
            font-size: 7.5pt;
            color: #94a3b8;
            margin-bottom: 2.5pt;
            padding-left: 8pt;
            position: relative;
        }

        .rule-item::before {
            content: '•';
            position: absolute;
            left: 0;
            color: #fbbf24;
        }

        /* ── COLUNA QR ── */
        .col-qr {
            position: absolute;
            right: 0;
            top: 0;
            width: 175pt;
            height: 310pt;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.03);
        }

        .qr-wrap {
            background: #ffffff;
            padding: 8pt;
            border-radius: 8pt;
        }

        .qr-wrap img {
            display: block;
            width: 105pt;
            height: 105pt;
        }

        .qr-label {
            font-size: 7.5pt;
            color: #94a3b8;
            text-align: center;
            margin-top: 8pt;
            letter-spacing: 0.8pt;
            text-transform: uppercase;
        }

        .qr-codigo {
            font-size: 6pt;
            color: rgba(255,255,255,0.3);
            text-align: center;
            margin-top: 5pt;
            font-family: 'Courier New', monospace;
            letter-spacing: 0.3pt;
        }

        .emitido {
            font-size: 6.5pt;
            color: rgba(255,255,255,0.25);
            text-align: center;
            margin-top: 8pt;
        }

        /* ── RODAPÉ ── */
        .footer {
            position: absolute;
            bottom: 8pt;
            left: 14pt;
            right: 185pt;
            z-index: 10;
            border-top: 0.5pt solid rgba(255,255,255,0.1);
            padding-top: 5pt;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-brand {
            font-size: 7pt;
            color: rgba(255,255,255,0.3);
            letter-spacing: 0.5pt;
        }

        .footer-site {
            font-size: 7pt;
            color: rgba(255,255,255,0.25);
        }

        /* Linha azul brilhante central horizontal */
        .center-glow-line {
            position: absolute;
            left: 0; right: 185pt;
            top: 103pt;
            height: 0.5pt;
            background: linear-gradient(to right, transparent, rgba(56,189,248,0.4), transparent);
            z-index: 5;
        }

        /* Linha dourada vertical à esquerda da col-left */
        .left-accent {
            position: absolute;
            left: 208pt;
            top: 108pt;
            bottom: 20pt;
            width: 1pt;
            background: linear-gradient(to bottom, transparent, rgba(251,191,36,0.3), transparent);
            z-index: 5;
        }
    </style>
</head>
<body>
<div class="ticket">

    {{-- Glows e decorações --}}
    <div class="glow-left"></div>
    <div class="glow-right"></div>
    <div class="line-top"></div>
    <div class="line-bottom"></div>
    <div class="separator"></div>
    <div class="center-glow-line"></div>
    <div class="left-accent"></div>

    {{-- Imagem de fundo (capa do evento) --}}
    <div class="bg-image">
        @if($capaBase64)
            <img src="data:{{ $tipoMime }};base64,{{ $capaBase64 }}">
        @endif
    </div>

    {{-- HEADER: marca --}}
    <div class="header">
        <div class="header-brand">
            <span class="brand-line"></span>
            LUANDA TICKETS
            <span class="brand-line"></span>
        </div>
    </div>

    {{-- TÍTULO DO EVENTO --}}
    <div class="event-title-wrap">
        <div class="event-title">{{ $bilhete->evento->titulo ?? 'Evento' }}</div>
        <div class="event-subtitle">"{{ $bilhete->evento->descricao ? \Illuminate\Support\Str::limit(strip_tags($bilhete->evento->descricao), 60) : 'Luanda Tickets — Experiência Exclusiva' }}"</div>
    </div>

    {{-- COL ESQUERDA: local, data, hora, nome, contacto, ID --}}
    <div class="col-left">
        <div class="info-row">
            <div>
                <div class="info-label">&#128205; Local</div>
                <div class="info-value">{{ $bilhete->evento->localizacao ?? 'N/D' }}</div>
            </div>
        </div>
        <div class="info-row">
            <div>
                <div class="info-label">&#128197; Data</div>
                <div class="info-value">
                    @if($bilhete->evento->data_evento)
                        {{ \Carbon\Carbon::parse($bilhete->evento->data_evento)->translatedFormat('d \d\e F \d\e Y') }}
                    @else N/D @endif
                </div>
            </div>
        </div>
        <div class="info-row">
            <div>
                <div class="info-label">&#9200; Hora</div>
                <div class="info-value">
                    @if($bilhete->evento->hora_inicio)
                        {{ \Illuminate\Support\Str::substr($bilhete->evento->hora_inicio, 0, 5) }}
                        @if($bilhete->evento->hora_fim)
                            (Encerra: {{ \Illuminate\Support\Str::substr($bilhete->evento->hora_fim, 0, 5) }})
                        @endif
                    @else N/D @endif
                </div>
            </div>
        </div>
        <div class="info-row">
            <div>
                <div class="info-label">&#128100; Nome</div>
                <div class="info-value">{{ $bilhete->pedido->user->name ?? 'N/D' }}</div>
            </div>
        </div>
        <div class="info-row">
            <div>
                <div class="info-label">&#128241; Contacto</div>
                <div class="info-value">—</div>
            </div>
        </div>
        <div class="info-row">
            <div>
                <div class="info-label">&#128203; ID do Bilhete</div>
                <div class="info-value" style="font-size:7pt;font-family:'Courier New',monospace;letter-spacing:0.3pt;">
                    {{ strtoupper($bilhete->codigo_unico) }}
                </div>
            </div>
        </div>
    </div>

    {{-- COL CENTRO: tipo, zona, preço, organização, regras --}}
    <div class="col-center">
        <span class="badge-tipo">INGRESSO: {{ strtoupper($bilhete->tipoIngresso->nome ?? 'GERAL') }}</span>

        <div class="center-label">Zona</div>
        <div class="center-value">{{ $bilhete->tipoIngresso->nome ?? 'Plateia Geral' }}</div>

        <div class="center-label">Preco</div>
        <div class="center-value">{{ number_format($bilhete->tipoIngresso->preco ?? 0, 0, ',', '.') }} Kz</div>

        <div class="center-label">Organizacao</div>
        <div class="center-value" style="font-size:8.5pt;">Luanda Tickets</div>
        <div class="center-value" style="font-size:7pt;color:#94a3b8;font-weight:normal;">www.luandatickets.ao</div>

        <div class="rules-title">Regras Importantes:</div>
        <div class="rule-item">Bilhete individual e intransferivel</div>
        <div class="rule-item">Proibida a entrada com objetos perigosos</div>
        <div class="rule-item">Nao ha reembolso apos confirmacao</div>
        <div class="rule-item">Chegue com antecedencia</div>
        <div class="rule-item">Apresente documento de identificacao</div>
    </div>

    {{-- COL QR --}}
    <div class="col-qr">
        <div class="qr-wrap">
            <img src="data:image/svg+xml;base64,{{ $bilhete->qr_code }}">
        </div>
        <div class="qr-label">Apresente na Entrada</div>
        <div class="qr-codigo">{{ strtoupper(\Illuminate\Support\Str::substr($bilhete->codigo_unico, 0, 13)) }}...</div>
        <div class="emitido">Emitido em: {{ date('d/m/Y \a\s H:i') }}</div>
    </div>

    {{-- RODAPÉ --}}
    <div class="footer">
        <span class="footer-brand">&#169; {{ date('Y') }} Luanda Tickets — Todos os direitos reservados</span>
        <span class="footer-site">suporte@luandatickets.ao</span>
    </div>

</div>
</body>
</html>