<?php

namespace App\Http\Controllers\Api;

use App\Ai\Agents\DocumentAgent;
use App\Ai\Agents\PerizinanAgent;
use App\Ai\Agents\SeoAgent;
use App\Http\Controllers\Controller;
use Illuminate\Broadcasting\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiAgentController extends Controller
{
    public function perizinan(Request $request)
    {
        $validated = $request->validate([
            'business_activity' => 'required|string|max:500',
            'kbli_code' => 'nullable|string|max:10',
            'kbli_description' => 'nullable|string|max:255',
            'business_scale' => 'nullable|string|in:micro,small,medium,large',
            'location_province' => 'nullable|string|max:100',
            'location_category' => 'nullable|string|in:industrial,commercial,residential,rural',
            'tier' => 'nullable|string|in:free,premium',
        ]);

        $tier = $validated['tier'] ?? 'free';

        $prompt = $this->buildPerizinanPrompt($validated);

        $agent = new PerizinanAgent(tier: $tier);

        $userId = Auth::id();

        return $agent->broadcast($prompt, new Channel('agent.perizinan.'.$userId))
            ->usingVercelDataProtocol();
    }

    public function paraphrase(Request $request)
    {
        $validated = $request->validate([
            'template_text' => 'required|string',
            'context' => 'required|array',
            'tier' => 'nullable|string|in:free,premium',
        ]);

        $contextJson = json_encode($validated['context']);
        $prompt = <<<PROMPT
Parafrase dan personalisasi dokumen legal berikut menggunakan data konteks proyek yang diberikan.

Dokumen Template:
```
{$validated['template_text']}
```

Konteks Proyek:
```json
{$contextJson}
```

Hasilkan output dalam format JSON yang diminta.
PROMPT;

        $agent = new DocumentAgent(mode: 'paraphrase');

        return $agent->stream($prompt, timeout: 120)
            ->usingVercelDataProtocol();
    }

    public function optimizeSeo(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100',
            'language' => 'nullable|string|in:id,en',
            'current_meta_title' => 'nullable|string',
            'current_meta_description' => 'nullable|string',
        ]);

        $contentPreview = strip_tags(substr($validated['content'], 0, 500));
        $lang = $validated['language'] ?? 'id';
        $currentTitle = $validated['current_meta_title'] ?? '(kosong)';
        $currentDesc = $validated['current_meta_description'] ?? '(kosong)';
        $category = $validated['category'] ?? 'Umum';

        $prompt = <<<PROMPT
Optimasi SEO meta tags untuk artikel ini:

Judul: {$validated['title']}
Meta Title saat ini: {$currentTitle}
Meta Description saat ini: {$currentDesc}
Kategori: {$category}
Bahasa: {$lang}
Preview konten: {$contentPreview}

Hasilkan output JSON dengan meta_title, meta_description, meta_keywords, improved_title, dan excerpt.
Semua dalam Bahasa Indonesia yang natural.
PROMPT;

        $agent = new SeoAgent();

        return $agent->stream($prompt, timeout: 120)
            ->usingVercelDataProtocol();
    }

    protected function buildPerizinanPrompt(array $data): string
    {
        $kbli = $data['kbli_code'] ?? null;
        $kbliDesc = $data['kbli_description'] ?? '';

        $kbliLine = $kbli
            ? "- Kode KBLI: {$kbli}" . ($kbliDesc ? " ({$kbliDesc})" : '')
            : '- Kode KBLI: Belum ditentukan (sarankan kode yang paling sesuai)';

        $scaleMap = [
            'micro' => 'Mikro (< 10 karyawan)',
            'small' => 'Kecil (10-50 karyawan)',
            'medium' => 'Menengah (50-100 karyawan)',
            'large' => 'Besar (> 100 karyawan)',
        ];

        $locMap = [
            'industrial' => 'Kawasan Industri',
            'commercial' => 'Area Komersial',
            'residential' => 'Area Residensial',
            'rural' => 'Pedesaan',
        ];

        $scale = $scaleMap[$data['business_scale'] ?? ''] ?? 'Tidak disebutkan';
        $loc = $locMap[$data['location_category'] ?? ''] ?? 'Tidak disebutkan';
        $province = $data['location_province'] ?? 'Tidak disebutkan';

        return <<<PROMPT
Analisis kebutuhan perizinan untuk usaha berikut:

PROFIL USAHA:
- Aktivitas Bisnis: {$data['business_activity']}
{$kbliLine}
- Skala Usaha: {$scale}
- Lokasi: {$province} ({$loc})

Berikan rekomendasi perizinan yang SPESIFIK dan AKURAT untuk jenis usaha ini dalam format JSON yang diminta.
PROMPT;
    }
}
