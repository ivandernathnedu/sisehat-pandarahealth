<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\HealthScore;
use App\Models\Factor;
use App\Models\Question;
use App\Models\Response;
use App\Models\FactorScore;
use App\Models\Recommendation;
use App\Models\AssessmentRecommendation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HealthService
{
    /**
     * Menghitung Health Score Lengkap untuk sebuah Assessment (DSS Logic)
     */
    public function calculate($assessmentId)
    {
        $factors = Factor::with('questions')->get();
        
        $totalNormalizedScore = 0;
        $validFactorsCount = 0;

        // 1. Buat Health Score (kosong dulu)
        $healthScore = HealthScore::updateOrCreate(
            ['assessment_id' => $assessmentId],
            [
                'overall_score' => 0,
                'category' => 'Pending',
                'calculated_at' => now()
            ]
        );

        // 2. Simpan Factor Scores dan Rekomendasi
        foreach ($factors as $factor) {
            $normalizedQuestionScores = [];

            foreach ($factor->questions as $question) {
                $ownerScore = $this->getOwnerScore($assessmentId, $question->question_id);
                $employeeScore = $this->getMeanEmployeeScore($assessmentId, $question->question_id);

                $qRawScore = null;
                // Logic 50:50 penggabungan Owner & Employee
                if ($ownerScore !== null && $employeeScore !== null) {
                    $qRawScore = ($ownerScore + $employeeScore) / 2;
                } elseif ($ownerScore !== null) {
                    $qRawScore = $ownerScore;
                } elseif ($employeeScore !== null) {
                    $qRawScore = $employeeScore;
                }

                if ($qRawScore !== null) {
                    $max = $question->max_score ?? 5;
                    $normScore = ($qRawScore / $max) * 100;
                    $normalizedQuestionScores[] = min(100, $normScore);
                }
            }

            if (count($normalizedQuestionScores) > 0) {
                $avgNormalizedScore = array_sum($normalizedQuestionScores) / count($normalizedQuestionScores);
                
                // Konversi kembali ke skala 5 untuk penentuan kategori agar konsisten dengan rule DSS
                $category = $this->determineCategory(($avgNormalizedScore / 100) * 5);

                FactorScore::updateOrCreate(
                    ['health_score_id' => $healthScore->health_score_id, 'factor_id' => $factor->factor_id],
                    ['score' => $avgNormalizedScore]
                );

                $this->generateRecommendation($assessmentId, $factor->factor_id, $category);

                $totalNormalizedScore += $avgNormalizedScore;
                $validFactorsCount++;
            }
        }

        // Tentukan Kategori Overall
        $overallNormalizedScore = $validFactorsCount > 0 ? $totalNormalizedScore / $validFactorsCount : 0;
        $overallRawScore = ($overallNormalizedScore / 100) * 5;
        $overallCategory = $this->determineCategory($overallRawScore);

        $healthScore->update([
            'overall_score' => $overallNormalizedScore,
            'category' => $overallCategory,
            'calculated_at' => now()
        ]);

        // Otomatis kirim data hasil asesmen & rekomendasi ke n8n Webhook
        $this->sendToN8n($assessmentId, $healthScore);

        return $healthScore;
    }

    /**
     * Mencari rekomendasi yang cocok dari Knowledge Base dan menyimpannya sebagai Snapshot
     */
    private function generateRecommendation($assessmentId, $factorId, $category)
    {
        // Cari rekomendasi dari knowledge base berdasarkan faktor dan level kategori
        $recommendation = Recommendation::where('factor_id', $factorId)
            ->where('category_level', $category)
            ->first();

        if ($recommendation) {
            // Simpan snapshot rekomendasi untuk history assessment ini
            AssessmentRecommendation::updateOrCreate(
                ['assessment_id' => $assessmentId, 'recommendation_id' => $recommendation->recommendation_id ?? $recommendation->id],
                ['generated_at' => now()]
            );
        }
    }

    private function getOwnerScore($assessmentId, $questionId)
    {
        $response = Response::where('assessment_id', $assessmentId)
            ->where('question_id', $questionId)
            ->where('respondent_type', 'owner')
            ->first();
        return $response ? (float) $response->answer_value : null;
    }

    private function getMeanEmployeeScore($assessmentId, $questionId)
    {
        $mean = Response::where('assessment_id', $assessmentId)
            ->where('question_id', $questionId)
            ->where('respondent_type', 'employee')
            ->avg('answer_value');
        return $mean !== null ? (float) $mean : null;
    }

    public function determineCategory($score)
    {
        if ($score <= 1.25) return 'KURANG_SEHAT';
        if ($score <= 2.50) return 'CUKUP_SEHAT';
        if ($score <= 3.75) return 'SEHAT';
        return 'SANGAT_SEHAT';
    }

    /**
     * Mengirimkan payload hasil kalkulasi & rekomendasi ke webhook n8n
     */
    public function sendToN8n($assessmentId, $healthScore = null)
    {
        $webhookUrl = config('services.n8n.webhook_url') ?: env('N8N_WEBHOOK_URL', 'http://localhost:5678/webhook/sisehat-notification');

        if (empty($webhookUrl)) {
            Log::warning("n8n Webhook URL belum terkonfigurasi di .env (N8N_WEBHOOK_URL).");
            return [
                'success' => false,
                'message' => 'N8N_WEBHOOK_URL tidak ditemukan.',
            ];
        }

        $assessment = Assessment::with(['umkm.owner'])->find($assessmentId);
        if (!$assessment) {
            return [
                'success' => false,
                'message' => 'Assessment tidak ditemukan.',
            ];
        }

        if (!$healthScore) {
            $healthScore = HealthScore::where('assessment_id', $assessmentId)->first();
        }

        // Ambil rincian skor per faktor
        $factorScores = FactorScore::with('factor')
            ->where('health_score_id', $healthScore?->health_score_id)
            ->get()
            ->map(function ($fs) {
                return [
                    'factor_id'   => $fs->factor_id,
                    'nama_factor' => $fs->factor?->nama_factor ?? 'N/A',
                    'score'       => round((float) $fs->score, 2),
                    'score_scale_5' => round(((float)$fs->score / 100) * 5, 2),
                ];
            });

        // Mapping 6 Faktor ke variabel bahasa Indonesia (skala 1-5 untuk kompatibilitas Code Node n8n)
        $factorKeyMap = [
            'institutional resources'  => 'sumber_daya',
            'leader involvement'       => 'kepemimpinan',
            'work environment quality' => 'lingkungan_kerja',
            'organizational values'    => 'nilai_organisasi',
            'operational stability'    => 'stabilitas',
            'economics performance'    => 'kinerja',
        ];

        $factorMapValues = [
            'sumber_daya'      => 0,
            'kepemimpinan'     => 0,
            'lingkungan_kerja' => 0,
            'nilai_organisasi' => 0,
            'stabilitas'       => 0,
            'kinerja'          => 0,
        ];

        foreach ($factorScores as $fs) {
            $norm = strtolower(trim($fs['nama_factor']));
            $key = $factorKeyMap[$norm] ?? null;
            if ($key) {
                $factorMapValues[$key] = $fs['score_scale_5'];
            }
        }

        // Ambil snapshot rekomendasi yang sudah di-generate
        $recommendations = AssessmentRecommendation::with('recommendation.factor')
            ->where('assessment_id', $assessmentId)
            ->get()
            ->map(function ($ar) {
                return [
                    'factor'              => $ar->recommendation?->factor?->nama_factor ?? 'N/A',
                    'category_level'      => $ar->recommendation?->category_level ?? 'N/A',
                    'recommendation_text' => $ar->recommendation?->recommendation_text ?? '',
                ];
            });

        $payload = [
            'event'           => 'assessment_calculated',
            'timestamp'       => now()->toIso8601String(),
            'assessment_id'   => $assessment->assessment_id,
            'assessment_name' => $assessment->assessment_name,
            'umkm' => [
                'umkm_id'      => $assessment->umkm?->umkm_id ?? 'N/A',
                'nama_umkm'    => $assessment->umkm?->nama_umkm ?? 'Vander Social Space',
                'sektor_usaha' => $assessment->umkm?->sektor_usaha ?? 'Food and Beverage',
                'alamat'       => $assessment->umkm?->alamat ?? '-',
            ],
            'owner' => [
                'owner_id' => $assessment->umkm?->owner?->owner_id,
                'name'     => $assessment->umkm?->owner?->name ?? 'Ivander Owner',
                'email'    => $assessment->umkm?->owner?->email ?? '',
                'gender'   => $assessment->umkm?->owner?->gender ?? 'laki-laki',
            ],
            'health_score' => [
                'overall_score' => round((float) ($healthScore?->overall_score ?? 0), 2),
                'category'      => $healthScore?->category ?? 'SEHAT',
                'calculated_at' => optional($healthScore?->calculated_at)->toIso8601String() ?? now()->toIso8601String(),
            ],
            // 6 Faktor langsung di root payload (Skala 1 - 5 untuk Code Node n8n)
            'sumber_daya'      => $factorMapValues['sumber_daya'],
            'kepemimpinan'     => $factorMapValues['kepemimpinan'],
            'lingkungan_kerja' => $factorMapValues['lingkungan_kerja'],
            'nilai_organisasi' => $factorMapValues['nilai_organisasi'],
            'stabilitas'       => $factorMapValues['stabilitas'],
            'kinerja'          => $factorMapValues['kinerja'],
            'total_skor'       => round(array_sum($factorMapValues), 2),

            'factor_scores'   => $factorScores,
            'recommendations' => $recommendations,
        ];

        // Prioritaskan endpoint test agar langsung muncul di kanvas n8n jika sedang listening
        $urlsToTry = array_unique([
            str_replace('/webhook/', '/webhook-test/', $webhookUrl),
            str_replace('/webhook-test/', '/webhook/', $webhookUrl),
            $webhookUrl,
        ]);

        $lastError = null;
        foreach ($urlsToTry as $url) {
            try {
                $response = Http::timeout(5)->post($url, $payload);
                if ($response->successful()) {
                    Log::info("Webhook n8n terkirim sukses: {$url} [HTTP {$response->status()}]");
                    return [
                        'success' => true,
                        'status'  => $response->status(),
                        'url'     => $url,
                        'payload' => $payload,
                    ];
                }
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
            }
        }

        Log::error("Gagal mengirim webhook ke n8n setelah mencoba URL: " . implode(', ', $urlsToTry) . " - Error: {$lastError}");
        return [
            'success' => false,
            'error'   => $lastError,
            'payload' => $payload,
        ];
    }
}
