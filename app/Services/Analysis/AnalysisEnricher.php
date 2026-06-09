<?php

namespace App\Services\Analysis;

class AnalysisEnricher
{
    public function enrich(array $analysis, array $formData): array
    {
        if (! isset($analysis['recommended_permits']) || ! is_array($analysis['recommended_permits'])) {
            $analysis['recommended_permits'] = [];
        }

        foreach ($analysis['recommended_permits'] as &$permit) {
            $permit['code'] = $permit['code'] ?? 'UNKNOWN';
            $permit['name'] = $permit['name'] ?? 'Izin Tidak Teridentifikasi';
            $permit['priority'] = in_array($permit['priority'] ?? '', ['critical', 'high', 'medium', 'low'])
                ? $permit['priority'] : 'medium';
            $permit['category'] = in_array($permit['category'] ?? '', ['foundational', 'environmental', 'technical', 'operational', 'sectoral'])
                ? $permit['category'] : 'operational';
            $permit['estimated_timeline'] = $permit['estimated_timeline'] ?? '7-14 hari kerja';
            $permit['description'] = $permit['description'] ?? '';
            $permit['prerequisites'] = $permit['prerequisites'] ?? [];
            $permit['triggers_next'] = $permit['triggers_next'] ?? [];

            if (! isset($permit['type'])) {
                $permit['type'] = match ($permit['priority']) {
                    'critical' => 'mandatory',
                    'high' => 'mandatory',
                    'medium' => 'recommended',
                    default => 'conditional',
                };
            }

            if (! isset($permit['government_fee']) || ! is_array($permit['government_fee'])) {
                $permit['government_fee'] = ['min' => 0, 'max' => 0, 'note' => 'Estimasi'];
            }
            if (! isset($permit['consultant_fee']) || ! is_array($permit['consultant_fee'])) {
                $permit['consultant_fee'] = ['min' => 1500000, 'max' => 3000000, 'note' => 'Estimasi'];
            }

            if (empty($permit['total_cost_range'])) {
                $totalMin = ($permit['government_fee']['min'] ?? 0) + ($permit['consultant_fee']['min'] ?? 0);
                $totalMax = ($permit['government_fee']['max'] ?? 0) + ($permit['consultant_fee']['max'] ?? 0);
                $permit['total_cost_range'] = CostFormatter::range($totalMin, $totalMax);
            }
        }
        unset($permit);

        $analysis['recommended_permits'] = $this->sortPermits($analysis['recommended_permits']);

        foreach ($analysis['recommended_permits'] as $idx => &$permit) {
            $permit['order'] = $idx + 1;
        }
        unset($permit);

        if (! isset($analysis['total_estimated_cost']) || ! is_array($analysis['total_estimated_cost'])) {
            $govMin = $govMax = $conMin = $conMax = 0;
            foreach ($analysis['recommended_permits'] as $permit) {
                $govMin += $permit['government_fee']['min'] ?? 0;
                $govMax += $permit['government_fee']['max'] ?? 0;
                $conMin += $permit['consultant_fee']['min'] ?? 0;
                $conMax += $permit['consultant_fee']['max'] ?? 0;
            }
            $analysis['total_estimated_cost'] = [
                'government_fees' => ['min' => $govMin, 'max' => $govMax],
                'consultant_fees' => ['min' => $conMin, 'max' => $conMax],
                'grand_total' => ['min' => $govMin + $conMin, 'max' => $govMax + $conMax],
                'currency' => 'IDR',
            ];
        }

        if (! isset($analysis['total_estimated_cost']['grand_total'])) {
            $govMin = $analysis['total_estimated_cost']['government_fees']['min'] ?? 0;
            $govMax = $analysis['total_estimated_cost']['government_fees']['max'] ?? 0;
            $conMin = $analysis['total_estimated_cost']['consultant_fees']['min'] ?? 0;
            $conMax = $analysis['total_estimated_cost']['consultant_fees']['max'] ?? 0;
            $analysis['total_estimated_cost']['grand_total'] = [
                'min' => $govMin + $conMin,
                'max' => $govMax + $conMax,
            ];
        }

        $analysis['total_estimated_timeline'] = $analysis['total_estimated_timeline'] ?? '14-30 hari kerja';
        $analysis['complexity_score'] = is_numeric($analysis['complexity_score'] ?? null)
            ? min(10, max(1, (float) $analysis['complexity_score'])) : 5.0;
        $analysis['risk_factors'] = $analysis['risk_factors'] ?? [];
        $analysis['next_steps'] = $analysis['next_steps'] ?? [];
        $analysis['required_documents'] = $analysis['required_documents'] ?? [];
        $analysis['risk_classification'] = $analysis['risk_classification'] ?? 'menengah_rendah';
        $analysis['limitations'] = $analysis['limitations'] ?? 'Analisis ini bersifat umum berdasarkan informasi yang diberikan. Untuk analisis detail dengan dokumen checklist lengkap, silakan daftar ke portal BizMark.ID.';

        if (! isset($analysis['risk_assessment'])) {
            $riskLevel = match ($analysis['risk_classification'] ?? 'menengah_rendah') {
                'rendah' => 'low',
                'menengah_rendah' => 'medium',
                'menengah_tinggi' => 'high',
                'tinggi' => 'high',
                default => 'medium',
            };
            $analysis['risk_assessment'] = [
                'level' => $riskLevel,
                'factors' => $analysis['risk_factors'],
                'mitigation' => [
                    'Konsultasikan dengan konsultan perizinan bersertifikat sebelum memulai proses',
                    'Siapkan seluruh dokumen persyaratan secara lengkap sebelum pengajuan',
                    'Pastikan kepatuhan terhadap peraturan daerah (Perda) setempat',
                    'Monitor perubahan regulasi yang mungkin mempengaruhi proses perizinan',
                ],
                'common_pitfalls' => [
                    'Dokumen tidak lengkap saat pengajuan sehingga terjadi penolakan/revisi',
                    'Tidak memperhatikan urutan perolehan izin (dependency chain)',
                    'Menggunakan format izin lama (SIUP/TDP/IMB) yang sudah tidak berlaku',
                    'Tidak memperbarui NIB setelah ada perubahan data usaha',
                ],
            ];
        }

        if (empty($analysis['risk_assessment']['mitigation'])) {
            $analysis['risk_assessment']['mitigation'] = [
                'Konsultasikan dengan konsultan perizinan bersertifikat sebelum memulai proses',
                'Siapkan seluruh dokumen persyaratan secara lengkap sebelum pengajuan',
                'Pastikan kepatuhan terhadap peraturan daerah (Perda) setempat',
                'Monitor perubahan regulasi yang mungkin mempengaruhi proses perizinan',
            ];
        }
        if (empty($analysis['risk_assessment']['common_pitfalls'])) {
            $analysis['risk_assessment']['common_pitfalls'] = [
                'Dokumen tidak lengkap saat pengajuan sehingga terjadi penolakan/revisi',
                'Tidak memperhatikan urutan perolehan izin (dependency chain)',
                'Menggunakan format izin lama (SIUP/TDP/IMB) yang sudah tidak berlaku',
                'Tidak memperbarui NIB setelah ada perubahan data usaha',
            ];
        }

        if (! isset($analysis['estimated_timeline']) || ! is_array($analysis['estimated_timeline'])) {
            $analysis['estimated_timeline'] = [
                'summary' => $analysis['total_estimated_timeline'] ?? '14-30 hari kerja',
            ];
        }

        if (! isset($analysis['estimated_timeline']['minimum_days']) || ! isset($analysis['estimated_timeline']['maximum_days'])) {
            $totalMinDays = 0;
            $totalMaxDays = 0;
            foreach ($analysis['recommended_permits'] as $p) {
                $timeline = $p['estimated_timeline'] ?? '';
                if (preg_match('/(\d+)\s*[-–]\s*(\d+)/', $timeline, $m)) {
                    $totalMinDays += (int) $m[1];
                    $totalMaxDays += (int) $m[2];
                } elseif (preg_match('/(\d+)/', $timeline, $m)) {
                    $totalMinDays += (int) $m[1];
                    $totalMaxDays += (int) $m[1];
                }
            }
            if ($totalMinDays > 0) {
                $analysis['estimated_timeline']['minimum_days'] = $totalMinDays;
                $analysis['estimated_timeline']['maximum_days'] = $totalMaxDays;
                $analysis['estimated_timeline']['summary'] = "{$totalMinDays}-{$totalMaxDays} hari kerja";
                $analysis['total_estimated_timeline'] = $analysis['estimated_timeline']['summary'];
            }
        }

        if (! isset($analysis['estimated_timeline']['critical_path'])) {
            $criticalPath = [];
            foreach ($analysis['recommended_permits'] as $p) {
                if (in_array($p['priority'] ?? '', ['critical', 'high'])) {
                    $criticalPath[] = $p['name'].' ('.($p['estimated_timeline'] ?? '?').')';
                }
            }
            $analysis['estimated_timeline']['critical_path'] = $criticalPath;
        }

        return $analysis;
    }

    private function sortPermits(array $permits): array
    {
        $categoryOrder = [
            'foundational' => 0,
            'environmental' => 1,
            'technical' => 2,
            'operational' => 3,
            'sectoral' => 4,
        ];
        $priorityOrder = [
            'critical' => 0,
            'high' => 1,
            'medium' => 2,
            'low' => 3,
        ];

        usort($permits, function ($a, $b) use ($categoryOrder, $priorityOrder) {
            $catA = $categoryOrder[$a['category'] ?? 'operational'] ?? 3;
            $catB = $categoryOrder[$b['category'] ?? 'operational'] ?? 3;
            if ($catA !== $catB) {
                return $catA - $catB;
            }

            $priA = $priorityOrder[$a['priority'] ?? 'medium'] ?? 2;
            $priB = $priorityOrder[$b['priority'] ?? 'medium'] ?? 2;
            if ($priA !== $priB) {
                return $priA - $priB;
            }

            return 0;
        });

        $permitsByName = [];
        foreach ($permits as $idx => $p) {
            $permitsByName[$p['name']] = $idx;
            if (! empty($p['code'])) {
                $permitsByName[$p['code']] = $idx;
            }
        }

        $sorted = [];
        $visited = [];

        $addPermit = function (int $idx) use (&$addPermit, &$sorted, &$visited, &$permits, &$permitsByName) {
            if (isset($visited[$idx])) {
                return;
            }
            $visited[$idx] = true;

            foreach ($permits[$idx]['prerequisites'] ?? [] as $prereqName) {
                foreach ($permitsByName as $name => $prereqIdx) {
                    if ($prereqIdx !== $idx && (
                        stripos($name, $prereqName) !== false ||
                        stripos($prereqName, $name) !== false
                    )) {
                        $addPermit($prereqIdx);
                        break;
                    }
                }
            }

            $sorted[] = $permits[$idx];
        };

        foreach ($permits as $idx => $p) {
            $addPermit($idx);
        }

        return count($sorted) === count($permits) ? $sorted : $permits;
    }
}
