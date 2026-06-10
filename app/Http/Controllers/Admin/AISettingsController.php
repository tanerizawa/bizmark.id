<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AISetting;
use App\Models\AISettingHistory;
use App\Services\AiRuntimeConfigService;
use App\Services\AISettingService;
use Illuminate\Http\Request;

class AISettingsController extends Controller
{
    public function __construct(
        private AiRuntimeConfigService $aiRuntime,
    ) {}

    /**
     * Show AI settings by category
     */
    public function index(Request $request)
    {
        $category = $request->get('category', 'pricing');

        $settings = AISetting::where('category', $category)
            ->orderBy('display_order')
            ->orderBy('key')
            ->get()
            ->groupBy('group_name');

        $categories = AISetting::select('category')
            ->distinct()
            ->pluck('category')
            ->toArray();

        return view('admin.ai-settings.index', compact('settings', 'category', 'categories'));
    }

    /**
     * Update AI settings
     */
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'settings' => 'required|array',
                'settings.*' => 'required',
            ]);

            $updatedCount = 0;

            foreach ($validated['settings'] as $key => $value) {
                $setting = AISetting::where('key', $key)->first();

                if (! $setting) {
                    continue;
                }

                if (! $this->validateValue($value, $setting->data_type, $setting->validation_rules)) {
                    return back()->withErrors(['settings.'.$key => "Invalid value for {$key}"]);
                }

                $reason = 'Updated via admin panel by '.auth()->user()->name;

                if ($setting->category === 'provider') {
                    $this->aiRuntime->applyProviderChange($key, $value, $reason);
                } else {
                    AISettingService::set($key, $value, $reason);
                }

                $updatedCount++;
            }

            return back()->with('success', "Successfully updated {$updatedCount} settings");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update settings: '.$e->getMessage()]);
        }
    }

    /**
     * Reset setting to default
     */
    public function reset(Request $request, string $key)
    {
        try {
            AISettingService::reset($key);

            return back()->with('success', "Setting '{$key}' has been reset to default value");

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to reset setting: '.$e->getMessage()]);
        }
    }

    /**
     * Show change history for a setting
     */
    public function history(Request $request, string $key)
    {
        $setting = AISetting::where('key', $key)->firstOrFail();

        $history = $setting->history()
            ->with('user')
            ->paginate(20);

        return view('admin.ai-settings.history', compact('setting', 'history'));
    }

    /**
     * Show all recent changes
     */
    public function recentChanges(Request $request)
    {
        $days = $request->get('days', 7);

        $changes = AISettingHistory::recent($days)
            ->with(['setting', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.ai-settings.recent-changes', compact('changes', 'days'));
    }

    /**
     * Clear all cache
     */
    public function clearCache()
    {
        try {
            AISettingService::clearAllCache();

            return back()->with('success', 'All AI settings cache has been cleared');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to clear cache: '.$e->getMessage()]);
        }
    }

    public function testProvider(Request $request, string $provider)
    {
        $validProviders = array_keys(config('ai.providers', []));
        if (! in_array($provider, $validProviders)) {
            return response()->json([
                'success' => false,
                'error' => "Unknown provider: {$provider}",
            ], 404);
        }

        $result = $this->aiRuntime->testProviderConnection($provider);

        return response()->json($result);
    }

    public function sdkStatus()
    {
        $status = $this->aiRuntime->getSdkStatus();

        return view('admin.ai-settings.sdk-status', compact('status'));
    }

    /**
     * Validate value based on data type and rules
     */
    private function validateValue($value, string $dataType, ?array $rules = null): bool
    {
        // Type validation
        switch ($dataType) {
            case 'number':
                if (! is_numeric($value)) {
                    return false;
                }
                $value = floatval($value);
                break;

            case 'boolean':
                if (! in_array($value, [0, 1, '0', '1', true, false, 'true', 'false'], true)) {
                    return false;
                }
                break;

            case 'json':
            case 'array':
                if (is_string($value)) {
                    json_decode($value);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        return false;
                    }
                }
                break;
        }

        // Custom validation rules
        if (! empty($rules)) {
            if (isset($rules['min']) && $value < $rules['min']) {
                return false;
            }
            if (isset($rules['max']) && $value > $rules['max']) {
                return false;
            }
            if (isset($rules['pattern']) && ! preg_match($rules['pattern'], $value)) {
                return false;
            }
        }

        return true;
    }
}
