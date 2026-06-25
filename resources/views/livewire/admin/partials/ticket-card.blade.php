@php
$accentColors = [
    'red'     => '#ef4444',
    'blue'    => '#3b82f6',
    'violet'  => '#a78bfa',
    'cyan'    => '#22d3ee',
    'emerald' => '#34d399',
    'orange'  => '#fb923c',
    'pink'    => '#f472b6',
    'teal'    => '#2dd4bf',
    'indigo'  => '#818cf8',
];
$accent = $accentColors[$colorAccent ?? 'blue'] ?? '#3b82f6';

$fechaCreacion    = \Carbon\Carbon::parse($ticket->fecha_creacion);
$diffTotalMinutes = (int) $fechaCreacion->diffInMinutes(now());
$diffDays         = (int) floor($diffTotalMinutes / 1440);
$diffHours        = (int) floor($diffTotalMinutes / 60);

if ($diffDays >= 1) {
    $tiempoLabel = $diffDays . 'd ' . (int)floor(($diffTotalMinutes % 1440)/60) . 'h';
    $tiempoColor = $diffDays >= 3 ? '#ff4444' : '#ff8800';
    $tiempoIcon  = $diffDays >= 3 ? '🔴' : '🟠';
} elseif ($diffHours >= 1) {
    $tiempoLabel = $diffHours . 'h ' . ($diffTotalMinutes % 60) . 'm';
    $tiempoColor = $diffHours >= 8 ? '#ff8800' : '#facc15';
    $tiempoIcon  = $diffHours >= 8 ? '🟠' : '🟡';
} else {
    $tiempoLabel = $diffTotalMinutes . 'm';
    $tiempoColor = '#4ade80';
    $tiempoIcon  = '🟢';
}

$estadoLabels = [2=>'● ACTIVO', 3=>'● SIN LEER', 4=>'● EN PROCESO', 6=>'● LEÍDO'];
$estadoColors = [2=>'#4ade80',  3=>'#ff4444',    4=>'#facc15',       6=>'#60a5fa'];
$estadoLabel  = $estadoLabels[$ticket->estado] ?? strtoupper(optional($ticket->tipoEstado)->nombre ?? '');
$estadoColor  = $estadoColors[$ticket->estado] ?? '#888';

$incidencia = optional($ticket->subCategoriaIncidencia)->nombre
           ?? optional($ticket->categoriaIncidencia)->nombre
           ?? '—';
$area = optional($ticket->area)->nombre ?? 'N/A';
@endphp

<div wire:click="showDetails({{ $ticket->id }})"
     style="background:#0d1b2e; border:1px solid #1e3a5f; border-left:5px solid {{ $accent }}; cursor:pointer; transition:all 0.15s; border-radius:2px;"
     onmouseover="this.style.background='#112240'; this.style.borderColor='{{ $accent }}';"
     onmouseout="this.style.background='#0d1b2e'; this.style.borderColor='#1e3a5f'; this.style.borderLeftColor='{{ $accent }}';">

    {{-- TOP: Correlativo + Tiempo --}}
    <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 18px 12px; border-bottom:1px solid #1e3a5f;">
        <span style="color:{{ $accent }}; font-size:22px; font-weight:900; letter-spacing:2px; font-family:monospace;">
            #{{ $ticket->correlativo }}
        </span>
        <span style="color:{{ $tiempoColor }}; font-size:15px; font-weight:800; font-family:monospace;">
            {{ $tiempoIcon }} {{ $tiempoLabel }}
        </span>
    </div>

    {{-- Solicitante --}}
    <div style="padding:14px 18px 0;">
        <div style="color:#4a7ab5; font-size:10px; letter-spacing:3px; text-transform:uppercase; margin-bottom:5px; font-weight:700;">SOLICITANTE</div>
        <div style="color:#ffffff; font-size:17px; font-weight:800; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; letter-spacing:0.5px;">
            {{ $ticket->nombres }}
        </div>
    </div>

    {{-- Área --}}
    <div style="padding:12px 18px 0;">
        <div style="color:#4a7ab5; font-size:10px; letter-spacing:3px; text-transform:uppercase; margin-bottom:5px; font-weight:700;">ÁREA</div>
        <div style="color:#93c5fd; font-size:15px; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
            {{ $area }}
        </div>
    </div>

    {{-- Incidencia --}}
    <div style="padding:12px 18px 14px;">
        <div style="color:#4a7ab5; font-size:10px; letter-spacing:3px; text-transform:uppercase; margin-bottom:5px; font-weight:700;">INCIDENCIA</div>
        <div style="color:#7dd3fc; font-size:13px; font-weight:600; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
            {{ $incidencia }}
        </div>
    </div>

    {{-- Footer --}}
    <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 18px; border-top:1px solid #1e3a5f; background:#091527;">
        <span style="color:{{ $estadoColor }}; font-size:12px; font-weight:900; letter-spacing:2px;">
            {{ $estadoLabel }}
        </span>
        {{-- <div style="display:flex; gap:4px;">
            <button wire:click.stop="edit({{ $ticket->id }})"
                style="background:#1e3a5f; border:none; color:#60a5fa; font-size:15px; cursor:pointer; padding:6px 10px; border-radius:2px;"
                onmouseover="this.style.background='#2563eb'; this.style.color='#fff';"
                onmouseout="this.style.background='#1e3a5f'; this.style.color='#60a5fa';"
                title="Editar">✎</button>
            <button wire:click.stop="verHistorial({{ $ticket->id }})"
                style="background:#1e3a5f; border:none; color:#a78bfa; font-size:15px; cursor:pointer; padding:6px 10px; border-radius:2px;"
                onmouseover="this.style.background='#7c3aed'; this.style.color='#fff';"
                onmouseout="this.style.background='#1e3a5f'; this.style.color='#a78bfa';"
                title="Historial">⏱</button>
        </div> --}}
    </div>

</div>