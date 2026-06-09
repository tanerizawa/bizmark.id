<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Kbli extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'kbli';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'description',
        'sector',
        'category',
        'activities',
        'examples',
        'complexity_level',
        'default_direct_costs',
        'default_hours_estimate',
        'default_hourly_rates',
        'regulatory_flags',
        'recommended_services',
        'notes',
        'is_active',
        'usage_count',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'default_direct_costs' => 'array',
        'default_hours_estimate' => 'array',
        'default_hourly_rates' => 'array',
        'regulatory_flags' => 'array',
        'recommended_services' => 'array',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Search KBLI by keyword (code, title, or activities)
     */
    /**
     * Search for 5-digit KBLI codes only (Kelompok Kegiatan - most specific level)
     *
     * @param  string  $keyword  Search term
     * @param  int  $limit  Maximum results
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function search(string $keyword, int $limit = 20)
    {
        return self::whereRaw('LENGTH(code) = 5')  // Only 5-digit codes
            ->where('is_active', true)
            ->where(function ($query) use ($keyword) {
                $query->where('code', 'LIKE', "%{$keyword}%")
                    ->orWhere('description', 'LIKE', "%{$keyword}%")
                    ->orWhere('activities', 'LIKE', "%{$keyword}%")
                    ->orWhere('category', 'LIKE', "%{$keyword}%");
            })
            ->orderByRaw('
                CASE 
                    WHEN code LIKE ? THEN 1
                    WHEN description LIKE ? THEN 2
                    ELSE 3
                END
            ', ["{$keyword}%", "{$keyword}%"])
            ->orderBy('usage_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get KBLI by code
     */
    /**
     * Find KBLI by code (preferably 5-digit)
     *
     * @param  string  $code  KBLI code
     * @return self|null
     */
    public static function findByCode(string $code)
    {
        $kbli = self::where('code', $code)
            ->where('is_active', true)
            ->first();

        // Warn if not 5-digit
        if ($kbli && strlen($code) !== 5) {
            Log::warning('KBLI code is not 5-digit', ['code' => $code]);
        }

        return $kbli;
    }

    /**
     * Get KBLI by category
     */
    public static function getByCategory(string $category)
    {
        return self::where('category', $category)
            ->where('is_active', true)
            ->orderBy('usage_count', 'desc')
            ->orderBy('activities')
            ->get();
    }

    /**
     * Get popular KBLIs
     */
    /**
     * Get popular 5-digit KBLI codes (most used)
     *
     * @param  int  $limit  Maximum results
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPopular(int $limit = 10)
    {
        return self::whereRaw('LENGTH(code) = 5')  // Only 5-digit codes
            ->where('is_active', true)
            ->where('usage_count', '>', 0)
            ->orderBy('usage_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Search KBLI codes by semantic similarity using pgvector.
     *
     * @param  string  $query  Business activity description
     * @param  int  $limit  Max results
     * @return \Illuminate\Support\Collection
     */
    public static function similaritySearch(string $query, int $limit = 5)
    {
        $embedding = app(\App\Services\EmbeddingService::class)->embed($query);

        if (empty($embedding)) {
            return self::search($query, $limit);
        }

        $vectorLiteral = \App\Services\EmbeddingService::toVectorLiteral($embedding);

        $rows = \Illuminate\Support\Facades\DB::select("
            SELECT id, code, description, sector, category, activities,
                   1 - (embedding <=> '{$vectorLiteral}'::vector) AS similarity
            FROM kbli
            WHERE embedding IS NOT NULL
              AND LENGTH(code) = 5
              AND is_active = true
            ORDER BY similarity DESC
            LIMIT ?
        ", [$limit]);

        return collect($rows);
    }

    /**
     * Increment usage count
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    /**
     * Calculate estimated cost for this KBLI
     */
    public function calculateEstimate(array $params = [])
    {
        $businessSizeFactor = $params['business_size_factor'] ?? 1.0;
        $selectedDeliverables = $params['deliverables'] ?? [];

        // Base hours
        $hours = $this->default_hours_estimate ?? [
            'admin' => 2,
            'technical' => 8,
            'review' => 4,
        ];

        // Scale by business size
        foreach ($hours as $role => $baseHours) {
            $hours[$role] = $baseHours * $businessSizeFactor;
        }

        // Rates
        $rates = $this->default_hourly_rates ?? [
            'admin' => 100000,
            'technical' => 200000,
            'review' => 150000,
        ];

        // Calculate service fee (biaya jasa)
        $biayaJasa = 0;
        foreach ($hours as $role => $h) {
            $biayaJasa += $h * ($rates[$role] ?? 0);
        }

        // Direct costs (biaya pokok)
        $directCosts = $this->default_direct_costs ?? [];
        $biayaPokok = 0;
        foreach ($directCosts as $item => $cost) {
            $biayaPokok += $cost;
        }

        // Overhead (10%)
        $overhead = ($biayaJasa + $biayaPokok) * 0.10;

        // Total
        $total = $biayaJasa + $biayaPokok + $overhead;

        // Round to nearest 50,000
        $total = round($total / 50000) * 50000;

        return [
            'biaya_jasa' => $biayaJasa,
            'biaya_pokok' => $biayaPokok,
            'overhead' => $overhead,
            'total' => $total,
            'hours_breakdown' => $hours,
            'rates_breakdown' => $rates,
            'direct_costs_breakdown' => $directCosts,
            'confidence' => $this->getConfidenceScore($params),
        ];
    }

    /**
     * Get confidence score for estimate
     */
    private function getConfidenceScore(array $params)
    {
        $score = 0.7; // Base confidence

        // Higher confidence for frequently used KBLIs
        if ($this->usage_count > 10) {
            $score += 0.1;
        }

        // Lower confidence for high complexity
        if ($this->complexity_level === 'high') {
            $score -= 0.2;
        }

        // Higher confidence for low complexity
        if ($this->complexity_level === 'low') {
            $score += 0.1;
        }

        return max(0.3, min(0.95, $score));
    }
}
