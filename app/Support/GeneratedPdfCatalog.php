<?php

namespace App\Support;

class GeneratedPdfCatalog
{
    public static function metaPath(string $jobId): string
    {
        return storage_path('app/generated_pdfs/'.$jobId.'.meta.json');
    }

    public static function writeMeta(string $jobId, string $downloadName, ?int $designFormatId = null): void
    {
        $dir = storage_path('app/generated_pdfs');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $payload = [
            'download_name' => $downloadName,
            'design_format_id' => $designFormatId,
        ];
        file_put_contents(static::metaPath($jobId), json_encode(
            array_filter($payload, static fn ($v) => $v !== null),
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
        ));
    }

    /** @return array{download_name: string, design_format_id?: int}|null */
    public static function readMeta(string $jobId): ?array
    {
        $path = static::metaPath($jobId);
        if (! is_file($path)) {
            return null;
        }
        try {
            $data = json_decode((string) file_get_contents($path), true);
        } catch (\Throwable $e) {
            return null;
        }
        if (! is_array($data) || empty($data['download_name'])) {
            return null;
        }
        $out = [
            'download_name' => (string) $data['download_name'],
        ];
        if (isset($data['design_format_id']) && is_numeric($data['design_format_id'])) {
            $out['design_format_id'] = (int) $data['design_format_id'];
        }

        return $out;
    }

    public static function readDownloadName(string $jobId): ?string
    {
        $meta = static::readMeta($jobId);

        return $meta['download_name'] ?? null;
    }

    /** @deprecated Usar readMeta */
    public static function readDesignFormatId(string $jobId): ?int
    {
        $meta = static::readMeta($jobId);

        return $meta['design_format_id'] ?? null;
    }

    public static function deleteMeta(string $jobId): void
    {
        $p = static::metaPath($jobId);
        if (is_file($p)) {
            @unlink($p);
        }
    }
}
