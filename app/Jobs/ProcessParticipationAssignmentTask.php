<?php

namespace App\Jobs;

use App\Models\BackgroundTask;
use App\Models\EmailCommunicationLog;
use App\Models\Participation;
use App\Models\Seller;
use App\Services\CommunicationEmailService;
use App\Services\BackgroundTaskService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessParticipationAssignmentTask implements ShouldQueue
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
        $sellerId = (int) ($payload['seller_id'] ?? 0);
        $participations = $payload['participations'] ?? [];

        if ($sellerId <= 0 || !is_array($participations) || empty($participations)) {
            throw new \RuntimeException('Payload inválido para asignación.');
        }

        $seller = Seller::with('user')->findOrFail($sellerId);
        if ((int) $seller->status !== Seller::STATUS_ACTIVE) {
            throw new \RuntimeException('El vendedor no está activo.');
        }

        $total = count($participations);
        $backgroundTaskService->markRunning($task, $total);

        $chunkSize = 250;
        $assignedCount = 0;
        $processed = 0;
        $assignedParticipationIds = [];

        foreach (array_chunk($participations, $chunkSize) as $chunk) {
            $task->refresh();
            if ($task->status === BackgroundTask::STATUS_CANCELLED) {
                return;
            }

            $ids = array_values(array_unique(array_map(fn ($p) => (int) ($p['id'] ?? 0), $chunk)));
            $setIds = array_values(array_unique(array_map(fn ($p) => (int) ($p['set_id'] ?? 0), $chunk)));
            $ids = array_filter($ids, fn ($v) => $v > 0);
            $setIds = array_filter($setIds, fn ($v) => $v > 0);

            if (empty($ids) || empty($setIds)) {
                $processed += count($chunk);
                $backgroundTaskService->updateProgress($task, $processed, $total);
                continue;
            }

            $dbChunk = Participation::with(['set.reserve.lottery'])
                ->whereIn('id', $ids)
                ->whereIn('set_id', $setIds)
                ->where(function ($query) use ($seller) {
                    $query->where(function ($q) {
                        $q->where('status', 'disponible')->whereNull('seller_id');
                    })->orWhere(function ($q) use ($seller) {
                        $q->where('status', 'asignada')->where('seller_id', $seller->id);
                    });
                })
                ->get()
                ->keyBy('id');

            foreach ($chunk as $participationData) {
                $participationId = (int) ($participationData['id'] ?? 0);
                $setId = (int) ($participationData['set_id'] ?? 0);
                $participation = $dbChunk->get($participationId);
                if (!$participation || (int) $participation->set_id !== $setId) {
                    continue;
                }

                // update() para disparar observer/log como en flujo actual.
                $participation->update([
                    'seller_id' => $seller->id,
                    'sale_date' => now()->toDateString(),
                    'sale_time' => now()->toTimeString(),
                    'status' => 'asignada',
                ]);
                $assignedCount++;
                $assignedParticipationIds[] = $participation->id;
            }

            $processed += count($chunk);
            $backgroundTaskService->updateProgress($task, $processed, $total);
        }

        $this->sendAssignmentEmail($seller, $assignedParticipationIds, $assignedCount);

        $backgroundTaskService->complete($task, [
            'seller_id' => $seller->id,
            'requested' => $total,
            'assigned' => $assignedCount,
            'omitted' => max(0, $total - $assignedCount),
            'message' => 'Asignación procesada en segundo plano.',
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

    private function sendAssignmentEmail(Seller $seller, array $assignedParticipationIds, int $assignedCount): void
    {
        if ($assignedCount <= 0 || empty($seller->email)) {
            return;
        }

        $assignedParticipations = Participation::with(['set.reserve.lottery'])
            ->whereIn('id', $assignedParticipationIds)
            ->get();

        $assignmentsBySet = [];
        foreach ($assignedParticipations as $participation) {
            $setId = (int) $participation->set_id;
            if (!isset($assignmentsBySet[$setId])) {
                $set = $participation->set;
                $assignmentsBySet[$setId] = [
                    'set' => $set,
                    'lottery' => $set->reserve->lottery ?? null,
                    'count' => 0,
                ];
            }
            $assignmentsBySet[$setId]['count']++;
        }

        $assignmentsList = [];
        foreach ($assignmentsBySet as $setId => $data) {
            $assignmentsList[] = [
                'set_id' => (int) $setId,
                'count' => (int) ($data['count'] ?? 0),
            ];
        }

        $log = app(CommunicationEmailService::class)->sendAndLog(
            recipientEmail: (string) $seller->email,
            recipientRole: 'vendedor',
            recipientUser: null,
            messageType: 'participation_assignment',
            templateKey: null,
            mailClass: \App\Mail\ParticipationAssignmentMail::class,
            mailPayload: [
                'seller_id' => $seller->id,
                'assignments' => $assignmentsList,
            ],
            context: [
                'seller_id' => $seller->id,
                'assigned_count' => $assignedCount,
            ],
        );

        if ($log->status === EmailCommunicationLog::STATUS_CANCELLED) {
            \Log::error('Error enviando email de asignación de participaciones: ' . ($log->error_message ?? 'unknown'));
        }
    }
}
