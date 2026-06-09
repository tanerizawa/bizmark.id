<?php

namespace App\Console\Commands;

use App\Models\Kbli;
use App\Services\EmbeddingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class IndexKbliEmbeddings extends Command
{
    protected $signature = 'kbli:index-embeddings
                            {--fresh : Re-index semua KBLI (bukan hanya yang kosong)}
                            {--limit=0 : Batasi jumlah KBLI yang diproses (0 = semua)}
                            {--dry-run : Tampilkan jumlah yang akan diproses tanpa melakukan embedding}';

    protected $description = 'Generate pgvector embeddings untuk semua KBLI aktif';

    public function handle(EmbeddingService $embedder): int
    {
        $fresh = $this->option('fresh');
        $limit = (int) $this->option('limit');

        $query = Kbli::where('is_active', true);

        if (! $fresh) {
            // Hanya KBLI yang belum punya embedding
            $query->whereRaw('embedding IS NULL');
        }

        if ($limit > 0) {
            $query->limit($limit);
        }

        $total = $query->count();

        if ($this->option('dry-run')) {
            $this->info("Dry-run: {$total} KBLI akan diproses.");

            return Command::SUCCESS;
        }

        if ($total === 0) {
            $this->info('Semua KBLI sudah ter-embed. Gunakan --fresh untuk re-index.');

            return Command::SUCCESS;
        }

        $this->info("Memproses {$total} KBLI embedding...");
        $bar = $this->output->createProgressBar($total);
        $done = 0;
        $fail = 0;

        $query->chunk(50, function ($kblis) use ($embedder, $bar, &$done, &$fail) {
            foreach ($kblis as $kbli) {
                $text = implode(' ', array_filter([
                    $kbli->code,
                    $kbli->title,
                    $kbli->category,
                    $kbli->description,
                    $kbli->activities,
                    $kbli->examples,
                ]));

                $embedding = $embedder->embed($text);

                if (empty($embedding)) {
                    $fail++;
                    $bar->advance();
                    usleep(300_000); // 300ms backoff on failure

                    continue;
                }

                $vectorLiteral = EmbeddingService::toVectorLiteral($embedding);

                DB::statement(
                    'UPDATE kbli SET embedding = ?::vector WHERE id = ?',
                    [$vectorLiteral, $kbli->id]
                );

                $done++;
                $bar->advance();
                usleep(150_000); // 150ms rate limiting — ~6 req/s
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("Selesai: {$done} berhasil, {$fail} gagal.");

        if ($fail > 0) {
            $this->warn('Jalankan ulang perintah ini untuk memproses yang gagal.');
        }

        return Command::SUCCESS;
    }
}
