<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class AISetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ai_settings';

    protected $fillable = [
        'category',
        'key',
        'value',
        'data_type',
        'description',
        'is_public',
        'is_encrypted',
        'validation_rules',
        'default_value',
        'display_order',
        'group_name',
        'requires_restart',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'validation_rules' => 'array',
        'is_public' => 'boolean',
        'is_encrypted' => 'boolean',
        'requires_restart' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get change history for this setting
     */
    public function history()
    {
        return $this->hasMany(AISettingHistory::class, 'setting_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get decrypted/typed value
     */
    public function getValueAttribute($value)
    {
        // Decrypt if needed
        if ($this->attributes['is_encrypted'] ?? false) {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }

        // Type casting
        return match ($this->attributes['data_type'] ?? 'string') {
            'json', 'array' => json_decode($value, true),
            'number' => (float) $value,
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            default => $value,
        };
    }

    /**
     * Set encrypted/typed value
     */
    public function setValueAttribute($value): void
    {
        // Type conversion to string
        $stringValue = match ($this->attributes['data_type'] ?? 'string') {
            'json', 'array' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };

        // Encrypt if needed
        if ($this->attributes['is_encrypted'] ?? false) {
            $stringValue = Crypt::encryptString($stringValue);
        }

        $this->attributes['value'] = $stringValue;
    }

    /**
     * Get display value (masked for encrypted)
     */
    public function getDisplayValueAttribute(): string
    {
        if ($this->is_encrypted) {
            return '••••••••';
        }

        return is_array($this->value)
            ? json_encode($this->value, JSON_PRETTY_PRINT)
            : (string) $this->value;
    }

    /**
     * Scope by category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
