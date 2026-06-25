<div style="height:100vh; display:flex; flex-direction:column; background:#000; font-family:monospace; overflow:hidden;">

    {{-- POLLING --}}
    <div wire:poll.10s wire:key="poll-trigger" style="display:none;"></div>

    {{-- NOTIFICACIÓN --}}
    @if ($newTicketNotification)
    <div style="position:fixed; top:20px; right:20px; z-index:999; background:#111; border:2px solid #f59e0b; padding:20px; min-width:280px;"
         x-data x-init="setTimeout(() => $wire.hideNotification(), 8000)">
        <div style="color:#f59e0b; font-size:11px; font-weight:900; letter-spacing:3px; margin-bottom:8px;">⚠ NUEVO TICKET</div>
        <div style="color:#fff; font-size:22px; font-weight:900;">#{{ $notificationData['correlativo'] ?? 'N/A' }}</div>
        <div style="color:#999; font-size:13px; margin-top:4px;">{{ $notificationData['area'] ?? '' }}</div>
        <button wire:click="hideNotification" style="position:absolute; top:10px; right:10px; color:#555; background:none; border:none; font-size:18px; cursor:pointer;">✕</button>
        <div style="height:3px; background:#333; margin-top:12px;">
            <div style="height:100%; background:#f59e0b; animation:shrink 8s linear forwards; width:100%;"></div>
        </div>
    </div>
    @endif

    {{-- FILTROS --}}
    <div style="flex-shrink:0; background:#111; border-bottom:1px solid #222; padding:12px 20px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar..."
            style="background:#000; border:1px solid #333; color:#fff; font-size:13px; padding:8px 12px; font-family:monospace; outline:none; width:180px;"/>
        <select wire:model.live="filterArea" style="background:#000; border:1px solid #333; color:#ffffff; font-size:12px; padding:8px; font-family:monospace; outline:none;">
            <option value="">TODAS LAS ÁREAS</option>
            @foreach ($areas as $area)
            <option value="{{ $area->id }}">{{ strtoupper($area->nombre) }}</option>
            @endforeach
        </select>
        <select wire:model.live="filterStatus" style="background:#000; border:1px solid #333; color:#ffffff; font-size:12px; padding:8px; font-family:monospace; outline:none;">
            <option value="">TODOS LOS ESTADOS</option>
            @foreach ($estados as $est)
            <option value="{{ $est->id }}">{{ strtoupper($est->nombre) }}</option>
            @endforeach
        </select>
        @if ($search || $filterStatus || $filterArea || $filterCategoria)
        <button wire:click="clearFilters" style="background:none; border:1px solid #ef4444; color:#ef4444; font-size:12px; padding:8px 14px; cursor:pointer; font-family:monospace;">✕ LIMPIAR</button>
        @endif
        <div style="margin-left:auto; display:flex; gap:24px; align-items:center;">
            <span style="color:#ef4444; font-size:16px; font-weight:900;">⚠ {{ $ticketsSinAsignar->count() }} SIN ASIGNAR</span>
            <span style="color:#ffffff; font-size:12px;">{{ $usuariosConTickets->count() }} TÉCNICOS</span>
        </div>
    </div>

    {{-- BOARD --}}
    <div style="flex:1; display:flex; overflow-x:auto; overflow-y:hidden; min-width:0;">

        {{-- COLUMNA SIN ASIGNAR --}}
        <div id="columna-sin-asignar" style="flex-shrink:0; width:360px; display:flex; flex-direction:column; border-right:1px solid #222; background:#000; transition:background 0.3s;">
            <div style="flex-shrink:0; padding:16px 20px; border-bottom:3px solid #ef4444; background:#0a0000;">
                <div style="color:#ef4444; font-size:20px; font-weight:900; letter-spacing:2px;">SIN ASIGNAR</div>
                <div style="color:#ef4444; font-size:40px; font-weight:900; line-height:1;">{{ $ticketsSinAsignar->count() }}</div>
                <div style="color:#ffffff; font-size:11px; letter-spacing:2px;">TICKETS ESPERANDO</div>
            </div>
            <div style="flex:1; overflow-y:auto; padding:12px; display:flex; flex-direction:column; gap:10px;">
                @forelse($ticketsSinAsignar as $ticket)
                    @include('livewire.admin.partials.ticket-card', ['ticket' => $ticket, 'colorAccent' => 'red'])
                @empty
                <div style="color:#222; text-align:center; padding:40px 0; font-size:12px; letter-spacing:2px;">TODO EN ORDEN ✓</div>
                @endforelse
            </div>
        </div>

        {{-- COLUMNAS TÉCNICOS --}}
        @forelse($usuariosConTickets as $index => $usuario)
        @php
            $cols = ['#3b82f6','#8b5cf6','#06b6d4','#10b981','#f97316','#ec4899','#14b8a6','#6366f1'];
            $accent = $cols[$index % count($cols)];
            $colorAccents = ['blue','violet','cyan','emerald','orange','pink','teal','indigo'];
            $colorAccent = $colorAccents[$index % count($colorAccents)];
            $maxTickets = $usuariosConTickets->first()->tickets_activos_count ?? 1;
            $porcentaje = $maxTickets > 0 ? ($usuario->tickets_activos_count / $maxTickets * 100) : 0;
        @endphp
        <div style="flex-shrink:0; width:480px; display:flex; flex-direction:column; border-right:1px solid #222; background:#000;">
            <div style="flex-shrink:0; padding:16px 20px; border-bottom:3px solid {{ $accent }}; background:#050505;">
                <div style="color:{{ $accent }}; font-size:18px; font-weight:900; letter-spacing:1px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    {{ strtoupper($usuario->name) }}
                </div>
                <div style="color:#fff; font-size:40px; font-weight:900; line-height:1;">{{ $usuario->tickets_activos_count }}</div>
                <div style="background:#111; height:6px; margin-top:8px; border-radius:3px;">
                    <div style="height:100%; width:{{ $porcentaje }}%; background:{{ $accent }}; border-radius:3px;"></div>
                </div>
            </div>
            <div style="flex:1; overflow-y:auto; padding:12px; display:flex; flex-direction:column; gap:10px;">
                @forelse($usuario->ticketsActivos as $ticket)
                    @include('livewire.admin.partials.ticket-card', ['ticket' => $ticket, 'colorAccent' => $colorAccent])
                @empty
                <div style="color:#222; text-align:center; padding:40px 0; font-size:12px; letter-spacing:2px;">SIN TICKETS</div>
                @endforelse
            </div>
        </div>
        @empty
        <div style="flex:1; display:flex; align-items:center; justify-content:center; color:#222; font-size:14px; letter-spacing:3px;">
            NO HAY TÉCNICOS ACTIVOS
        </div>
        @endforelse
    </div>

    {{-- MODAL DETALLE --}}
    @if($showDetailModal && $ticketDetail)
    <div style="position:fixed; inset:0; z-index:50; background:rgba(0,0,0,0.85); display:flex; align-items:center; justify-content:center; padding:20px;">
        <div style="background:#111; border:1px solid #333; width:100%; max-width:520px; max-height:85vh; overflow-y:auto;">
            <div style="display:flex; align-items:center; justify-content:space-between; padding:20px 24px; border-bottom:1px solid #222; position:sticky; top:0; background:#111;">
                <div>
                    <div style="color:#fff; font-size:16px; font-weight:900; letter-spacing:2px;">DETALLE TICKET</div>
                    <div style="color:#555; font-size:13px;">#{{ $ticketDetail->correlativo }}</div>
                </div>
                <button wire:click="closeDetailModal" style="color:#555; background:none; border:none; font-size:20px; cursor:pointer;">✕</button>
            </div>
            <div style="padding:24px;">
                @php
                $fields = [
                    'Solicitante'    => $ticketDetail->nombres,
                    'Área'           => optional($ticketDetail->area)->nombre,
                    'Incidencia'     => optional($ticketDetail->categoriaIncidencia)->nombre,
                    'Sub-Incidencia' => optional($ticketDetail->subCategoriaIncidencia)->nombre,
                    'Estado'         => optional($ticketDetail->tipoEstado)->nombre,
                    'Técnico'        => optional($ticketDetail->usuario)->name ?? 'Sin asignar',
                    'Creado'         => \Carbon\Carbon::parse($ticketDetail->fecha_creacion)->format('d/m/Y H:i'),
                ];
                @endphp
                @foreach($fields as $label => $value)
                <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #1a1a1a;">
                    <span style="color:#555; font-size:11px; letter-spacing:2px; text-transform:uppercase;">{{ $label }}</span>
                    <span style="color:#fff; font-size:14px; font-weight:600; text-align:right;">{{ $value ?? '—' }}</span>
                </div>
                @endforeach
                @if($ticketDetail->descripcion)
                <div style="margin-top:16px;">
                    <div style="color:#ffffff; font-size:11px; letter-spacing:2px; margin-bottom:8px;">DESCRIPCIÓN</div>
                    <div style="color:#ffffff; font-size:13px; line-height:1.6; background:#0a0a0a; padding:14px; border:1px solid #1a1a1a;">{{ $ticketDetail->descripcion }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL HISTORIAL --}}
    @if($showHistorialModal)
    <div style="position:fixed; inset:0; z-index:50; background:rgba(0,0,0,0.85); display:flex; align-items:center; justify-content:center; padding:20px;">
        <div style="background:#111; border:1px solid #333; width:100%; max-width:640px; max-height:85vh; overflow-y:auto;">
            <div style="display:flex; align-items:center; justify-content:space-between; padding:20px 24px; border-bottom:1px solid #222; position:sticky; top:0; background:#111;">
                <div>
                    <div style="color:#fff; font-size:16px; font-weight:900; letter-spacing:2px;">HISTORIAL</div>
                    <div style="color:#ffffff; font-size:13px;">{{ $historialTicket->count() }} registros</div>
                </div>
                <button wire:click="closeHistorialModal" style="color:#555; background:none; border:none; font-size:20px; cursor:pointer;">✕</button>
            </div>
            <div style="padding:20px; display:flex; flex-direction:column; gap:10px;">
                @forelse($historialTicket as $auditoria)
                <div style="background:#0a0a0a; border:1px solid #222; border-left:3px solid #8b5cf6; padding:16px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                        <span style="color:#8b5cf6; font-size:11px; font-weight:900; letter-spacing:2px;">{{ $auditoria->accion }}</span>
                        <span style="color:#ffffff; font-size:11px;">{{ \Carbon\Carbon::parse($auditoria->fecha_auditoria)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div style="color:#aaa; font-size:13px;">Estado: <span style="color:#fff;">{{ $auditoria->estado_nombre ?? '—' }}</span></div>
                    <div style="color:#aaa; font-size:13px;">Técnico: <span style="color:#fff;">{{ $auditoria->usuario_nombre ?? '—' }}</span></div>
                </div>
                @empty
                <div style="color:#333; text-align:center; padding:40px; font-size:12px; letter-spacing:2px;">SIN REGISTROS</div>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL ELIMINAR --}}
    @if($showDeleteModal)
    <div style="position:fixed; inset:0; z-index:50; background:rgba(0,0,0,0.85); display:flex; align-items:center; justify-content:center; padding:20px;">
        <div style="background:#111; border:1px solid #ef444455; width:100%; max-width:360px; padding:32px; text-align:center;">
            <div style="color:#ef4444; font-size:18px; font-weight:900; letter-spacing:2px; margin-bottom:10px;">ELIMINAR TICKET</div>
            <div style="color:#555; font-size:13px; margin-bottom:28px;">Esta acción es irreversible.</div>
            <div style="display:flex; gap:12px;">
                <button wire:click="cancelDelete" style="flex:1; background:none; border:1px solid #333; color:#777; font-size:13px; padding:12px; cursor:pointer; font-family:monospace;">CANCELAR</button>
                <button wire:click="deleteTicket" style="flex:1; background:#ef444411; border:1px solid #ef4444; color:#ef4444; font-size:13px; padding:12px; cursor:pointer; font-family:monospace; font-weight:900;">ELIMINAR</button>
            </div>
        </div>
    </div>
    @endif

<style>
@keyframes shrink { from { width:100%; } to { width:0%; } }
::-webkit-scrollbar { width:3px; height:3px; }
::-webkit-scrollbar-track { background:#000; }
::-webkit-scrollbar-thumb { background:#222; }
</style>
</div>