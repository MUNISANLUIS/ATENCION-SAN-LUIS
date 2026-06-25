<?php

namespace App\Jobs;

use App\Models\Sistema;
use App\Models\Ticket as TicketModel;
use App\Models\TicketAuditoria;
use App\Events\TicketCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckAllSistemas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    // ─── Configuración fija ──────────────────────────────────────────
    const ID_AREA            = 27;
    const NOMBRES            = 'Sistemas';
    const ID_TIPO_INCIDENCIA = 14;
    const ID_SUB_INCIDENCIA  = 103;
    const USUARIO_CREACION   = 893;
    const UMBRAL_DEGRADADO   = 8000; // ms → ticket DEGRADADO
    const UMBRAL_CAIDO       = 15000; // ms → ticket CAÍDO aunque responda
    const TIMEOUT_HTTP       = 5;    // segundos máx de espera HTTP
    // ────────────────────────────────────────────────────────────────

    public function handle(): void
    {
        Log::info('=== INICIO CHECK ALL SISTEMAS ===');
        $overallStart = microtime(true);

        try {
            $sistemas = Sistema::where('estado', 1)->get();
            Log::info('Sistemas activos obtenidos', ['total' => $sistemas->count()]);

            $checked   = 0;
            $online    = 0;
            $offline   = 0;
            $errors    = 0;
            $latencias = [];

            foreach ($sistemas as $index => $sistema) {
                Log::info("Verificando sistema [{$index}/{$sistemas->count()}]", [
                    'id'     => $sistema->id,
                    'nombre' => $sistema->nombre,
                    'url'    => $sistema->url_base,
                ]);

                try {
                    $headers = [];
                    if ($sistema->headers) {
                        try {
                            $decoded = json_decode($sistema->headers, true);
                            if (is_array($decoded)) {
                                $headers = $decoded;
                            }
                        } catch (\Exception $e) {
                            Log::warning('Headers inválidos para sistema', [
                                'sistema_id' => $sistema->id,
                                'error'      => $e->getMessage(),
                            ]);
                        }
                    }

                    $startTime = microtime(true);

                    $response = Http::timeout(self::TIMEOUT_HTTP) // ← fijo 5s, ignora el de BD
                        ->withHeaders($headers)
                        ->get($sistema->url_base);

                    $endTime  = microtime(true);
                    $latencia = round(($endTime - $startTime) * 1000);
                    $latencias[] = $latencia;

        Log::info('=  ANTES DEL IF UMBRAL =');
                    // ─── Clasificar por latencia ──────────────────────────────
                    if ($latencia >= self::UMBRAL_CAIDO) {
                        // Respondió pero tardó demasiado → CAÍDO
                        $nuevoEstado = 'offline';

                        $sistema->ultimo_estado  = 'offline';
                        $sistema->ultimo_chequeo = now();
                        $sistema->latencia_ms    = $latencia;
                        $sistema->codigo_http    = $response->status();
                        $sistema->save();

                        $offline++;
                        $checked++;

                        Log::warning('⚠ Sistema CAÍDO por latencia crítica', [
                            'id'          => $sistema->id,
                            'nombre'      => $sistema->nombre,
                            'latencia_ms' => $latencia,
                            'codigo_http' => $response->status(),
                        ]);

        Log::info('=  ANTES DE CREAR TICKET =');
                        $this->crearTicketAutomatico(
                            $sistema->nombre,
                            'CAÍDO',
                            "SISTEMA {$sistema->nombre} - LATENCIA: {$latencia} ms - Tiempo de respuesta crítico, sistema considerado caído"
                        );

                    } elseif ($latencia >= self::UMBRAL_DEGRADADO) {
                        // Respondió pero está lento → DEGRADADO
                        $nuevoEstado = 'online';

                        $sistema->ultimo_estado  = 'online';
                        $sistema->ultimo_chequeo = now();
                        $sistema->latencia_ms    = $latencia;
                        $sistema->codigo_http    = $response->status();
                        $sistema->save();

                        $online++;
                        $checked++;

                        Log::warning('🟡 Sistema DEGRADADO (lento)', [
                            'id'          => $sistema->id,
                            'nombre'      => $sistema->nombre,
                            'latencia_ms' => $latencia,
                            'codigo_http' => $response->status(),
                        ]);

                        $this->crearTicketAutomatico(
                            $sistema->nombre,
                            'DEGRADADO',
                            "SISTEMA {$sistema->nombre} - LATENCIA: {$latencia} ms - Lentitud detectada automáticamente"
                        );

                    } else {
                        // Normal ✓
                        $nuevoEstado = $response->successful() ? 'online' : 'offline';

                        $sistema->ultimo_estado  = $nuevoEstado;
                        $sistema->ultimo_chequeo = now();
                        $sistema->latencia_ms    = $latencia;
                        $sistema->codigo_http    = $response->status();
                        $sistema->save();

                        $nuevoEstado === 'online' ? $online++ : $offline++;
                        $checked++;

                        Log::info('✓ Sistema verificado OK', [
                            'id'          => $sistema->id,
                            'nombre'      => $sistema->nombre,
                            'latencia_ms' => $latencia,
                            'estado'      => $nuevoEstado,
                            'codigo_http' => $response->status(),
                        ]);
                    }

                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    $endTime  = microtime(true);
                    $latencia = round(($endTime - $startTime) * 1000);

                    Log::error('✗ Error de conexión (CAÍDO)', [
                        'sistema_id'  => $sistema->id,
                        'nombre'      => $sistema->nombre,
                        'latencia_ms' => $latencia,
                        'error'       => $e->getMessage(),
                    ]);

                    $sistema->ultimo_estado  = 'offline';
                    $sistema->ultimo_chequeo = now();
                    $sistema->latencia_ms    = null;
                    $sistema->codigo_http    = null;
                    $sistema->save();

                    $offline++;
                    $errors++;

                    $this->crearTicketAutomatico(
                        $sistema->nombre,
                        'CAÍDO',
                        "SISTEMA {$sistema->nombre} - SIN RESPUESTA - Caída del sistema detectada automáticamente"
                    );

                } catch (\Exception $e) {
                    Log::error('✗ Error general (CAÍDO)', [
                        'sistema_id' => $sistema->id,
                        'nombre'     => $sistema->nombre,
                        'error'      => $e->getMessage(),
                        'tipo'       => get_class($e),
                    ]);

                    $sistema->ultimo_estado  = 'offline';
                    $sistema->ultimo_chequeo = now();
                    $sistema->latencia_ms    = null;
                    $sistema->codigo_http    = null;
                    $sistema->save();

                    $offline++;
                    $errors++;

                    $this->crearTicketAutomatico(
                        $sistema->nombre,
                        'CAÍDO',
                        "SISTEMA {$sistema->nombre} - ERROR: {$e->getMessage()} - Caída detectada automáticamente"
                    );
                }
            }

            $overallEnd  = microtime(true);
            $totalTime   = round(($overallEnd - $overallStart) * 1000, 2);
            $avgLatencia = count($latencias) > 0
                ? round(array_sum($latencias) / count($latencias), 2)
                : 0;

            Log::info('=== FIN CHECK ALL SISTEMAS ===', [
                'total_sistemas'       => $sistemas->count(),
                'verificados'          => $checked,
                'online'               => $online,
                'offline'              => $offline,
                'errores'              => $errors,
                'tiempo_total_ms'      => $totalTime,
                'latencia_promedio_ms' => $avgLatencia,
                'latencia_min_ms'      => count($latencias) > 0 ? min($latencias) : 0,
                'latencia_max_ms'      => count($latencias) > 0 ? max($latencias) : 0,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fatal en CheckAllSistemas Job', [
                'error'   => $e->getMessage(),
                'tipo'    => get_class($e),
                'archivo' => $e->getFile(),
                'linea'   => $e->getLine(),
            ]);
        }
    }

    // ─── Método reutilizable para crear tickets automáticos ──────────
    private function crearTicketAutomatico(string $sistemaNombre, string $tipo, string $descripcion): void
    {
        Log::info("🎫 INTENTANDO crear ticket [{$tipo}] para: {$sistemaNombre}");

        try {
            // Correlativo seguro
            $ultimo      = TicketModel::orderBy('id', 'desc')->first();
            $numero      = $ultimo ? (intval($ultimo->correlativo) + 1) : 1;
            $correlativo = str_pad($numero, 6, '0', STR_PAD_LEFT);

            $ticket = TicketModel::create([
                'correlativo'        => $correlativo,
                'id_area'            => self::ID_AREA,
                'nombres'            => self::NOMBRES,
                'id_tipo_incidencia' => self::ID_TIPO_INCIDENCIA,
                'id_sub_incidencia'  => self::ID_SUB_INCIDENCIA,
                'descripcion'        => $descripcion,
                'estado'             => '3',
                'usuario_creacion'   => self::USUARIO_CREACION,
                'fecha_creacion'     => now(),
                'estacion_creacion'  => 'SCHEDULER-AUTOMATICO',
            ]);

            Log::info("🎫 Ticket creado en BD con id: {$ticket->id}");

            // Auditoría
            TicketAuditoria::create([
                'id_ticket'              => $ticket->id,
                'correlativo'            => $ticket->correlativo,
                'id_area'                => $ticket->id_area,
                'area_nombre'            => optional($ticket->area)->nombre,
                'nombres'                => $ticket->nombres,
                'id_tipo_incidencia'     => $ticket->id_tipo_incidencia,
                'tipo_incidencia_nombre' => optional($ticket->categoriaIncidencia)->nombre,
                'id_sub_incidencia'      => $ticket->id_sub_incidencia,
                'sub_incidencia_nombre'  => optional($ticket->subCategoriaIncidencia)->nombre,
                'descripcion'            => $ticket->descripcion,
                'estado'                 => $ticket->estado,
                'estado_nombre'          => optional($ticket->tipoEstado)->nombre,
                'id_usuario'             => null,
                'usuario_nombre'         => 'SISTEMA AUTOMATICO',
                'usuario_creacion'       => $ticket->usuario_creacion,
                'fecha_creacion'         => $ticket->fecha_creacion,
                'usuario_actualizacion'  => null,
                'fecha_actualizacion'    => null,
                'estacion_creacion'      => $ticket->estacion_creacion,
                'respuesta'              => null,
                'accion'                 => 'CREATE',
                'fecha_auditoria'        => now(),
                'id_usuario_auditoria'   => null,
            ]);

            // Evento
            event(new TicketCreated($ticket));

            Log::warning("✅ Ticket automático [{$tipo}] creado exitosamente", [
                'correlativo' => $correlativo,
                'sistema'     => $sistemaNombre,
                'descripcion' => $descripcion,
            ]);

        } catch (\Exception $e) {
            Log::error('❌ FALLO al crear ticket automático', [
                'sistema' => $sistemaNombre,
                'tipo'    => $tipo,
                'error'   => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea'   => $e->getLine(),
            ]);
        }
    }
}