<?php

namespace App\Jobs;

use App\Models\BackgroundTask;
use App\Models\DesignFormat;
use App\Services\BackgroundTaskService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessParticipationCreationTask implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly string $taskUuid)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(BackgroundTaskService $backgroundTaskService): void
    {
        $task = BackgroundTask::query()->where('uuid', $this->taskUuid)->first();
        if (! $task) {
            return;
        }

        $payload = (array) ($task->payload ?? []);
        $designFormatId = (int) ($payload['design_format_id'] ?? 0);
        if ($designFormatId <= 0) {
            throw new \RuntimeException('Payload inválido: falta design_format_id.');
        }

        $designFormat = DesignFormat::with('set')->findOrFail($designFormatId);
        $total = (int) ($designFormat->set->total_participations ?? 0);
        $backgroundTaskService->markRunning($task, max(1, $total));

        if ($task->fresh()->status === BackgroundTask::STATUS_CANCELLED) {
            return;
        }

        // Reusa la lógica actual de negocio del modelo, pero ahora en background.
        $created = (int) ($designFormat->generateParticipations() ?: 0);
        $backgroundTaskService->updateProgress($task, max(1, $total), max(1, $total));
        $backgroundTaskService->complete($task, [
            'design_format_id' => $designFormatId,
            'set_id' => (int) ($designFormat->set_id ?? 0),
            'total_expected' => $total,
            'created' => $created,
            'message' => 'Participaciones generadas en segundo plano.',
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $task = BackgroundTask::query()->where('uuid', $this->taskUuid)->first();
        if (! $task) {
            return;
        }
        app(BackgroundTaskService::class)->fail($task, $exception->getMessage());
    }
}
