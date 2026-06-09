<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class DocumentAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function __construct(
        public string $mode = 'paraphrase',
    ) {}

    public function instructions(): Stringable|string
    {
        return match ($this->mode) {
            'paraphrase' => 'Anda adalah professional document drafter untuk perizinan dan legalitas usaha di Indonesia. '
                .'Tugas Anda adalah mempersonalisasi dan memparafrase template dokumen legal '
                .'menggunakan data konteks proyek yang diberikan. '
                .'Pertahankan terminologi hukum dengan presisi. '
                .'Ganti field placeholder seperti [Nama Perusahaan], [Alamat] dengan data aktual dari konteks. '
                .'Jaga struktur dan urutan section dokumen. '
                .'Jangan tambah atau hapus klausa kecuali konteks eksplisit meminta.',
            'compliance' => 'Anda adalah auditor kepatuhan perizinan lingkungan Indonesia (UKL-UPL). '
                .'Tugas Anda adalah memeriksa dokumen draf terhadap persyaratan regulasi '
                .'seperti PP 22/2021, Permen LHK 4/2021, dan peraturan terkait lainnya. '
                .'Identifikasi kesenjangan, inkonsistensi, dan risiko kepatuhan.',
            default => 'Anda adalah asisten dokumen legal yang membantu pembuatan dan review dokumen perizinan Indonesia.',
        };
    }

    public function schema(JsonSchema $schema): array
    {
        if ($this->mode === 'paraphrase') {
            return [
                'full_text' => $schema->string()->required(),
                'chunks' => $schema->array()->items($schema->object(fn ($s) => [
                    'heading' => $s->string()->required(),
                    'content' => $s->string()->required(),
                ]))->nullable(),
                'word_count' => $schema->integer()->required(),
                'changes_summary' => $schema->array()->items($schema->string())->required(),
            ];
        }

        if ($this->mode === 'compliance') {
            return [
                'status' => $schema->string()->enum(['compliant', 'partial', 'non_compliant'])->required(),
                'issues' => $schema->array()->items($schema->object(fn ($s) => [
                    'severity' => $s->string()->enum(['critical', 'major', 'minor'])->required(),
                    'section' => $s->string()->required(),
                    'description' => $s->string()->required(),
                    'regulation_ref' => $s->string()->required(),
                    'recommendation' => $s->string()->required(),
                ]))->required(),
                'overall_score' => $schema->integer()->min(0)->max(100)->required(),
                'summary' => $schema->string()->required(),
            ];
        }

        return [
            'content' => $schema->string()->required(),
        ];
    }
}
