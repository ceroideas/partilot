<?php

namespace App\Jobs;

use App\Http\Controllers\DesignController;
use App\Models\DesignFormat;
use App\Support\GeneratedPdfCatalog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateCoverBackPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $designId;

    protected string $jobId;

    public function __construct(int $designId, string $jobId)
    {
        $this->designId = $designId;
        $this->jobId = $jobId;
    }

    public function handle(): void
    {
        ini_set('max_execution_time', '0');
        ini_set('memory_limit', '1024M');

        $design = DesignFormat::findOrFail($this->designId);

        $controller = app(DesignController::class);
        try {
            $pdf = $controller->makeCoverBackPdfFacade($design);
        } catch (\InvalidArgumentException|\RuntimeException $e) {
            \Log::error('GenerateCoverBackPdfJob falló para diseño '.$this->designId.': '.$e->getMessage());
            throw $e;
        }

        Storage::makeDirectory('generated_pdfs');
        $final_path = storage_path('app/generated_pdfs/'.$this->jobId.'.pdf');
        $pdf->save($final_path);

        GeneratedPdfCatalog::writeMeta(
            $this->jobId,
            'portada-trasera-diseno-'.$this->designId.'.pdf',
            $this->designId
        );
    }
}
