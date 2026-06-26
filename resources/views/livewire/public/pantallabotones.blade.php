<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Municipalidad de San Luis · Emisión de Tickets</title>

    <style>
        /* ===== RESET & BASE ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            overflow: hidden;
        }

        body {
            background: #e8ecf1;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            touch-action: manipulation;
            padding: 0;
            margin: 0;
        }

        /* ===== CONTENEDOR PRINCIPAL ===== */
        .totem {
            width: 100%;
            height: 100vh;
            max-height: 100vh;
            background: #ffffff;
            border-radius: 0;
            box-shadow: none;
            padding: 24px 28px 20px;
            display: flex;
            flex-direction: column;
            border: none;
            overflow: hidden;
        }

        /* ===== HEADER - SOLO LOGO ===== */
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding-bottom: 16px;
            border-bottom: 1.5px solid #e6eaef;
            flex-shrink: 0;
            position: relative;
            background: transparent;
        }

        .header-logo {
            height: 80px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            background: transparent;
            padding: 0;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.06));
        }

        .header-status {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            background: #e6f2ed;
            padding: 6px 16px 6px 14px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            color: #1e6b4a;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(30, 107, 74, 0.12);
            white-space: nowrap;
            flex-shrink: 0;
        }

        .header-status .dot {
            width: 10px;
            height: 10px;
            background: #1e9b6a;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 2px rgba(30, 155, 106, 0.25);
            animation: pulse-dot 2s infinite;
        }

        @keyframes pulse-dot {
            0% { box-shadow: 0 0 0 0 rgba(30, 155, 106, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(30, 155, 106, 0); }
            100% { box-shadow: 0 0 0 0 rgba(30, 155, 106, 0); }
        }

        /* ===== PANEL ===== */
        .panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 18px 0 8px;
            min-height: 0;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            flex-shrink: 0;
        }

        .panel-header h2 {
            font-size: 17px;
            font-weight: 600;
            color: #1e3142;
            letter-spacing: 0.2px;
        }

        .panel-header .page-badge {
            background: #eef1f6;
            padding: 4px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 500;
            color: #3a5470;
        }

        /* ===== GRID ===== */
        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            flex: 1;
            align-content: stretch;
        }

        .btn {
            background: #f8fafc;
            border: 1.5px solid #e2e8ef;
            border-radius: 16px;
            padding: 18px 10px;
            font-size: 15px;
            font-weight: 600;
            color: #1a2f44;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 0;
            height: 100%;
            transition: all 0.12s ease;
            cursor: pointer;
            user-select: none;
            touch-action: manipulation;
            word-break: break-word;
            line-height: 1.3;
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
            position: relative;
            flex: 1;
        }

        .btn[data-service*="*"] {
            background: #f0f6fe;
            border-color: #b8cee8;
            color: #0f3b6a;
            font-weight: 600;
        }

        .btn:active:not(.empty) {
            transform: scale(0.96);
            background: #e9eef5;
            border-color: #a0b8d4;
        }

        .btn:focus-visible {
            outline: 3px solid #3a7bb5;
            outline-offset: 2px;
        }

        .btn.empty {
            background: #f2f5f9;
            border: 2px dashed #d0d8e2;
            cursor: default;
            opacity: 0.3;
            border-radius: 16px;
            pointer-events: none;
            box-shadow: none;
            height: 100%;
            min-height: 0;
        }

        /* ===== NAVEGACIÓN ===== */
        .nav-section {
            display: flex;
            justify-content: center;
            gap: 28px;
            margin-top: 16px;
            margin-bottom: 2px;
            flex-shrink: 0;
        }

        .nav-btn {
            background: #ffffff;
            border: 1.5px solid #d5dee8;
            border-radius: 60px;
            padding: 10px 28px;
            font-weight: 600;
            font-size: 15px;
            color: #1a3450;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.1s ease;
            cursor: pointer;
            touch-action: manipulation;
            background: #fafcff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        }

        .nav-btn:active {
            transform: scale(0.94);
            background: #e9eff6;
            border-color: #a0b8d4;
        }

        .nav-btn svg {
            width: 20px;
            height: 20px;
            stroke: #1f3f62;
            stroke-width: 2.4;
            fill: none;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .totem {
                padding: 18px 20px 16px;
            }
            .header-logo {
                height: 65px;
            }
            .btn {
                font-size: 14px;
                padding: 14px 8px;
            }
        }

        @media (max-width: 780px) {
            .totem {
                padding: 14px 14px 12px;
            }
            .header {
                padding-bottom: 12px;
            }
            .header-logo {
                height: 55px;
            }
            .header-status {
                font-size: 11px;
                padding: 4px 12px;
                gap: 6px;
                position: relative;
                transform: none;
                top: auto;
                right: auto;
                margin-left: auto;
            }
            .grid {
                gap: 10px;
            }
            .btn {
                font-size: 13px;
                padding: 12px 6px;
                border-radius: 14px;
            }
            .nav-section {
                gap: 16px;
                margin-top: 12px;
            }
            .nav-btn {
                padding: 8px 18px;
                font-size: 13px;
                gap: 6px;
            }
            .nav-btn svg {
                width: 16px;
                height: 16px;
            }
            .panel {
                padding: 12px 0 4px;
            }
            .panel-header h2 {
                font-size: 15px;
            }
            .panel-header .page-badge {
                font-size: 12px;
                padding: 2px 12px;
            }
        }

        @media (max-width: 560px) {
            .totem {
                padding: 8px 8px 8px;
            }
            .header {
                gap: 10px;
                padding-bottom: 10px;
                flex-wrap: wrap;
                justify-content: center;
            }
            .header-logo {
                height: 50px;
            }
            .header-status {
                font-size: 10px;
                padding: 2px 8px;
                gap: 4px;
                position: relative;
                transform: none;
                top: auto;
                right: auto;
                margin-left: auto;
            }
            .header-status .dot {
                width: 8px;
                height: 8px;
            }
            .grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            .btn {
                font-size: 11px;
                padding: 8px 4px;
                border-radius: 10px;
            }
            .nav-section {
                gap: 12px;
                margin-top: 10px;
            }
            .nav-btn {
                padding: 6px 14px;
                font-size: 12px;
                gap: 4px;
            }
            .nav-btn svg {
                width: 14px;
                height: 14px;
            }
            .panel-header h2 {
                font-size: 13px;
            }
            .panel-header .page-badge {
                font-size: 10px;
                padding: 2px 8px;
            }
            .panel {
                padding: 8px 0 2px;
            }
        }

        @media (max-width: 380px) {
            .grid {
                grid-template-columns: 1fr 1fr;
                gap: 6px;
            }
            .btn {
                font-size: 10px;
                padding: 6px 3px;
                border-radius: 8px;
            }
        }

        /* ===== RIPPLE ===== */
        .ripple-effect {
            position: absolute;
            border-radius: 50%;
            background: rgba(40, 100, 180, 0.12);
            transform: scale(0);
            animation: ripple 0.5s linear;
            pointer-events: none;
        }

        @keyframes ripple {
            to { transform: scale(4); opacity: 0; }
        }
    </style>
</head>
<body>
    <div class="totem" role="main">

        <!-- HEADER - SOLO LOGO -->
        <header class="header">
            <img 
                src="{{ asset('assets/logo.png') }}" 
                alt="Municipalidad de San Luis" 
                class="header-logo"
                loading="lazy"
            >

        </header>

        <!-- PANEL -->
        <div class="panel">
            <!-- GRID -->
            <div class="grid" role="group" aria-label="Servicios">
                @php
                    $buttons = [
                        'PREFERENCIAL CAJA',
                        '* FISCALIZACION TRIBUTARIA',
                        'PREFERENCIAL MESA DE PARTES',
                        '* CAJA',
                        '* MULTAS Y FISCALIZACION',
                        '* MESA DE PARTES',
                        '* DECLARACIONES JURADAS',
                        '* DEFENSA CIVIL',
                        '',
                        '* ESTADO DE CUENTA',
                        '* LICENCIA FUNCIONAMIENTO',
                        '',
                        '* ORIENTACION PREDIAL Y ARB',
                        '* DESARROLLO URBANO',
                        '',
                    ];
                @endphp

                @foreach ($buttons as $label)
                    @if (empty($label))
                        <div class="btn empty" aria-hidden="true"></div>
                    @else
                        @php
                            $cleanLabel = trim(str_replace('*', '', $label));
                        @endphp
                        <div class="btn" data-service="{{ $label }}" role="button" tabindex="0">
                            {{ $cleanLabel }}
                        </div>
                    @endif
                @endforeach
            </div>
            
        </div>
    </div>

    <script>
        (function() {
            'use strict';

            const serviceBtns = document.querySelectorAll('.btn:not(.empty)');
            const navBtns = document.querySelectorAll('.nav-btn');

            serviceBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const service = this.getAttribute('data-service') || this.textContent.trim();
                    createRipple(e, this);
                    alert('Servicio seleccionado:\n\n' + service + '\n\n(Simulación de emisión de ticket)');
                });

                btn.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });

            navBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const isPrev = this.querySelector('polyline')?.getAttribute('points')
                        ?.includes('15 6 9 12 15 18') ?? false;
                    const dir = isPrev ? 'anterior' : 'siguiente';
                    alert('Navegación: página ' + dir + '\n\n(Simulación de cambio de página)');
                });

                btn.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });

            function createRipple(e, element) {
                const rect = element.getBoundingClientRect();
                const ripple = document.createElement('span');
                ripple.className = 'ripple-effect';
                const size = Math.max(rect.width, rect.height);
                ripple.style.width = ripple.style.height = size + 'px';
                const clientX = e.clientX || (e.touches && e.touches[0].clientX) || rect.left + rect.width/2;
                const clientY = e.clientY || (e.touches && e.touches[0].clientY) || rect.top + rect.height/2;
                ripple.style.left = (clientX - rect.left - size/2) + 'px';
                ripple.style.top = (clientY - rect.top - size/2) + 'px';
                element.style.position = 'relative';
                element.style.overflow = 'hidden';
                element.appendChild(ripple);
                setTimeout(() => {
                    ripple.remove();
                }, 500);
            }
        })();
    </script>
</body>
</html>