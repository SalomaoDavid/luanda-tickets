<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 0; size: 612pt 253pt landscape; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Helvetica', Arial, sans-serif; background: #0d1c34; color: #ffffff; width: 612pt; height: 253pt; overflow: hidden; }

        .ticket {
            width: 612pt; height: 253pt; position: relative; background: #0d1c34; overflow: hidden;
            border-radius: 14pt;
            box-shadow:
                0 10pt 34pt rgba(2,6,20,0.4),
                0 0 0 1.2pt rgba(56,189,248,0.4),
                0 0 0 2.6pt rgba(251,191,36,0.26);
        }
        .notch-top,
        .notch-bottom{
            position:absolute; left:463.2pt; width:12pt; height:12pt; border-radius:50%;
            background:radial-gradient(circle at 35% 30%, rgba(255,255,255,0.10), rgba(255,255,255,0.02) 70%);
            border:1pt solid rgba(255,255,255,0.16);
            z-index:6;
        }
        .notch-top{ top:2pt; }
        .notch-bottom{ bottom:2pt; }
        .glow-left { position: absolute; left: -49pt; top: -32.6pt; width: 261.1pt; height: 261.1pt; border-radius: 50%; background: radial-gradient(circle, rgba(45,90,200,0.62) 0%, transparent 70%); }
        .glow-right { position: absolute; right: 65.3pt; top: -49pt; width: 204pt; height: 204pt; border-radius: 50%; background: radial-gradient(circle, rgba(35,110,210,0.42) 0%, transparent 70%); }
        .line-top { position: absolute; top: 0; left: 0; right: 0; height: 1.6pt; background: linear-gradient(to right, transparent, #1e6abf, #38bdf8, #1e6abf, transparent); }
        .line-bottom { position: absolute; bottom: 0; left: 0; right: 0; height: 1.6pt; background: linear-gradient(to right, transparent, #b8860b, #fbbf24, #b8860b, transparent); }
        .separator {
            position: absolute; right: 142.8pt; top: 8.2pt; bottom: 8.2pt; width: 1.4pt;
            background-image: radial-gradient(circle, rgba(255,255,255,0.28) 1.1pt, transparent 1.3pt);
            background-size: 100% 7.5pt; background-repeat: repeat-y; background-position: center top;
        }
        .center-glow-line { position: absolute; left: 0; right: 151pt; top: 84pt; height: 0.4pt; background: linear-gradient(to right, transparent, rgba(56,189,248,0.4), transparent); z-index: 5; }
        .left-accent { position: absolute; left: 169.7pt; top: 88.1pt; bottom: 16.3pt; width: 0.8pt; background: linear-gradient(to bottom, transparent, rgba(251,191,36,0.3), transparent); z-index: 5; }

        .bg-image { position: absolute; top: 0; left: 0; right: 146.9pt; bottom: 0; z-index: 1; overflow: hidden; }
        .bg-image img { width: 100%; height: 100%; object-fit: cover; opacity: 0.34; }
        .bg-image-veil { position: absolute; inset: 0; z-index: 2;
            background:
                linear-gradient(180deg, rgba(13,28,52,0.55) 0%, rgba(13,28,52,0.25) 35%, rgba(13,28,52,0.75) 100%),
                linear-gradient(90deg, rgba(13,28,52,0.5) 0%, rgba(13,28,52,0.1) 30%, rgba(13,28,52,0.1) 70%, rgba(13,28,52,0.55) 100%);
        }

        .header { position: absolute; top: 11.4pt; left: 0; right: 146.9pt; text-align: center; z-index: 10; }
        .header-brand { font-size: 8.2pt; font-weight: bold; letter-spacing: 3.3pt; color: #fbbf24; text-transform: uppercase; border-bottom: 0.4pt solid rgba(251,191,36,0.35); display: inline-block; padding-bottom: 3.3pt; padding-left: 9.8pt; padding-right: 9.8pt; }
        .brand-line { display: inline-block; width: 18pt; height: 0.8pt; background: #fbbf24; vertical-align: middle; margin: 0 4.1pt; }

        .event-title-wrap { position: absolute; top: 29pt; left: 8.2pt; right: 151pt; text-align: center; z-index: 10; }
        .event-title { font-size: 23.5pt; font-weight: 800; color: #fff6da; text-transform: uppercase; letter-spacing: 0.8pt; line-height: 1.08;
            text-shadow: 0 0 20pt rgba(251,191,36,0.65), 0 0 4pt rgba(251,191,36,0.5); }
        .event-title-underline { width: 62pt; height: 2.2pt; margin: 5pt auto 0; border-radius: 2pt;
            background: linear-gradient(to right, transparent, #fbbf24, #38bdf8, transparent); }
        .event-subtitle { font-size: 6.8pt; color: #b9c6da; font-style: italic; margin-top: 5pt; letter-spacing: 0.4pt; }


        .col-left { position: absolute; left: 11.4pt; top: 86.5pt; width: 163.2pt; z-index: 10; }
        .info-row { margin-bottom: 5.7pt; }
        .info-label { font-size: 6.3pt; color: #fbbf24; font-weight: bold; text-transform: uppercase; letter-spacing: 0.2pt; }
        .info-value { font-size: 7.8pt; color: #ffffff; font-weight: bold; margin-top: 0.8pt; }

        .col-center { position: absolute; left: 183.6pt; top: 86.5pt; width: 159.1pt; z-index: 10; }
        .badge-tipo { background: transparent; border: 0.8pt solid #fbbf24; color: #fbbf24; font-size: 6.9pt; font-weight: bold; letter-spacing: 0.8pt; text-transform: uppercase; padding: 2.2pt 8.2pt; border-radius: 16.3pt; display: inline-block; margin-bottom: 5pt; }
        .center-label { font-size: 6.3pt; color: #fbbf24; font-weight: bold; text-transform: uppercase; letter-spacing: 0.4pt; margin-bottom: 0.6pt; margin-top: 3.8pt; }
        .center-value { font-size: 7.8pt; color: #ffffff; font-weight: bold; }
        .rules-title { font-size: 6.3pt; color: #fbbf24; font-weight: bold; text-transform: uppercase; letter-spacing: 0.4pt; margin-top: 5.2pt; margin-bottom: 2pt; }
        .rule-item { font-size: 5.7pt; color: #94a3b8; margin-bottom: 1pt; padding-left: 6.5pt; position: relative; line-height: 1.15; }
        .rule-item::before { content: '\2022'; position: absolute; left: 0; color: #fbbf24; font-size: 6pt; top: 0.8pt; }

        .org-row { display: flex; align-items: center; gap: 3.7pt; margin-top: 0.4pt; }
        .org-avatar { width: 13.5pt; height: 13.5pt; border-radius: 50%; object-fit: cover; border: 0.6pt solid rgba(251,191,36,0.55); flex-shrink: 0; }
        .org-avatar-fallback { width: 13.5pt; height: 13.5pt; border-radius: 50%; border: 0.6pt solid rgba(251,191,36,0.55); background: rgba(251,191,36,0.14); color: #fbbf24; font-size: 6.5pt; font-weight: bold; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .org-name { font-size: 6.9pt; color: #ffffff; font-weight: bold; }

        .cat-tag { font-size: 5.7pt; color: #38bdf8; font-weight: bold; margin-top: 1.6pt; letter-spacing: 0.1pt; }

        .col-qr { position: absolute; right: 0; top: 0; width: 142.8pt; height: 253pt; z-index: 10; display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(255,255,255,0.055); }
        .qr-wrap { background: #ffffff; padding: 6.5pt; border-radius: 6.5pt; }
        .qr-wrap img { display: block; width: 85.7pt; height: 85.7pt; }
        .qr-label { font-size: 6.1pt; color: #94a3b8; text-align: center; margin-top: 6.5pt; letter-spacing: 0.7pt; text-transform: uppercase; }
        .qr-codigo { font-size: 5.5pt; color: rgba(255,255,255,0.3); text-align: center; margin-top: 4.1pt; font-family: 'Courier New', monospace; letter-spacing: 0.2pt; }
        .emitido { font-size: 5.7pt; color: rgba(255,255,255,0.25); text-align: center; margin-top: 6.5pt; }

        .footer { position: absolute; bottom: 5pt; left: 11.4pt; right: 151pt; z-index: 10; border-top: 0.4pt solid rgba(255,255,255,0.1); padding-top: 3pt; display: flex; justify-content: space-between; align-items: center; }
        .footer-brand { font-size: 6pt; color: rgba(255,255,255,0.3); letter-spacing: 0.4pt; }
        .footer-site { font-size: 6pt; color: rgba(255,255,255,0.25); }
    </style>
</head>
<body>
<div class="ticket">

    <div class="glow-left"></div>
    <div class="glow-right"></div>
    <div class="line-top"></div>
    <div class="line-bottom"></div>
    <div class="separator"></div>
    <div class="center-glow-line"></div>
    <div class="left-accent"></div>
    <div class="notch-top"></div>
    <div class="notch-bottom"></div>

    <div class="bg-image">
        @if($capaBase64)
            <img src="data:{{ $tipoMime }};base64,{{ $capaBase64 }}">
        @endif
        <div class="bg-image-veil"></div>
    </div>

    <div class="header">
        <div class="header-brand">
            <span class="brand-line"></span>
            LUANDA TICKETS
            <span class="brand-line"></span>
        </div>
    </div>

    <div class="event-title-wrap">
        <div class="event-title">{{ $bilhete->evento->titulo ?? 'Evento' }}</div>
        <div class="event-title-underline"></div>
        <div class="event-subtitle">"{{ $bilhete->evento->descricao ? \Illuminate\Support\Str::limit(strip_tags($bilhete->evento->descricao), 60) : 'Luanda Tickets — Experiencia Exclusiva' }}"</div>
    </div>

    {{-- COL ESQUERDA --}}
    @php
        $catNomeBilhete = strtolower(optional($bilhete->evento->categoria ?? null)->nome ?? '');
        $metaBilhete    = $bilhete->evento->meta ?? [];
        $temRotaViagem  = str_contains($catNomeBilhete, 'viag') && !empty($metaBilhete['partida_provincia']) && !empty($metaBilhete['destino_provincia']);
    @endphp
    <div class="col-left">
        <div class="info-row">
            <div class="info-label">{{ $temRotaViagem ? 'Rota' : 'Local' }}</div>
            <div class="info-value">
                @if($temRotaViagem)
                    {{ $metaBilhete['partida_provincia'] }} → {{ $metaBilhete['destino_provincia'] }}
                @else
                    {{ $bilhete->evento->localizacao ?? 'N/D' }}
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Data</div>
            <div class="info-value">
                @if($bilhete->evento->data_evento)
                    {{ \Carbon\Carbon::parse($bilhete->evento->data_evento)->translatedFormat('d \d\e F \d\e Y') }}
                @else N/D @endif
            </div>
            @if(!empty($bilhete->numero_dia) && !empty($bilhete->tipoIngresso->dias_validos))
                <div class="cat-tag">Dia {{ $bilhete->numero_dia }} de {{ $bilhete->tipoIngresso->dias_validos }} — Passe Completo</div>
            @endif
        </div>
        <div class="info-row">
            <div class="info-label">Hora</div>
            <div class="info-value">
                @if(!empty($bilhete->evento->hora_inicio))
                    {{ \Illuminate\Support\Str::substr($bilhete->evento->hora_inicio, 0, 5) }}
                    @if(!empty($bilhete->evento->hora_fim))
                        — {{ \Illuminate\Support\Str::substr($bilhete->evento->hora_fim, 0, 5) }}
                    @endif
                @else
                    N/D
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Titular</div>
            <div class="info-value">{{ $bilhete->pedido->user->name ?? 'N/D' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">ID do Bilhete</div>
            <div class="info-value" style="font-size:5.7pt;font-family:'Courier New',monospace;letter-spacing:0.2pt;">
                {{ strtoupper(substr($bilhete->codigo_unico, 0, 24)) }}
            </div>
        </div>
    </div>

    {{-- COL CENTRO --}}
    <div class="col-center">
        <span class="badge-tipo">INGRESSO: {{ strtoupper($bilhete->tipoIngresso->nome ?? 'GERAL') }}</span>

        <div class="center-label">Zona</div>
        <div class="center-value">{{ $bilhete->tipoIngresso->nome ?? 'Plateia Geral' }}</div>

        <div class="center-label">Preco</div>
        <div class="center-value">{{ number_format($bilhete->tipoIngresso->preco ?? 0, 0, ',', '.') }} Kz</div>

        {{-- ✅ Organização: nome + ícone/foto do criador do evento --}}
        <div class="center-label">Organizacao</div>
        <div class="org-row">
            @if($criadorAvatarBase64)
                <img class="org-avatar" src="data:{{ $criadorAvatarMime }};base64,{{ $criadorAvatarBase64 }}">
            @else
                <span class="org-avatar-fallback">{{ strtoupper(\Illuminate\Support\Str::substr($bilhete->evento->user->name ?? 'L', 0, 1)) }}</span>
            @endif
            <span class="org-name">{{ $bilhete->evento->user->name ?? 'Luanda Tickets' }}</span>
        </div>
        <div class="center-value" style="font-size:5.7pt;color:#94a3b8;font-weight:normal;margin-top:1.4pt;">www.luandatickets.ao</div>

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
        <div class="emitido">Emitido em: {{ date('d/m/Y H:i') }}</div>
    </div>

    <div class="footer">
        <span class="footer-brand">© {{ date('Y') }} Luanda Tickets — Todos os direitos reservados</span>
        <span class="footer-site">suporte@luandatickets.ao</span>
    </div>

</div>
</body>
</html>