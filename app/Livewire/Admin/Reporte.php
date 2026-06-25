<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Ticket;

use Illuminate\Support\Facades\Log;

class Reporte extends Component
{
    public $fechaInicio;
    public $fechaFin;
    public $estadosSeleccionados = [];
    public $idArea;
    public $idTipoIncidencia;

    public function mount()
    {
        // ✅ Ambas fechas = HOY por defecto
        $this->fechaInicio      = now()->format('Y-m-d');
        $this->fechaFin         = now()->format('Y-m-d');
        $this->idArea           = '';
        $this->idTipoIncidencia = '';

        // Por defecto: todos los estados MENOS Anulado (id=1)
        $this->estadosSeleccionados = \App\Models\TipoEstado::where('estado', '1')
            ->where('id', '!=', 1)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function render()
    {
        $tickets         = $this->getTickets();
        $areas           = \App\Models\Area::where('estado', '1')->get();
        $tiposEstado     = \App\Models\TipoEstado::where('estado', '1')->get();
        $tiposIncidencia = \App\Models\CategoriaIncidencia::where('estado', '1')->get();

        return view('livewire.admin.reporte', [
            'tickets'         => $tickets,
            'areas'           => $areas,
            'tiposEstado'     => $tiposEstado,
            'tiposIncidencia' => $tiposIncidencia,
        ]);
    }

    private function getTickets()
    {
        $query = Ticket::with(['area', 'categoriaIncidencia', 'subCategoriaIncidencia', 'usuario', 'tipoEstado'])
            // ✅ SQL Server: CAST para comparar solo la parte de fecha sin horas
            ->whereRaw('CAST(fecha_creacion AS DATE) >= ?', [$this->fechaInicio])
            ->whereRaw('CAST(fecha_creacion AS DATE) <= ?', [$this->fechaFin]);

        if (!empty($this->estadosSeleccionados)) {
            $query->whereIn('estado', $this->estadosSeleccionados);
        }

        if ($this->idArea) {
            $query->where('id_area', $this->idArea);
        }

        if ($this->idTipoIncidencia) {
            $query->where('id_tipo_incidencia', $this->idTipoIncidencia);
        }

        return $query->orderBy('fecha_creacion', 'desc')->get();
    }

    public function toggleEstado($id)
    {
        $id = (string) $id;
        if (in_array($id, $this->estadosSeleccionados)) {
            $this->estadosSeleccionados = array_values(
                array_filter($this->estadosSeleccionados, fn($e) => $e !== $id)
            );
        } else {
            $this->estadosSeleccionados[] = $id;
        }
    }
public function exportarExcel()
{
    try {
        $tickets  = $this->getTickets();
        $fileName = 'reporte-tickets-' . now()->format('Y-m-d_H-i-s') . '.xls';

        $headers = [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0',
            'Pragma'              => 'no-cache',
        ];

        $content = $this->buildExcelHtml($tickets);

        return response()->stream(function () use ($content) {
            echo $content;
        }, 200, $headers);

    } catch (\Exception $e) {
        Log::error('Error al exportar Excel: ' . $e->getMessage());
        session()->flash('error', 'Error al exportar: ' . $e->getMessage());
    }
}
private function buildExcelHtml($tickets): string
    {
        $filas = '';
        foreach ($tickets as $i => $ticket) {
            $bg    = ($i % 2 === 0) ? '#FFFFFF' : '#EEF4FB';
            $filas .= '<tr style="background:' . $bg . ';">';
            $filas .= '<td style="' . $this->tdStyle() . 'text-align:center;">'  . $ticket->id . '</td>';
            $filas .= '<td style="' . $this->tdStyle() . 'text-align:center;">'  . htmlspecialchars($ticket->correlativo ?? '') . '</td>';
            $filas .= '<td style="' . $this->tdStyle() . '">' . htmlspecialchars($ticket->area->nombre ?? 'N/A') . '</td>';
            $filas .= '<td style="' . $this->tdStyle() . '">' . htmlspecialchars($ticket->nombres ?? '') . '</td>';
            $filas .= '<td style="' . $this->tdStyle() . '">' . htmlspecialchars($ticket->categoriaIncidencia->nombre ?? 'N/A') . '</td>';
            $filas .= '<td style="' . $this->tdStyle() . '">' . htmlspecialchars($ticket->subCategoriaIncidencia->nombre ?? 'N/A') . '</td>';
            $filas .= '<td style="' . $this->tdStyle() . '">' . htmlspecialchars($ticket->descripcion ?? '') . '</td>';
            $filas .= '<td style="' . $this->tdStyle() . '">' . htmlspecialchars($ticket->respuesta ?? 'Sin respuesta') . '</td>';
            $filas .= '<td style="' . $this->tdStyle() . 'text-align:center;">' . htmlspecialchars($ticket->tipoEstado->nombre ?? 'N/A') . '</td>';
            $filas .= '<td style="' . $this->tdStyle() . '">' . htmlspecialchars($ticket->usuario->name ?? 'Sin asignar') . '</td>';
            $filas .= '</tr>';
        }

        $thStyle = 'background:#2E5FA3;color:#FFFFFF;font-weight:bold;padding:8px 10px;
                    border:1px solid #1A3F7A;text-align:center;font-size:12px;white-space:nowrap;';

        return '
        <html xmlns:o="urn:schemas-microsoft-com:office:office"
              xmlns:x="urn:schemas-microsoft-com:office:excel">
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Calibri, Arial, sans-serif; font-size: 12px; }
                table { border-collapse: collapse; width: 100%; }
                tr:hover { background: #D6E4F7 !important; }
            </style>
        </head>
        <body>

            <table style="margin-bottom:10px;">
                <tr>
                    <td style="font-size:16px;font-weight:bold;color:#2E5FA3;padding:4px 0;">
                        Reporte de Tickets
                    </td>
                </tr>
                <tr>
                    <td style="font-size:11px;color:#666;">
                        Generado el: ' . now()->format('d/m/Y H:i:s') . '
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        Total registros: ' . count($tickets) . '
                    </td>
                </tr>
            </table>

            <table>
                <thead>
                    <tr>
                        <th style="' . $thStyle . '">ID</th>
                        <th style="' . $thStyle . '">Correlativo</th>
                        <th style="' . $thStyle . '">Área</th>
                        <th style="' . $thStyle . '">Usuario</th>
                        <th style="' . $thStyle . '">Tipo Incidencia</th>
                        <th style="' . $thStyle . '">Sub Categoría</th>
                        <th style="' . $thStyle . '">Descripción</th>
                        <th style="' . $thStyle . '">Respuesta</th>
                        <th style="' . $thStyle . '">Estado</th>
                        <th style="' . $thStyle . '">Usuario Asignado</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $filas . '
                </tbody>
            </table>

        </body>
        </html>';
    }

private function tdStyle(): string
{
    return 'padding:6px 10px;border:1px solid #BDC3C7;font-size:11px;vertical-align:middle;';
}

    public function limpiarFiltros()
    {
        $this->mount();
    }
}