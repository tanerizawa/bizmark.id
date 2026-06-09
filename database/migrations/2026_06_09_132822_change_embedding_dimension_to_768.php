<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS kbli_embedding_idx');
        DB::statement('ALTER TABLE kbli DROP COLUMN IF EXISTS embedding');
        DB::statement('ALTER TABLE kbli ADD COLUMN embedding vector(768)');
        DB::statement(
            'CREATE INDEX IF NOT EXISTS kbli_embedding_idx ON kbli USING ivfflat (embedding vector_cosine_ops) WITH (lists = 50)'
        );
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS kbli_embedding_idx');
        DB::statement('ALTER TABLE kbli DROP COLUMN IF EXISTS embedding');
        DB::statement('ALTER TABLE kbli ADD COLUMN embedding vector(1536)');
        DB::statement(
            'CREATE INDEX IF NOT EXISTS kbli_embedding_idx ON kbli USING ivfflat (embedding vector_cosine_ops) WITH (lists = 50)'
        );
    }
};
