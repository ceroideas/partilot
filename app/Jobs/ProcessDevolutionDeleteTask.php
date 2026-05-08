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
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class ProcessDevolutionDeleteTask implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(private readonly string $taskUuid)
    {
    }

    public function handle(BackgroundTaskService $backgroundTaskService): void
    {
        $task = BackgroundTask::query()->where('uuid', $this->taskUuid)->first();
        if (! $task) {
            return;
        }

        $backgroundTaskService->markRunning($task, 1);

        $payload = (array) ($task->payload ?? []);
        $devolutionId = (string) ($payload['devolution_id'] ?? '');
        if ($devolutionId === '') {
            throw new \RuntimeException('Payload inválido: falta devolution_id.');
        }

        $user = User::find((int) $task->requested_by_user_id);
        if (! $user) {
            throw new \RuntimeException('Usuario solicitante no encontrado.');
        }

        Auth::login($user);
        try {
            $uri = '/devolutions/'.$devolutionId.'?force_sync=1';
            $request = Request::create($uri, 'DELETE');
            $request->setUserResolver(fn () => $user);

            $response = app(DevolutionsController::class)->destroy($request, $devolutionId);
            $status = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;
            $data = method_exists($response, 'getData') ? (array) $response->getData(true) : [];

            if ($status >= 400 || ! ($data['success'] ?? false)) {
                $message = (string) ($data['message'] ?? 'Error al eliminar la devolución en segundo plano.');
                throw new \RuntimeException($message);
            }

            $backgroundTaskService->updateProgress($task, 1, 1);
            $backgroundTaskService->complete($task, [
                'message' => $data['message'] ?? 'Devolución eliminada correctamente.',
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
