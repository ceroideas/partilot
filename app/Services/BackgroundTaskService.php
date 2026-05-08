<?php

namespace App\Services;

use App\Models\BackgroundTask;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class BackgroundTaskService
{
    public function createTask(User $user, array $data): BackgroundTask
    {
        $type = (string) ($data['type'] ?? '');
        if (!in_array($type, BackgroundTask::supportedTypes(), true)) {
            throw new \InvalidArgumentException('Tipo de tarea no soportado.');
        }
        $payload = $this->normalizePayload((array) ($data['payload'] ?? []));
        $resourceKey = $this->normalizeString($data['resource_key'] ?? null);
        $hash = $this->buildTaskHash($type, $resourceKey, $payload);

        $existing = $this->findActiveByHash($user->id, $hash);
        if ($existing) {
            return $existing;
        }

        if ($resourceKey !== null) {
            $activeForResource = BackgroundTask::query()
                ->where('resource_key', $resourceKey)
                ->whereIn('status', [BackgroundTask::STATUS_PENDING, BackgroundTask::STATUS_RUNNING])
                ->latest('id')
                ->first();
            if ($activeForResource) {
                return $activeForResource;
            }
        }

        return BackgroundTask::query()->create([
            'uuid' => (string) Str::uuid(),
            'type' => $type,
            'status' => BackgroundTask::STATUS_PENDING,
            'requested_by_user_id' => $user->id,
            'entity_id' => Arr::get($data, 'entity_id'),
            'administration_id' => Arr::get($data, 'administration_id'),
            'set_id' => Arr::get($data, 'set_id'),
            'resource_key' => $resourceKey,
            'task_hash' => $hash,
            'payload' => $payload,
            'progress_total' => 0,
            'progress_done' => 0,
            'progress_percent' => 0,
        ]);
    }

    public function markRunning(BackgroundTask $task, int $total = 0): void
    {
        $task->update([
            'status' => BackgroundTask::STATUS_RUNNING,
            'started_at' => $task->started_at ?: now(),
            'progress_total' => max(0, $total),
            'progress_done' => 0,
            'progress_percent' => 0,
            'error_message' => null,
        ]);
    }

    public function updateProgress(BackgroundTask $task, int $done, ?int $total = null): void
    {
        $totalValue = $total !== null ? max(0, $total) : (int) $task->progress_total;
        $doneValue = max(0, $done);
        if ($totalValue > 0) {
            $doneValue = min($doneValue, $totalValue);
        }
        $percent = $totalValue > 0 ? (int) floor(($doneValue * 100) / $totalValue) : 0;

        $task->update([
            'progress_total' => $totalValue,
            'progress_done' => $doneValue,
            'progress_percent' => max(0, min(100, $percent)),
        ]);
    }

    public function complete(BackgroundTask $task, array $summary = []): void
    {
        $task->update([
            'status' => BackgroundTask::STATUS_COMPLETED,
            'result_summary' => $summary,
            'progress_percent' => 100,
            'finished_at' => now(),
            'error_message' => null,
        ]);
    }

    public function fail(BackgroundTask $task, string $message, array $summary = []): void
    {
        $task->update([
            'status' => BackgroundTask::STATUS_FAILED,
            'error_message' => Str::limit(trim($message), 4000, ''),
            'result_summary' => $summary ?: $task->result_summary,
            'finished_at' => now(),
        ]);
    }

    public function cancel(BackgroundTask $task): void
    {
        $task->update([
            'status' => BackgroundTask::STATUS_CANCELLED,
            'finished_at' => now(),
        ]);
    }

    private function findActiveByHash(int $userId, string $hash): ?BackgroundTask
    {
        return BackgroundTask::query()
            ->where('requested_by_user_id', $userId)
            ->where('task_hash', $hash)
            ->whereIn('status', [BackgroundTask::STATUS_PENDING, BackgroundTask::STATUS_RUNNING])
            ->orderByDesc('id')
            ->first();
    }

    private function buildTaskHash(string $type, ?string $resourceKey, array $payload): string
    {
        return hash('sha256', implode('|', [
            $type,
            (string) ($resourceKey ?? ''),
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]));
    }

    private function normalizePayload(array $payload): array
    {
        ksort($payload);
        return $payload;
    }

    private function normalizeString($value): ?string
    {
        $string = trim((string) $value);
        return $string === '' ? null : $string;
    }
}

