<?php

namespace App\Console\Commands;

use App\Models\NotificationM;
use App\Services\NotificationAttachmentService;
use Illuminate\Console\Command;

class MigrateNotificationImagesToPrivate extends Command
{
    protected $signature = 'app:notifications-migrate-images-private {--dry-run : Muestra los cambios sin mover archivos}';

    protected $description = 'Mueve los adjuntos de avisos desde el disco publico al almacenamiento privado';

    /**
     * Ejecuta la operación principal del comando.
     */
    public function handle(NotificationAttachmentService $notificationAttachmentService): int
    {
        $summary = [
            'migrated' => 0,
            'already_private' => 0,
            'missing' => 0,
            'skipped' => 0,
        ];

        NotificationM::query()
            ->select(['id', ...array_values(NotificationAttachmentService::SLOT_COLUMNS)])
            ->orderBy('id')
            /**
             * Procesa los registros por lotes controlados.
             */
            ->chunkById(100, function ($notifications) use ($notificationAttachmentService, &$summary) {
                foreach ($notifications as $notification) {
                    foreach (NotificationAttachmentService::SLOT_COLUMNS as $column) {
                        $result = $notificationAttachmentService->migratePublicFile(
                            $notification->{$column},
                            (bool) $this->option('dry-run')
                        );
                        $summary[$result]++;
                    }
                }
            });

        $this->table(
            ['Estado', 'Cantidad'],
            collect($summary)->map(fn (int $count, string $status) => [$status, $count])->values()->all()
        );

        if ($this->option('dry-run')) {
            $this->warn('Simulacion finalizada: no se movio ningun archivo.');
        } else {
            $this->info('Migracion de adjuntos privados finalizada.');
        }

        return self::SUCCESS;
    }
}
