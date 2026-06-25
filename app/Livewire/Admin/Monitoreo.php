<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sistema;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Monitoreo extends Component
{
    use WithPagination;

    // Propiedades de filtro
    public $search = '';
    public $filterEstado = '';
    public $filterStatusMonitoreo = ''; // todos, online, offline
    public $perPage = 10;

    // Auto-refresh - DESACTIVADO POR DEFECTO para evitar lag
    public $autoRefresh = false;
    public $refreshInterval = 60; // 60 segundos (más espaciado)

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        Log::info('=== INICIO RENDER MONITOREO ===');
        $startTime = microtime(true);

        $sistemas = Sistema::query()
            ->select(['id', 'nombre', 'url_base', 'estado', 'ultimo_chequeo', 'ultimo_estado', 'latencia_ms', 'codigo_http'])
            ->when($this->search, function ($query) {
                Log::info('Aplicando filtro de búsqueda', ['search' => $this->search]);
                $query->where(function($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('url_base', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterEstado !== '', function ($query) {
                Log::info('Aplicando filtro de estado', ['estado' => $this->filterEstado]);
                $query->where('estado', $this->filterEstado);
            })
            ->when($this->filterStatusMonitoreo !== '', function ($query) {
                Log::info('Aplicando filtro de estado monitoreo', ['status' => $this->filterStatusMonitoreo]);
                $query->where('ultimo_estado', $this->filterStatusMonitoreo);
            })
            ->where('estado', 1) // Solo sistemas activos
            ->orderBy('nombre', 'asc')
            ->paginate($this->perPage);

        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2);
        Log::info('=== FIN RENDER MONITOREO ===', [
            'tiempo_ejecucion_ms' => $executionTime,
            'total_sistemas' => $sistemas->total(),
            'sistemas_pagina' => $sistemas->count()
        ]);

        return view('livewire.admin.monitoreo', [
            'sistemas' => $sistemas
        ]);
    }

    public function checkSistema($sistemaId)
    {
        Log::info('=== INICIO CHECK SISTEMA ===', ['sistema_id' => $sistemaId]);

        try {
            $sistema = Sistema::findOrFail($sistemaId);
            Log::info('Sistema encontrado', [
                'nombre' => $sistema->nombre,
                'url' => $sistema->url_base,
                'timeout' => $sistema->timeout_segundos
            ]);

            // Preparar headers si existen
            $headers = [];
            if ($sistema->headers) {
                try {
                    $headers = json_decode($sistema->headers, true) ?? [];
                    Log::info('Headers parseados', ['headers' => $headers]);
                } catch (\Exception $e) {
                    Log::warning('Error parseando headers', ['error' => $e->getMessage()]);
                }
            }

            // Medir el tiempo de respuesta
            Log::info('Iniciando petición HTTP...');
            $startTime = microtime(true);

            $response = Http::timeout($sistema->timeout_segundos)
                ->withHeaders($headers)
                ->get($sistema->url_base);

            $endTime = microtime(true);
            $latencia = round(($endTime - $startTime) * 1000);

            Log::info('Petición HTTP completada', [
                'status' => $response->status(),
                'latencia_ms' => $latencia,
                'successful' => $response->successful()
            ]);

            // Actualizar sistema con los campos correctos
            $sistema->ultimo_chequeo = now();
            $sistema->ultimo_estado = $response->successful() ? 'online' : 'offline';
            $sistema->latencia_ms = $latencia;
            $sistema->codigo_http = $response->status();
            $sistema->save();

            Log::info('Sistema actualizado correctamente', [
                'ultimo_estado' => $sistema->ultimo_estado,
                'latencia_ms' => $latencia,
                'codigo_http' => $sistema->codigo_http
            ]);

            session()->flash('message', "✅ Sistema verificado: {$sistema->nombre} - Latencia: {$latencia}ms - Estado: {$sistema->ultimo_estado} - HTTP: {$sistema->codigo_http}");

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Error de conexión al verificar sistema', [
                'sistema_id' => $sistemaId,
                'error' => $e->getMessage(),
                'tipo' => 'ConnectionException'
            ]);

            Sistema::where('id', $sistemaId)->update([
                'ultimo_estado' => 'offline',
                'ultimo_chequeo' => now(),
                'latencia_ms' => null,
                'codigo_http' => null,
            ]);

            session()->flash('error', '❌ No se pudo conectar al sistema: Tiempo de espera agotado');

        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Error en la petición HTTP', [
                'sistema_id' => $sistemaId,
                'error' => $e->getMessage(),
                'tipo' => 'RequestException'
            ]);

            Sistema::where('id', $sistemaId)->update([
                'ultimo_estado' => 'offline',
                'ultimo_chequeo' => now(),
                'latencia_ms' => null,
                'codigo_http' => null,
            ]);

            session()->flash('error', '❌ Error en la petición HTTP: ' . $e->getMessage());

        } catch (\Exception $e) {
            Log::error('Error general al verificar sistema', [
                'sistema_id' => $sistemaId,
                'error' => $e->getMessage(),
                'tipo' => get_class($e),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            Sistema::where('id', $sistemaId)->update([
                'ultimo_estado' => 'offline',
                'ultimo_chequeo' => now(),
                'latencia_ms' => null,
            ]);

            session()->flash('error', '❌ Error al verificar el sistema: ' . $e->getMessage());
        }

        Log::info('=== FIN CHECK SISTEMA ===');
    }

    public function checkAllSistemas()
    {
        Log::info('=== INICIO CHECK ALL SISTEMAS ===');
        $overallStart = microtime(true);

        try {
            $sistemas = Sistema::where('estado', 1)->get();
            Log::info('Sistemas activos obtenidos', ['total' => $sistemas->count()]);

            $checked = 0;
            $online = 0;
            $offline = 0;
            $errors = 0;
            $latencias = [];

            foreach ($sistemas as $index => $sistema) {
                Log::info("Verificando sistema [{$index}}/{$sistemas->count()}", [
                    'id' => $sistema->id,
                    'nombre' => $sistema->nombre,
                    'url' => $sistema->url_base
                ]);

                try {
                    $headers = [];
                    if ($sistema->headers) {
                        try {
                            $headers = json_decode($sistema->headers, true) ?? [];
                        } catch (\Exception $e) {
                            Log::warning('Headers inválidos para sistema', [
                                'sistema_id' => $sistema->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }

                    $startTime = microtime(true);

                    $response = Http::timeout($sistema->timeout_segundos)
                        ->withHeaders($headers)
                        ->get($sistema->url_base);

                    $endTime = microtime(true);
                    $latencia = round(($endTime - $startTime) * 1000);
                    $latencias[] = $latencia;

                    $nuevoEstado = $response->successful() ? 'online' : 'offline';

                    $sistema->ultimo_chequeo = now();
                    $sistema->ultimo_estado = $nuevoEstado;
                    $sistema->latencia_ms = $latencia;
                    $sistema->codigo_http = $response->status();
                    $sistema->save();

                    if ($nuevoEstado === 'online') {
                        $online++;
                    } else {
                        $offline++;
                    }

                    Log::info("✓ Sistema verificado OK", [
                        'id' => $sistema->id,
                        'nombre' => $sistema->nombre,
                        'latencia_ms' => $latencia,
                        'estado' => $nuevoEstado,
                        'codigo_http' => $response->status()
                    ]);

                    $checked++;

                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::error("✗ Error de conexión en sistema", [
                        'sistema_id' => $sistema->id,
                        'nombre' => $sistema->nombre,
                        'error' => $e->getMessage(),
                        'tipo' => 'ConnectionException'
                    ]);

                    $sistema->ultimo_estado = 'offline';
                    $sistema->ultimo_chequeo = now();
                    $sistema->latencia_ms = null;
                    $sistema->codigo_http = null;
                    $sistema->save();
                    $offline++;
                    $errors++;

                } catch (\Exception $e) {
                    Log::error("✗ Error general en sistema", [
                        'sistema_id' => $sistema->id,
                        'nombre' => $sistema->nombre,
                        'error' => $e->getMessage(),
                        'tipo' => get_class($e)
                    ]);

                    $sistema->ultimo_estado = 'offline';
                    $sistema->ultimo_chequeo = now();
                    $sistema->latencia_ms = null;
                    $sistema->save();
                    $offline++;
                    $errors++;
                }
            }

            $overallEnd = microtime(true);
            $totalTime = round(($overallEnd - $overallStart) * 1000, 2);
            $avgLatencia = count($latencias) > 0 ? round(array_sum($latencias) / count($latencias), 2) : 0;

            Log::info('=== FIN CHECK ALL SISTEMAS ===', [
                'total_sistemas' => $sistemas->count(),
                'verificados' => $checked,
                'online' => $online,
                'offline' => $offline,
                'errores' => $errors,
                'tiempo_total_ms' => $totalTime,
                'latencia_promedio_ms' => $avgLatencia,
                'latencia_min_ms' => count($latencias) > 0 ? min($latencias) : 0,
                'latencia_max_ms' => count($latencias) > 0 ? max($latencias) : 0
            ]);

            session()->flash('message', "✅ Verificados: {$checked} sistemas | 🟢 Online: {$online} | 🔴 Offline: {$offline}" .
                ($errors > 0 ? " | ⚠️ Errores: {$errors}" : ""));

        } catch (\Exception $e) {
            Log::error('Error fatal en checkAllSistemas', [
                'error' => $e->getMessage(),
                'tipo' => get_class($e),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', '❌ Error al verificar sistemas: ' . $e->getMessage());
        }
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;
        Log::info('Toggle Auto-refresh', [
            'activo' => $this->autoRefresh,
            'intervalo_segundos' => $this->refreshInterval
        ]);

        if ($this->autoRefresh) {
            session()->flash('message', "🔄 Auto-actualización activada (cada {$this->refreshInterval} segundos)");
        } else {
            session()->flash('message', '⏸️ Auto-actualización desactivada');
        }
    }

    public function clearFilters()
    {
        Log::info('Limpiando filtros de monitoreo');
        $this->reset(['search', 'filterEstado', 'filterStatusMonitoreo']);
        $this->resetPage();
    }

    public function updatingSearch()
    {
        Log::debug('Actualizando búsqueda', ['nuevo_valor' => $this->search]);
        $this->resetPage();
    }

    public function updatingFilterEstado()
    {
        Log::debug('Actualizando filtro estado', ['nuevo_valor' => $this->filterEstado]);
        $this->resetPage();
    }

    public function updatingFilterStatusMonitoreo()
    {
        Log::debug('Actualizando filtro estado monitoreo', ['nuevo_valor' => $this->filterStatusMonitoreo]);
        $this->resetPage();
    }

    public function mount()
    {
        Log::info('Componente Monitoreo montado', [
            'autoRefresh' => $this->autoRefresh,
            'refreshInterval' => $this->refreshInterval,
            'perPage' => $this->perPage
        ]);
    }
}
