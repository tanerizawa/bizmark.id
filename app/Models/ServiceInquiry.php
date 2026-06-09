<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class ServiceInquiry extends Model
{
    protected $fillable = [
        'inquiry_number',
        'email',
        'company_name',
        'company_type',
        'phone',
        'contact_person',
        'position',
        'kbli_code',
        'kbli_description',
        'business_activity',
        'form_data',
        'ai_analysis',
        'ai_model_used',
        'ai_processing_time',
        'ai_tokens_used',
        'analyzed_at',
        'status',
        'priority',
        'source',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'client_id',
        'converted_to_application_id',
        'converted_at',
        'ip_address',
        'user_agent',
        'session_id',
        'last_contacted_at',
        'contacted_by',
        'admin_notes',
    ];

    protected $casts = [
        'form_data' => 'array',
        'ai_analysis' => 'array',
        'analyzed_at' => 'datetime',
        'converted_at' => 'datetime',
        'last_contacted_at' => 'datetime',
    ];

    /**
     * Generate unique inquiry number with DB-level locking to prevent race conditions
     */
    public static function generateInquiryNumber(): string
    {
        return DB::transaction(function () {
            $year = now()->year;
            $lastInquiry = self::whereYear('created_at', $year)
                ->orderBy('id', 'desc')
                ->lockForUpdate()
                ->first();

            $sequence = $lastInquiry ? (int) substr($lastInquiry->inquiry_number, -4) + 1 : 1;

            return sprintf('INQ-%d-%04d', $year, $sequence);
        });
    }

    /**
     * Relationships
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function convertedToApplication(): BelongsTo
    {
        return $this->belongsTo(PermitApplication::class, 'converted_to_application_id');
    }

    public function contactedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contacted_by');
    }

    public function shapefileProject(): HasOne
    {
        return $this->hasOne(ShapefileProject::class);
    }

    /**
     * Scopes
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeAnalyzed($query)
    {
        return $query->where('status', 'analyzed');
    }

    public function scopeError($query)
    {
        return $query->where('status', 'error');
    }

    public function scopeContacted($query)
    {
        return $query->where('status', 'contacted');
    }

    public function scopeQualified($query)
    {
        return $query->where('status', 'qualified');
    }

    public function scopeConverted($query)
    {
        return $query->where('status', 'converted');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Accessors
     */
    public function getEstimatedValueAttribute(): ?int
    {
        if (! $this->ai_analysis || ! isset($this->ai_analysis['total_estimated_cost'])) {
            return null;
        }

        $cost = $this->ai_analysis['total_estimated_cost'];

        // Check grand_total first (AI response format), then fallback to direct min/max
        if (isset($cost['grand_total']['min'], $cost['grand_total']['max'])) {
            return (int) (($cost['grand_total']['min'] + $cost['grand_total']['max']) / 2);
        }

        return isset($cost['min']) && isset($cost['max'])
            ? (int) (($cost['min'] + $cost['max']) / 2)
            : null;
    }

    public function getComplexityScoreAttribute(): ?float
    {
        return $this->ai_analysis['complexity_score'] ?? null;
    }

    /**
     * Mutators
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inquiry) {
            if (! $inquiry->inquiry_number) {
                $inquiry->inquiry_number = self::generateInquiryNumber();
            }
        });
    }
}
