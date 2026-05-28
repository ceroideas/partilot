<?php

use App\Support\HtmlText;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const COLUMNS = ['name', 'city', 'address', 'comments', 'province'];

    public function up(): void
    {
        DB::table('entities')
            ->orderBy('id')
            ->chunkById(100, function ($rows) {
                foreach ($rows as $row) {
                    $updates = [];

                    foreach (self::COLUMNS as $column) {
                        $raw = $row->{$column} ?? null;
                        if ($raw === null || $raw === '' || ! str_contains($raw, '&')) {
                            continue;
                        }

                        $decoded = HtmlText::decode($raw);
                        if ($decoded !== $raw) {
                            $updates[$column] = $decoded;
                        }
                    }

                    if ($updates !== []) {
                        DB::table('entities')->where('id', $row->id)->update($updates);
                    }
                }
            });
    }

    public function down(): void
    {
        // No reversible de forma segura.
    }
};
