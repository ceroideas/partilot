<?php

namespace App\Jobs;

use App\Http\Controllers\DevolutionsController;
use App\Models\BackgroundTask;
use App\Models\User;
use App\Services\BackgroundTaskService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProcessDevolutionTask implements ShouldQueue
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

        $backgroundTaskService->markRunning($task, 1);

        $payload = (array) ($task->payload ?? []);
        $user = User::find((int) $task->requested_by_user_id);
        if (! $user) {
            throw new \RuntimeException('Usuario solicitante no encontrado para ejecutar la devolución.');
        }

        Auth::login($user);
        try {
            $request = Request::create('/devolutions', 'POST', $payload);
            $request->setUserResolver(fn () => $user);

            $response = app(DevolutionsController::class)->store($request);
            $status = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;
            $data = method_exists($response, 'getData') ? (array) $response->getData(true) : [];

            if ($status >= 400 || !($data['success'] ?? false)) {
                $message = (string) ($data['message'] ?? 'Error procesando devolución en background.');
                throw new \RuntimeException($message);
            }

            $backgroundTaskService->updateProgress($task, 1, 1);
            $backgroundTaskService->complete($task, [
                'devolution_id' => $data['devolution_id'] ?? null,
                'message' => $data['message'] ?? 'Devolución procesada en segundo plano.',
            ]);
        } finally {
            Auth::logout();
        }
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
