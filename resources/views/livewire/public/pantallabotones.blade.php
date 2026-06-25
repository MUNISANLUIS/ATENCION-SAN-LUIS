{{-- resources/views/tickets/pantallabotones.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.5, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Municipalidad de San Luis · Emisión de Tickets</title>

    <style>
        /* ===== RESET & BASE ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #e8ecf1;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            margin: 0;
            touch-action: manipulation;
        }

        /* ===== CONTENEDOR PRINCIPAL ===== */
        .totem {
            width: 100%;
            max-width: 1100px;
            background: #ffffff;
            border-radius: 32px;
            box-shadow: 0 20px 60px -12px rgba(0, 20, 40, 0.30), 0 8px 24px -6px rgba(0, 0, 0, 0.08);
            padding: 28px 32px 20px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        /* ===== HEADER ===== */
        .header {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 20px;
            border-bottom: 1.5px solid #e6eaef;
            flex-wrap: wrap;
        }

        .header-logo {
            flex-shrink: 0;
            width: 72px;
            height: 72px;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.06));
            border-radius: 10px;
            background: #ffffff;
            padding: 4px;
        }

        .header-text {
            flex: 1;
        }

        .header-text h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a2a3a;
            letter-spacing: -0.2px;
            line-height: 1.2;
        }

        .header-text .sub {
            font-size: 14px;
            font-weight: 500;
            color: #5a6f85;
            background: #f0f3f7;
            padding: 4px 16px 4px 14px;
            border-radius: 30px;
            display: inline-block;
            margin-top: 2px;
            letter-spacing: 0.3px;
        }

        .header-status {
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
            padding: 22px 0 10px;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 8px;
        }

        .panel-header h2 {
            font-size: 17px;
            font-weight: 600;
            color: #1e3142;
            letter-spacing: 0.2px;
            display: flex;
            align-items: center;
            gap: 8px;
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
        }

        .btn {
            background: #f8fafc;
            border: 1.5px solid #e2e8ef;
            border-radius: 18px;
            padding: 18px 10px;
            font-size: 15px;
            font-weight: 600;
            color: #1a2f44;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-height: 76px;
            transition: all 0.12s ease;
            cursor: pointer;
            user-select: none;
            touch-action: manipulation;
            word-break: break-word;
            line-height: 1.3;
            background: #ffffff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
            position: relative;
            gap: 10px;
        }

        /* Botones destacados (con asterisco) */
        .btn[data-service*="*"] {
            background: #f0f6fe;
            border-color: #b8cee8;
            color: #0f3b6a;
            font-weight: 600;
        }

        .btn[data-service*="*"] .icon-star {
            flex-shrink: 0;
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
            min-height: 76px;
            border-radius: 18px;
            pointer-events: none;
            box-shadow: none;
        }

        .btn .icon {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            stroke: #3a5a7a;
            stroke-width: 2;
            fill: none;
        }

        .btn[data-service*="*"] .icon {
            stroke: #0f3b6a;
        }

        /* ===== NAVEGACIÓN ===== */
        .nav-section {
            display: flex;
            justify-content: center;
            gap: 28px;
            margin-top: 26px;
            margin-bottom: 4px;
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

        /* ===== TOOLBAR ===== */
        .toolbar {
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1.5px solid #e6ebf2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
            color: #3a5470;
            font-weight: 500;
            font-size: 13px;
        }

        .toolbar-left .badge {
            background: #eef2f8;
            padding: 5px 14px;
            border-radius: 40px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .toolbar-left .badge .indicator {
            width: 12px;
            height: 12px;
            background: #2a7a4b;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .toolbar-left .divider {
            width: 1px;
            height: 24px;
            background: #d5dee8;
        }

        .toolbar-right {
            display: flex;
            gap: 10px;
        }

        .toolbar-right .action-btn {
            background: transparent;
            border: 1.5px solid #d5dee8;
            border-radius: 30px;
            padding: 5px 18px;
            font-size: 13px;
            font-weight: 500;
            color: #1a3450;
            transition: 0.1s ease;
            cursor: pointer;
            touch-action: manipulation;
            background: #f7faff;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .toolbar-right .action-btn:active {
            background: #dce6f2;
            transform: scale(0.94);
        }

        .toolbar-right .action-btn svg {
            width: 16px;
            height: 16px;
            stroke: #1a3450;
            stroke-width: 2;
            fill: none;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 780px) {
            .totem {
                padding: 18px 16px 16px;
                border-radius: 24px;
            }
            .header-text h1 {
                font-size: 24px;
            }
            .grid {
                gap: 12px;
            }
            .btn {
                font-size: 13px;
                min-height: 66px;
                padding: 14px 6px;
                border-radius: 14px;
            }
            .btn .icon {
                width: 18px;
                height: 18px;
            }
            .nav-btn {
                padding: 8px 20px;
                font-size: 14px;
            }
        }

        @media (max-width: 560px) {
            .totem {
                padding: 12px 10px 12px;
                border-radius: 20px;
            }
            .header {
                gap: 12px;
            }
            .header-logo {
                width: 56px;
                height: 56px;
            }
            .header-text h1 {
                font-size: 20px;
            }
            .header-text .sub {
                font-size: 12px;
                padding: 2px 12px;
            }
            .header-status {
                font-size: 11px;
                padding: 3px 10px;
            }
            .grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            .btn {
                font-size: 12px;
                min-height: 58px;
                border-radius: 12px;
                padding: 10px 4px;
            }
            .btn .icon {
                width: 16px;
                height: 16px;
            }
            .nav-section {
                gap: 14px;
            }
            .nav-btn {
                padding: 6px 14px;
                font-size: 13px;
                gap: 6px;
            }
            .nav-btn svg {
                width: 16px;
                height: 16px;
            }
            .toolbar-left {
                font-size: 12px;
                gap: 10px;
                flex-wrap: wrap;
            }
            .toolbar-right .action-btn {
                padding: 4px 12px;
                font-size: 12px;
            }
            .panel-header h2 {
                font-size: 14px;
            }
            .panel-header .page-badge {
                font-size: 12px;
                padding: 2px 12px;
            }
        }

        @media (max-width: 400px) {
            .grid {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }
            .btn {
                font-size: 11px;
                min-height: 50px;
                padding: 8px 4px;
                border-radius: 10px;
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

        <!-- HEADER -->
        <header class="header">
            <img 
                src="{{ asset('assets/logo.png') }}" 
                alt="Municipalidad de San Luis" 
                class="header-logo"
                loading="lazy"
            >
            <div class="header-text">
                <h1>Municipalidad de San Luis</h1>
                <span class="sub">Sistema de Emisión de Tickets</span>
            </div>
            <div class="header-status">
                <span class="dot"></span>
                <span>En línea · 3 módulos</span>
            </div>
        </header>

        <!-- PANEL -->
        <div class="panel">
            <div class="panel-header">
                <h2>
                    <svg width="20" height="20" viewBox="0 0 24 24" stroke="#3a5a7a" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                    Servicios disponibles
                </h2>
                <span class="page-badge">Página 1 de 2</span>
            </div>

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
                            $isStarred = strpos($label, '*') !== false;
                            $cleanLabel = trim(str_replace('*', '', $label));
                        @endphp
                        <div class="btn" data-service="{{ $label }}" role="button" tabindex="0">
                            @if ($isStarred)
                                <svg class="icon icon-star" width="18" height="18" viewBox="0 0 24 24" stroke="#0f3b6a" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                            @else
                                <svg class="icon" width="18" height="18" viewBox="0 0 24 24" stroke="#3a5a7a" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                                </svg>
                            @endif
                            {{ $cleanLabel }}
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- NAVEGACIÓN -->
            <div class="nav-section">
                <div class="nav-btn" role="button" tabindex="0" aria-label="Anterior">
                    <svg viewBox="0 0 24 24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 6 9 12 15 18"></polyline>
                    </svg>
                    <span>Anterior</span>
                </div>
                <div class="nav-btn" role="button" tabindex="0" aria-label="Siguiente">
                    <span>Siguiente</span>
                    <svg viewBox="0 0 24 24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 6 15 12 9 18"></polyline>
                    </svg>
                </div>
            </div>

            <!-- TOOLBAR -->
            <div class="toolbar">
                <div class="toolbar-left">
                    <span class="badge">
                        <span class="indicator"></span>
                        Sistema operativo · v2.4
                    </span>
                    <span class="divider"></span>
                    <span>Seguro</span>
                </div>
                <div class="toolbar-right">
                    <button class="action-btn" type="button">
                        <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="23 4 23 10 17 10"></polyline>
                            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                        </svg>
                        Actualizar
                    </button>
                    <button class="action-btn" type="button">
                        <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            'use strict';

            const serviceBtns = document.querySelectorAll('.btn:not(.empty)');
            const navBtns = document.querySelectorAll('.nav-btn');
            const closeBtn = document.querySelector('.action-btn:last-child');
            const updateBtn = document.querySelector('.action-btn:first-child');

            // Servicios
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

            // Navegación
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

            // Cerrar
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    if (confirm('¿Cerrar la ventana de tickets?')) {
                        const totem = document.querySelector('.totem');
                        totem.style.transition = 'opacity 0.25s, transform 0.2s';
                        totem.style.opacity = '0.4';
                        totem.style.transform = 'scale(0.97)';
                        setTimeout(() => {
                            totem.style.opacity = '1';
                            totem.style.transform = 'scale(1)';
                            alert('Ventana reiniciada (simulación)');
                        }, 280);
                    }
                });
            }

            // Actualizar
            if (updateBtn) {
                updateBtn.addEventListener('click', function() {
                    alert('Actualizando lista de servicios... (simulación)');
                });
            }

            // Ripple
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