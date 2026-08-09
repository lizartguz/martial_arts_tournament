<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanYearlyDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean-yearly {--start-year=2025} {--end-year=2030}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia todas las tablas de las bases de datos anuales (senvatec_db_XXXX)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startYear = $this->option('start-year');
        $endYear = $this->option('end-year');
        
        $this->info("🗑️  Limpiando bases de datos anuales ({$startYear} - {$endYear})...\n");
        
        if (!$this->confirm('⚠️  ADVERTENCIA: Esto eliminará TODAS las tablas de las bases de datos anuales. ¿Deseas continuar?')) {
            $this->warn('Operación cancelada.');
            return 0;
        }
        
        $totalCleaned = 0;
        
        for ($year = $startYear; $year <= $endYear; $year++) {
            $connection = 'senvatec_db_' . $year;
            
            // Verificar si la conexión existe
            if (!config("database.connections.{$connection}")) {
                $this->warn("⚠️  Conexión '{$connection}' no encontrada en config/database.php - Saltando...");
                continue;
            }
            
            // Verificar si la conexión es válida
            try {
                DB::connection($connection)->getPdo();
                
                $this->line("📋 Procesando '{$connection}'...");
                
                // Obtener todas las tablas de esta base de datos
                $tables = DB::connection($connection)
                    ->select('SHOW TABLES');
                
                $tableKey = 'Tables_in_' . config("database.connections.{$connection}.database");
                
                if (count($tables) > 0) {
                    $this->line("   Tablas encontradas: " . count($tables));
                    
                    // Deshabilitar restricciones de clave foránea
                    DB::connection($connection)->statement('SET FOREIGN_KEY_CHECKS=0');
                    
                    foreach ($tables as $table) {
                        $tableName = $table->$tableKey;
                        $this->line("   🗑️  Eliminando tabla: {$tableName}");
                        Schema::connection($connection)->dropIfExists($tableName);
                        $totalCleaned++;
                    }
                    
                    // Habilitar restricciones de clave foránea
                    DB::connection($connection)->statement('SET FOREIGN_KEY_CHECKS=1');
                    
                    $this->info("   ✅ Base de datos '{$connection}' limpiada completamente.");
                } else {
                    $this->line("   ℹ️  No hay tablas para eliminar.");
                }
                
            } catch (\Exception $e) {
                $this->warn("   ⚠️  No se pudo conectar a '{$connection}' - Saltando... ({$e->getMessage()})");
                continue;
            }
        }
        
        $this->newLine();
        $this->info("🎉 Limpieza completada!");
        $this->info("📊 Total de tablas eliminadas: {$totalCleaned}");
        $this->info("💡 Ahora puedes ejecutar: php artisan migrate:fresh --seed");
        
        return 0;
    }
}
