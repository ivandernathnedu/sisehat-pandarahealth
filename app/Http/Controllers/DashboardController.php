<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\HealthScore;
use App\Models\FactorScore;
use App\Models\AssessmentRecommendation;
use App\Models\Umkm;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Mengambil health score terbaru untuk UMKM
     */
    public function getLatestScore($umkmId)
    {
        $latestAssessment = Assessment::where('umkm_id', $umkmId)
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestAssessment) {
            return response()->json(['message' => 'Belum ada data assessment.'], 404);
        }

        $healthScore = HealthScore::where('assessment_id', $latestAssessment->assessment_id)->first();

        return response()->json([
            'assessment' => $latestAssessment,
            'score' => $healthScore
        ]);
    }

    /**
     * Mengambil analisis mendalam (Radar Chart, Rekomendasi DSS)
     */
    public function getDetailedAnalysis($assessmentId)
    {
        $assessment = Assessment::with('umkm')->find($assessmentId);
        if (!$assessment) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json($this->getDetailedData($assessmentId));
    }

    /**
     * Logic inti untuk mendapatkan data detail analisis
     */
    private function getDetailedData($assessmentId)
    {
        $assessment = Assessment::with('umkm')->find($assessmentId);
        if (!$assessment) {
            return null;
        }

        // 1. Ambil Skor & Kategori Utama (Hitung live jika belum ada atau masih in_progress)
        $healthScore = HealthScore::where('assessment_id', $assessmentId)->first();
        if (!$healthScore || $assessment->status === 'in_progress') {
            $healthService = app(\App\Services\HealthService::class);
            $healthScore = $healthService->calculate($assessmentId);
        }
        
        // 2. Ambil Skor per Faktor dari tabel factor_scores
        $factorScores = $healthScore 
            ? FactorScore::with('factor')->where('health_score_id', $healthScore->health_score_id)->get()
            : collect();

        // 5. Trend Analysis (Perkembangan Faktor dibanding assessment sebelumnya)
        $previousAssessment = Assessment::where('umkm_id', $assessment->umkm_id)
            ->where('assessment_id', '<', $assessmentId)
            ->whereIn('status', ['open', 'closed', 'completed']) // Mengakomodasi status yang ada di database
            ->orderBy('created_at', 'desc')
            ->first();

        $prevHealthScore = $previousAssessment ? HealthScore::where('assessment_id', $previousAssessment->assessment_id)->first() : null;
        $prevScores = $prevHealthScore 
            ? FactorScore::where('health_score_id', $prevHealthScore->health_score_id)->pluck('score', 'factor_id')
            : collect();

        // 6. Industry Benchmark
        $industry = $assessment->umkm->sektor_usaha ?? null;
        $industryAvgScores = collect();
        $peerCount = 0;
        
        if ($industry) {
            $industryAvgScores = DB::table('factor_scores as fs')
                ->join('health_scores as hs', 'fs.health_score_id', '=', 'hs.health_score_id')
                ->join('assessments as a', 'hs.assessment_id', '=', 'a.assessment_id')
                ->join('umkms as u', 'a.umkm_id', '=', 'u.umkm_id')
                ->where('u.sektor_usaha', $industry)
                ->whereIn('a.status', ['open', 'closed', 'completed'])
                ->groupBy('fs.factor_id')
                ->select('fs.factor_id', DB::raw('AVG(fs.score) as avg_score'))
                ->pluck('avg_score', 'fs.factor_id');

            $peerCount = Assessment::join('umkms', 'assessments.umkm_id', '=', 'umkms.umkm_id')
                ->join('health_scores as hs', 'assessments.assessment_id', '=', 'hs.assessment_id')
                ->where('umkms.sektor_usaha', $industry)
                ->whereIn('assessments.status', ['open', 'closed', 'completed'])
                ->count();
        }

        $radarData = $factorScores->map(function($fs) use ($prevScores, $industryAvgScores) {
            $prevScore = $prevScores->get($fs->factor_id);
            $industryAvg = $industryAvgScores->get($fs->factor_id);
            $diff = $prevScore !== null ? round($fs->score - $prevScore, 2) : null;
            
            $changeLabel = "Data Baru";
            if ($diff !== null) {
                if ($diff > 0) $changeLabel = "Naik " . abs($diff) . " poin";
                elseif ($diff < 0) $changeLabel = "Turun " . abs($diff) . " poin";
                else $changeLabel = "Stabil";
            }

            return [
                'id' => $fs->factor_id,
                'factor' => $fs->factor?->nama_factor ?? 'Faktor Tidak Diketahui',
                'code' => $fs->factor?->nama_factor ?? 'N/A',
                'score' => (float) $fs->score,
                'prev_score' => $prevScore !== null ? (float) $prevScore : null,
                'industry_avg' => $industryAvg !== null ? round((float) $industryAvg, 2) : null,
                'diff_prev' => $diff,
                'change_label' => $changeLabel,
                'trend' => $diff > 0 ? 'up' : ($diff < 0 ? 'down' : 'stable'),
                'category' => $fs->category
            ];
        });

        // 3. Cari Faktor Tertinggi & Terendah (Sorted List)
        $rankList = $radarData->sortByDesc('score')->values();
        $highestFactor = $rankList->first();
        $lowestFactor = $rankList->last();

        // 4. Ambil Rekomendasi DSS
        $recommendations = DB::table('assessment_recommendations as ar')
            ->join('recommendations as r', 'ar.recommendation_id', '=', 'r.recommendation_id')
            ->where('ar.assessment_id', $assessmentId)
            ->select('r.*')
            ->orderBy(DB::raw("CASE 
                WHEN r.category_level = 'KURANG_SEHAT' THEN 1
                WHEN r.category_level = 'CUKUP_SEHAT' THEN 2
                WHEN r.category_level = 'SEHAT' THEN 3
                WHEN r.category_level = 'SANGAT_SEHAT' THEN 4
                ELSE 5 END"))
            ->get();

        // Jika rekomendasi kosong (mungkin karena data master baru di-reset), lakukan trigger kalkulasi ulang otomatis
        if ($recommendations->isEmpty()) {
            $healthService = app(\App\Services\HealthService::class);
            $healthService->calculate($assessmentId);
            
            // Ambil ulang data setelah kalkulasi
            $recommendations = DB::table('assessment_recommendations as ar')
                ->join('recommendations as r', 'ar.recommendation_id', '=', 'r.recommendation_id')
                ->where('ar.assessment_id', $assessmentId)
                ->select('r.*')
                ->orderBy(DB::raw("CASE 
                    WHEN r.category_level = 'KURANG_SEHAT' THEN 1
                    WHEN r.category_level = 'CUKUP_SEHAT' THEN 2
                    WHEN r.category_level = 'SEHAT' THEN 3
                    WHEN r.category_level = 'SANGAT_SEHAT' THEN 4
                    ELSE 5 END"))
                ->get();
        }

        return [
            'assessment_info' => $assessment,
            'industry_benchmark' => [
                'sektor_usaha' => $industry,
                'peer_count' => $peerCount
            ],
            'previous_assessment_info' => $previousAssessment,
            'health_score' => $healthScore,
            'radar_chart' => $radarData,
            'rankings' => [
                'all' => $rankList,
                'top_3' => $rankList->take(3),
                'bottom_3' => $rankList->take(-3)->reverse()->values()
            ],
            'highlights' => [
                'highest' => $highestFactor,
                'lowest' => $lowestFactor
            ],
            'recommendations' => $recommendations
        ];
    }

    // ==========================================
    // WEB UI METHODS (MENGEMBALIKAN VIEW)
    // ==========================================

    private function getActiveData(Request $request)
    {
        // Gunakan guard owner atau default auth
        $owner = Auth::guard('owner')->user() ?? auth()->user();
        $allUmkms = $owner ? Umkm::where('owner_id', $owner->owner_id)->get() : collect();
        $hasUmkm = $allUmkms->count() > 0;
        
        $selectedUmkmId = $request->query('umkm_id');
        $activeUmkm = null;
        
        if ($hasUmkm) {
            if ($selectedUmkmId) {
                $activeUmkm = $allUmkms->where('umkm_id', $selectedUmkmId)->first();
            }
            if (!$activeUmkm) {
                $activeUmkm = $allUmkms->first();
            }
        }

        $latestAssessment = null;
        $assessmentWithScore = null;
        $hasAssessment = false;

        if ($activeUmkm) {
            $latestAssessment = Assessment::where('umkm_id', $activeUmkm->umkm_id)->latest()->first();
            // Cek apakah ada respon untuk assessment ini
            $hasAssessment = $latestAssessment && \Illuminate\Support\Facades\DB::table('responses')
                ->where('assessment_id', $latestAssessment->assessment_id)
                ->exists();
            
            $assessmentWithScore = $latestAssessment;
        }

        // Check if user has ANY UMKM or ANY Assessment at all in their account
        $hasAnyUmkm = $allUmkms->count() > 0;
        $hasAnyAssessment = false;
        if ($hasAnyUmkm) {
            $hasAnyAssessment = \Illuminate\Support\Facades\DB::table('responses')
                ->whereIn('assessment_id', function($query) use ($allUmkms) {
                    $query->select('assessment_id')->from('assessments')->whereIn('umkm_id', $allUmkms->pluck('umkm_id'));
                })->exists();
        }

        return [
            'owner' => $owner,
            'all_umkms' => $allUmkms,
            'active_umkm' => $activeUmkm,
            'has_umkm' => $hasUmkm,
            'has_assessment' => $hasAssessment,
            'latest_assessment' => $latestAssessment,
            'assessment_with_score' => $assessmentWithScore,
            'selected_umkm_id' => $activeUmkm ? $activeUmkm->umkm_id : null,
            
            // Global Popups (for new users)
            'show_umkm_popup' => !$hasAnyUmkm,
            'show_assessment_popup' => ($hasAnyUmkm && !$hasAnyAssessment),
            
            // In-page Notice (for existing users switching to an empty UMKM)
            'show_in_page_notice' => ($hasAnyAssessment && !$hasAssessment)
        ];
    }

    private function getDefaultAssessment()
    {
        // This is now redundant but kept for compatibility or internal logic if needed
        $data = $this->getActiveData(request());
        return $data['assessment_with_score'];
    }

    private function getUmkmStatus()
    {
        $data = $this->getActiveData(request());
        return ['has_umkm' => $data['has_umkm'], 'has_assessment' => $data['has_assessment']];
    }

    public function webDashboard(Request $request)
    {
        $data = $this->getActiveData($request);
        
        $myAvgHealth = 0;
        $myHealthCategory = 'N/A';
        $myFactors = collect();
        
        if ($data['assessment_with_score']) {
            $assessmentId = $data['assessment_with_score']->assessment_id;
            
            // Trigger recalculation if in_progress or forced by user
            if ($data['assessment_with_score']->status === 'in_progress' || $request->query('recalculate')) {
                $healthService = app(\App\Services\HealthService::class);
                $hs = $healthService->calculate($assessmentId);
            } else {
                $hs = HealthScore::where('assessment_id', $assessmentId)->first();
            }

            $myAvgHealth = $hs ? round($hs->overall_score, 1) : 0;
            $myHealthCategory = $hs ? $hs->category : 'PENDING';
            
            $myFactors = $hs ? DB::table('factor_scores as fs')
                ->join('factors as f', 'fs.factor_id', '=', 'f.factor_id')
                ->where('fs.health_score_id', $hs->health_score_id)
                ->select('f.nama_factor', 'fs.score as avg_score')
                ->get() : collect();
        }

        // Aggregate ecosystem statistics
        $totalUmkm = Umkm::count();
        $totalEmployees = DB::table('responses')
            ->where('respondent_type', 'employee')
            ->distinct('employee_code')
            ->count('employee_code');

        $totalOwners = DB::table('responses')
            ->join('assessments', 'responses.assessment_id', '=', 'assessments.assessment_id')
            ->join('umkms', 'assessments.umkm_id', '=', 'umkms.umkm_id')
            ->where('responses.respondent_type', 'owner')
            ->distinct('umkms.owner_id')
            ->count('umkms.owner_id');

        $totalRespondents = $totalEmployees + $totalOwners;
        
        $healthScores = HealthScore::all();
        $avgHealth = $healthScores->avg('overall_score') ?? 0;
        
        $sehatCount = $healthScores->where('overall_score', '>=', 60)->count();
        $kritisCount = $healthScores->where('overall_score', '<', 60)->count();

        // Business Type Distribution
        $bizTypes = Umkm::select('sektor_usaha', DB::raw('count(*) as count'))
            ->groupBy('sektor_usaha')
            ->pluck('count', 'sektor_usaha');

        // Gender Distribution
        $genderDist = Owner::select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->pluck('count', 'gender');

        // Top UMKM Ranking
        $topUmkms = DB::table('health_scores as hs')
            ->join('assessments as a', 'hs.assessment_id', '=', 'a.assessment_id')
            ->join('umkms as u', 'a.umkm_id', '=', 'u.umkm_id')
            ->select('u.nama_umkm', 'u.sektor_usaha', 'hs.overall_score', 'hs.category as health_category')
            ->orderByDesc('hs.overall_score')
            ->limit(5)
            ->get();

        // Average Factor Scores
        $avgFactors = DB::table('factor_scores as fs')
            ->join('factors as f', 'fs.factor_id', '=', 'f.factor_id')
            ->select('f.nama_factor', DB::raw('AVG(fs.score) as avg_score'))
            ->groupBy('f.factor_id', 'f.nama_factor')
            ->get();

        // Company Age Distribution
        $ageDist = Umkm::select('umur_usaha', DB::raw('count(*) as count'))
            ->groupBy('umur_usaha')
            ->pluck('count', 'umur_usaha');

        return view('pages.dashboard', [
            'stats' => [
                'total_umkm' => $totalUmkm,
                'total_respondents' => $totalRespondents,
                'avg_health' => round($avgHealth, 1),
                'sehat_count' => $sehatCount,
                'kritis_count' => $kritisCount,
            ],
            'biz_types' => $bizTypes,
            'age_dist' => $ageDist,
            'gender_dist' => $genderDist,
            'top_umkms' => $topUmkms,
            'avg_factors' => $avgFactors,
            'has_umkm' => $data['has_umkm'],
            'has_assessment' => $data['has_assessment'],
            'my_avg_health' => round($myAvgHealth, 1),
            'my_health_category' => $myHealthCategory,
            'my_factors' => $myFactors,
            'all_umkms' => $data['all_umkms'],
            'selected_umkm_id' => $data['selected_umkm_id'],
            'show_umkm_popup' => $data['show_umkm_popup'],
            'show_assessment_popup' => $data['show_assessment_popup'],
            'show_in_page_notice' => $data['show_in_page_notice']
        ]);
    }

    public function webAssessment()
    {
        $this->ensureMasterDataSeeded();

        $owner = \Illuminate\Support\Facades\Auth::guard('owner')->user();
        $umkms = $owner ? $owner->umkms : collect();
        
        foreach ($umkms as $umkm) {
            // Ambil asesmen terbaru yang belum 'completed' atau 'closed'
            $activeAssessment = \App\Models\Assessment::where('umkm_id', $umkm->umkm_id)
                ->whereIn('status', ['draft', 'open'])
                ->latest()
                ->first();

            // Jika tidak ada yang aktif, ambil yang terakhir selesai (untuk histori/tombol baru)
            if (!$activeAssessment) {
                $activeAssessment = \App\Models\Assessment::where('umkm_id', $umkm->umkm_id)
                    ->where('status', 'completed')
                    ->latest()
                    ->first();
            }

            $status = 'belum_mulai';
            $progress = 0;
            $assessmentId = null;
            $employeeAnswered = 0;
            $employeeTarget = 0;

            if ($activeAssessment) {
                $assessmentId = $activeAssessment->assessment_id;
                $employeeTarget = $activeAssessment->jumlah_karyawan ?? 0;
                
                // Hitung progres berdasarkan jawaban owner di asesmen ini
                $totalQuestions = \App\Models\Question::where('question_role', 'owner')->count();
                $answeredQuestions = \Illuminate\Support\Facades\DB::table('responses')
                    ->where('assessment_id', $assessmentId)
                    ->where('respondent_type', 'owner')
                    ->count();

                // Hitung responden karyawan unik
                $employeeAnswered = \Illuminate\Support\Facades\DB::table('responses')
                    ->where('assessment_id', $assessmentId)
                    ->where('respondent_type', 'employee')
                    ->distinct('employee_code')
                    ->count('employee_code');

                if ($activeAssessment->status == 'draft') {
                    $status = $answeredQuestions > 0 ? 'sedang_berlangsung' : 'belum_mulai';
                    $progress = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;
                } elseif ($activeAssessment->status == 'open') {
                    if ($employeeAnswered == 0) {
                        $status = 'menunggu_karyawan';
                    } elseif ($employeeTarget > 0 && $employeeAnswered >= $employeeTarget) {
                        $status = 'selesai_karyawan';
                    } else {
                        $status = 'proses_karyawan';
                    }
                    $progress = 100;
                } else {
                    $status = 'selesai';
                    $progress = 100;
                }
            }

            $umkm->active_assessment_id = $assessmentId;
            $umkm->assessment_status = $status;
            $umkm->assessment_progress = $progress;
            $umkm->employee_answered = $employeeAnswered;
            $umkm->employee_target = $employeeTarget;
        }

        $questions = \App\Models\Question::where('question_role', 'owner')
            ->orderBy('urutan')
            ->get();

        return view('pages.assessment', compact('umkms', 'questions'));
    }

    public function webFillAssessment($umkmId)
    {
        $owner = \Illuminate\Support\Facades\Auth::guard('owner')->user();
        $umkm = \App\Models\Umkm::where('owner_id', $owner->owner_id)->findOrFail($umkmId);
        
        // Pastikan ada record assessment
        $assessment = \App\Models\Assessment::where('umkm_id', $umkmId)
            ->whereIn('status', ['draft', 'open'])
            ->latest()
            ->first();

        if (!$assessment) {
            return redirect()->route('assessment')->with('error', 'Silakan tentukan target jumlah karyawan terlebih dahulu untuk memulai asesmen baru.');
        }

        // Pastikan master data faktor & pertanyaan sudah terisi otomatis
        $this->ensureMasterDataSeeded();

        // Ambil pertanyaan dikelompokkan per faktor (Hanya yang ada pertanyaan untuk owner)
        $factors = \App\Models\Factor::whereHas('questions', function($q) {
            $q->where('question_role', 'owner');
        })->with(['questions' => function($q) {
            $q->where('question_role', 'owner')->orderBy('urutan');
        }])->get();

        // Ambil jawaban yang sudah ada
        $existingResponses = \Illuminate\Support\Facades\DB::table('responses')
            ->where('assessment_id', $assessment->assessment_id)
            ->where('respondent_type', 'owner')
            ->pluck('answer_value', 'question_id')
            ->toArray();

        return view('pages.fill-assessment', compact('umkm', 'assessment', 'factors', 'existingResponses'));
    }

    public function webFinishAssessment($id)
    {
        $assessment = \App\Models\Assessment::findOrFail($id);
        $assessment->update(['status' => 'open']);
        
        // Hitung skor asesmen & otomatis tembakkan ke Webhook n8n
        try {
            $healthService = app(\App\Services\HealthService::class);
            $healthService->calculate($id);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Kalkulasi n8n gagal di webFinishAssessment: " . $e->getMessage());
        }

        return response()->json(['message' => 'Asesmen berhasil diselesaikan dan kini terbuka untuk karyawan.']);
    }

    /**
     * Memastikan master data Factors, Questions, dan Recommendations terisi dari CSV jika tabel kosong
     */
    private function ensureMasterDataSeeded()
    {
        if (\App\Models\Question::where('question_role', 'owner')->count() > 0 && \App\Models\Factor::count() > 0) {
            return;
        }

        // 1. Seed Factors dari CSV
        $factorsCsv = base_path('Imports/factors.csv');
        if (file_exists($factorsCsv)) {
            $lines = array_map('str_getcsv', file($factorsCsv));
            $header = array_shift($lines);
            foreach ($lines as $row) {
                if (count($row) >= 3) {
                    \App\Models\Factor::firstOrCreate(
                        ['nama_factor' => trim($row[0])],
                        ['deskripsi' => trim($row[1]), 'urutan' => (int)$row[2]]
                    );
                }
            }
        }

        // 2. Seed Questions dari CSV
        $questionsCsv = base_path('Imports/questions.csv');
        if (file_exists($questionsCsv)) {
            $lines = array_map('str_getcsv', file($questionsCsv));
            $header = array_shift($lines);
            foreach ($lines as $row) {
                if (count($row) >= 4) {
                    $factor = \App\Models\Factor::where('nama_factor', trim($row[0]))->first();
                    if ($factor) {
                        $urutan = (int)$row[3];
                        \App\Models\Question::firstOrCreate(
                            ['urutan' => $urutan],
                            [
                                'factor_id'     => $factor->factor_id,
                                'pertanyaan'    => trim($row[1]),
                                'question_role' => strtolower(trim($row[2])) === 'owner' ? 'owner' : 'employee',
                                'max_score'     => ($urutan >= 1 && $urutan <= 6) ? 3 : 5
                            ]
                        );
                    }
                }
            }
        }

        // 3. Seed Recommendations dari CSV
        if (\App\Models\Recommendation::count() == 0) {
            $recsCsv = base_path('Imports/recommendations.csv');
            if (file_exists($recsCsv)) {
                $lines = array_map('str_getcsv', file($recsCsv));
                $header = array_shift($lines);
                foreach ($lines as $row) {
                    if (count($row) >= 3) {
                        $factor = \App\Models\Factor::where('nama_factor', trim($row[0]))->first();
                        if ($factor) {
                            \App\Models\Recommendation::firstOrCreate(
                                [
                                    'factor_id'      => $factor->factor_id,
                                    'category_level' => trim($row[1])
                                ],
                                [
                                    'recommendation_text' => trim($row[2])
                                ]
                            );
                        }
                    }
                }
            }
        }
    }

    public function webCreateNewAssessment(Request $request, $umkmId)
    {
        $owner = \Illuminate\Support\Facades\Auth::guard('owner')->user();
        $umkm = \App\Models\Umkm::where('owner_id', $owner->owner_id)->findOrFail($umkmId);

        // Tutup asesmen lama yang masih aktif agar tidak tumpang tindih
        \App\Models\Assessment::where('umkm_id', $umkmId)
            ->whereIn('status', ['draft', 'open'])
            ->update(['status' => 'closed']);

        // Buat assessment baru
        $assessment = \App\Models\Assessment::create([
            'umkm_id' => $umkmId,
            'status' => 'draft',
            'tanggal_mulai' => now(),
            'assessment_name' => 'Asesmen ' . now()->format('M Y'),
            'jumlah_karyawan' => $request->input('jumlah_karyawan', 0)
        ]);

        return redirect()->route('assessment.fill', $umkmId);
    }

    public function webComparison(Request $request)
    {
        $data = $this->getActiveData($request);
        
        // Cek jika user memilih periode tertentu, jika tidak gunakan yang terbaru
        $requestedAssessmentId = $request->query('assessment_id');
        $assessment = null;
        
        if ($requestedAssessmentId) {
            $assessment = \App\Models\Assessment::where('umkm_id', $data['selected_umkm_id'])
                ->find($requestedAssessmentId);
        }
        
        if (!$assessment) {
            $assessment = $data['assessment_with_score'];
        }
        
        $history = collect();
        if ($data['active_umkm']) {
            $tempHistory = \App\Models\Assessment::where('umkm_id', $data['active_umkm']->umkm_id)
                ->orderBy('created_at', 'asc')
                ->get();
                
            $monthCounters = [];
            foreach ($tempHistory as $hist) {
                $monthYear = \Carbon\Carbon::parse($hist->assessment_date)->format('Y-m');
                if (!isset($monthCounters[$monthYear])) {
                    $monthCounters[$monthYear] = 1;
                } else {
                    $monthCounters[$monthYear]++;
                }
                $hist->sequence_in_month = $monthCounters[$monthYear];
            }
            $history = $tempHistory->sortByDesc('created_at')->values();
        }

        $hasUmkm = $data['has_umkm'];
        $hasAssessment = $data['has_assessment'];

        $defaultData = [
            'assessment_info' => null,
            'health_score' => null,
            'radar_chart' => [],
            'rankings' => ['all' => []],
            'industry_benchmark' => ['peer_count' => 0, 'sektor_usaha' => 'N/A'],
            'previous_assessment_info' => null,
            'show_umkm_popup' => $data['show_umkm_popup'],
            'show_assessment_popup' => $data['show_assessment_popup'],
            'all_umkms' => $data['all_umkms'],
            'selected_umkm_id' => $data['selected_umkm_id'],
            'show_in_page_notice' => $data['show_in_page_notice'],
            'assessment_history' => $history,
            'selected_assessment_id' => $assessment ? $assessment->assessment_id : null
        ];

        if (!$assessment) {
            $defaultData['error'] = 'Belum ada data assessment.';
            return view('pages.comparison', $defaultData);
        }

        $detailedData = $this->getDetailedData($assessment->assessment_id);
        $dataMerge = array_merge($defaultData, $detailedData ?? []);

        return view('pages.comparison', $dataMerge);
    }

    public function webProfilFaktor(Request $request)
    {
        $data = $this->getActiveData($request);
        
        // Cek jika user memilih periode tertentu, jika tidak gunakan yang terbaru
        $requestedAssessmentId = $request->query('assessment_id');
        $assessment = null;
        
        if ($requestedAssessmentId) {
            $assessment = Assessment::where('umkm_id', $data['selected_umkm_id'])
                ->find($requestedAssessmentId);
        }
        
        if (!$assessment) {
            $assessment = $data['assessment_with_score'];
        }
        
        $history = collect();
        if ($data['active_umkm']) {
            // Mengambil secara berurutan dari terlama ke terbaru untuk memberi nomor urut
            $tempHistory = Assessment::where('umkm_id', $data['active_umkm']->umkm_id)
                ->orderBy('created_at', 'asc')
                ->get();
                
            $monthCounters = [];
            foreach ($tempHistory as $hist) {
                $monthYear = \Carbon\Carbon::parse($hist->assessment_date)->format('Y-m');
                if (!isset($monthCounters[$monthYear])) {
                    $monthCounters[$monthYear] = 1;
                } else {
                    $monthCounters[$monthYear]++;
                }
                $hist->sequence_in_month = $monthCounters[$monthYear];
            }
            
            // Urutkan kembali dari terbaru ke terlama untuk ditampilkan di dropdown
            $history = $tempHistory->sortByDesc('created_at')->values();
        }

        $defaultData = [
            'assessment_info' => $assessment,
            'health_score' => null,
            'radar_chart' => [],
            'rankings' => ['all' => []],
            'highlights' => ['highest' => null, 'lowest' => null],
            'recommendations' => collect(),
            'outliers' => [],
            'show_umkm_popup' => $data['show_umkm_popup'],
            'show_assessment_popup' => $data['show_assessment_popup'],
            'all_umkms' => $data['all_umkms'],
            'selected_umkm_id' => $data['selected_umkm_id'],
            'show_in_page_notice' => $data['show_in_page_notice'],
            'assessment_history' => $history,
            'selected_assessment_id' => $assessment ? $assessment->assessment_id : null
        ];

        if (!$assessment) {
            $defaultData['error'] = 'Belum ada data assessment.';
            return view('pages.profil-faktor', $defaultData);
        }

        $detailedData = $this->getDetailedData($assessment->assessment_id);
        $dataMerge = array_merge($defaultData, $detailedData ?? []);

        // Fetch outlier data directly without json response
        $factors = DB::table('factors')->get();
        $outlierResults = [];

        foreach ($factors as $factor) {
            $responses = DB::table('responses as r')
                ->join('questions as q', 'r.question_id', '=', 'q.question_id')
                ->where('r.assessment_id', $assessment->assessment_id)
                ->where('q.factor_id', $factor->factor_id)
                ->select('r.answer_value', 'q.max_score')
                ->get();

            $normalizedScores = [];
            foreach ($responses as $resp) {
                $max = $resp->max_score ?? 5;
                $normalizedScores[] = ($resp->answer_value / $max) * 100;
            }

            if (count($normalizedScores) > 0) {
                sort($normalizedScores);
                $outlierResults[] = [
                    'nama_factor' => $factor->nama_factor,
                    'code' => $factor->nama_factor,
                    'stats' => $this->calculateBoxplot($normalizedScores)
                ];
            }
        }
        $dataMerge['outliers'] = $outlierResults;

        return view('pages.profil-faktor', $dataMerge);
    }

    public function webRekomendasi(Request $request)
    {
        $data = $this->getActiveData($request);
        
        // Cek jika user memilih periode tertentu
        $requestedAssessmentId = $request->query('assessment_id');
        $assessment = null;
        
        if ($requestedAssessmentId) {
            $assessment = \App\Models\Assessment::where('umkm_id', $data['selected_umkm_id'])
                ->find($requestedAssessmentId);
        }
        
        if (!$assessment) {
            $assessment = $data['assessment_with_score'];
        }

        $history = collect();
        if ($data['active_umkm']) {
            $tempHistory = \App\Models\Assessment::where('umkm_id', $data['active_umkm']->umkm_id)
                ->orderBy('created_at', 'asc')
                ->get();
                
            $monthCounters = [];
            foreach ($tempHistory as $hist) {
                $monthYear = \Carbon\Carbon::parse($hist->assessment_date)->format('Y-m');
                if (!isset($monthCounters[$monthYear])) {
                    $monthCounters[$monthYear] = 1;
                } else {
                    $monthCounters[$monthYear]++;
                }
                $hist->sequence_in_month = $monthCounters[$monthYear];
            }
            $history = $tempHistory->sortByDesc('created_at')->values();
        }

        $hasUmkm = $data['has_umkm'];
        $hasAssessment = $data['has_assessment'];

        $defaultData = [
            'assessment_info' => null,
            'health_score' => null,
            'recommendations' => collect(),
            'radar_chart' => [],
            'show_umkm_popup' => $data['show_umkm_popup'],
            'show_assessment_popup' => $data['show_assessment_popup'],
            'all_umkms' => $data['all_umkms'],
            'selected_umkm_id' => $data['selected_umkm_id'],
            'show_in_page_notice' => $data['show_in_page_notice'],
            'assessment_history' => $history,
            'selected_assessment_id' => $assessment ? $assessment->assessment_id : null
        ];

        if (!$assessment) {
            $defaultData['error'] = 'Belum ada data assessment.';
            return view('pages.rekomendasi', $defaultData);
        }

        $detailedData = $this->getDetailedData($assessment->assessment_id);
        $dataMerge = array_merge($defaultData, $detailedData ?? []);

        return view('pages.rekomendasi', $dataMerge);
    }

    public function webMonitoring(Request $request)
    {
        $data = $this->getActiveData($request);
        
        // Gunakan assessment terbaru yang sama dengan Dashboard agar angka konsisten
        $assessment = $data['assessment_with_score'];

        $hasUmkm = $data['has_umkm'];
        $hasAssessment = $data['has_assessment'];

        $defaultData = [
            'assessment' => $assessment,
            'total_respondents' => 0,
            'target_respondents' => 50,
            'completion_rate' => 0,
            'estimated_score' => 0,
            'estimated_category' => 'N/A',
            'recent_activity' => collect(),
            'show_umkm_popup' => $data['show_umkm_popup'],
            'show_assessment_popup' => $data['show_assessment_popup'],
            'all_umkms' => $data['all_umkms'],
            'selected_umkm_id' => $data['selected_umkm_id']
        ];

        if (!$assessment) {
            $defaultData['error'] = 'Belum ada data assessment.';
            return view('pages.monitoring', $defaultData);
        }

        $id = $assessment->assessment_id;

        $totalEmployees = \App\Models\Response::where('assessment_id', $id)
            ->where('respondent_type', 'employee')
            ->distinct('employee_code')
            ->count('employee_code');

        $hasOwner = \App\Models\Response::where('assessment_id', $id)
            ->where('respondent_type', 'owner')
            ->exists() ? 1 : 0;

        $totalRespondents = $totalEmployees + $hasOwner;

        $targetRespondents = ($assessment->jumlah_karyawan ?? 0) + 1;
        if ($targetRespondents <= 0) $targetRespondents = 1;

        $completionRate = min(round(($totalRespondents / $targetRespondents) * 100), 100);

        $healthService = app(\App\Services\HealthService::class);
        $estimatedScoreData = $healthService->calculate($id);

        $recentActivity = \App\Models\Response::where('assessment_id', $id)
            ->where('respondent_type', 'employee')
            ->select('employee_code', DB::raw('MAX(created_at) as last_active'))
            ->groupBy('employee_code')
            ->orderBy('last_active', 'desc')
            ->limit(3)
            ->get();

        return view('pages.monitoring', [
            'assessment' => $assessment,
            'total_respondents' => $totalRespondents,
            'target_respondents' => $targetRespondents,
            'completion_rate' => $completionRate,
            'estimated_score' => round($estimatedScoreData->overall_score ?? 0, 1),
            'estimated_category' => $estimatedScoreData->category ?? 'N/A',
            'recent_activity' => $recentActivity,
            'show_umkm_popup' => $data['show_umkm_popup'],
            'show_assessment_popup' => $data['show_assessment_popup'],
            'all_umkms' => $data['all_umkms'],
            'selected_umkm_id' => $data['selected_umkm_id'],
            'show_in_page_notice' => $data['show_in_page_notice']
        ]);
    }

    public function webUmkmRank(Request $request)
    {
        $data = $this->getActiveData($request);
        return view('pages.umkm-rank', $data);
    }

    public function apiUmkmRank(Request $request)
    {
        $search = $request->query('search');
        $sort = $request->query('sort', 'score_desc');
        $limit = $request->query('limit', '25');

        // Subquery untuk skor terbaru per UMKM
        $latestScores = DB::table('health_scores as hs')
            ->join('assessments as a', 'hs.assessment_id', '=', 'a.assessment_id')
            ->select('a.umkm_id', 'hs.overall_score', 'hs.category')
            ->whereIn('a.assessment_id', function($q) {
                $q->select(DB::raw('MAX(assessment_id)'))
                  ->from('assessments')
                  ->groupBy('umkm_id');
            });

        // Hitung Global Rank berdasarkan Skor Keseluruhan
        $globalRankQuery = DB::table('umkms as u')
            ->leftJoinSub($latestScores, 'ls', function ($join) {
                $join->on('u.umkm_id', '=', 'ls.umkm_id');
            })
            ->orderByDesc('ls.overall_score')
            ->pluck('u.umkm_id');

        $rankMap = [];
        foreach ($globalRankQuery as $index => $umkmId) {
            $rankMap[$umkmId] = $index + 1;
        }

        $query = DB::table('umkms as u')
            ->leftJoinSub($latestScores, 'ls', function ($join) {
                $join->on('u.umkm_id', '=', 'ls.umkm_id');
            })
            ->select('u.nama_umkm', 'u.sektor_usaha', 'u.created_at', 'u.umkm_id', 'ls.overall_score', 'ls.category as health_category');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('u.nama_umkm', 'like', "%{$search}%")
                  ->orWhere('u.sektor_usaha', 'like', "%{$search}%");
            });
        }

        switch ($sort) {
            case 'score_asc': $query->orderBy('ls.overall_score', 'asc'); break;
            case 'az': $query->orderBy('u.nama_umkm', 'asc'); break;
            case 'za': $query->orderBy('u.nama_umkm', 'desc'); break;
            case 'kategori': $query->orderBy('ls.category', 'asc'); break;
            case 'terbaru': $query->orderBy('u.created_at', 'desc'); break;
            case 'terlama': $query->orderBy('u.created_at', 'asc'); break;
            case 'score_desc': 
            default: 
                $query->orderByDesc('ls.overall_score'); 
                break;
        }

        if ($limit !== 'all') {
            $query->limit((int)$limit);
        }

        $results = $query->get();
        
        // Pasang rank global ke hasil pencarian
        foreach ($results as $result) {
            $result->global_rank = $rankMap[$result->umkm_id] ?? '-';
        }

        return response()->json($results);
    }

    /**
     * Mendapatkan data statistik Boxplot untuk deteksi Outlier per Faktor
     */
    public function getFactorOutliers($assessmentId)
    {
        $factors = DB::table('factors')->get();
        $results = [];

        foreach ($factors as $factor) {
            $scores = DB::table('responses as r')
                ->join('questions as q', 'r.question_id', '=', 'q.question_id')
                ->where('r.assessment_id', $assessmentId)
                ->where('q.factor_id', $factor->factor_id)
                ->select('r.answer_value', 'q.max_score')
                ->get();

            $normalizedScores = [];
            foreach ($scores as $resp) {
                $max = $resp->max_score ?? 5;
                $normalizedScores[] = ($resp->answer_value / $max) * 100;
            }

            if (count($normalizedScores) > 0) {
                sort($normalizedScores);
                $results[] = [
                    'nama_factor' => $factor->nama_factor,
                    'stats' => $this->calculateBoxplot($normalizedScores)
                ];
            }
        }

        return response()->json([
            'message' => 'Data outlier per faktor berhasil dihitung',
            'data' => $results
        ]);
    }

    /**
     * Fungsi helper untuk menghitung komponen Boxplot
     */
    private function calculateBoxplot(array $data)
    {
        $count = count($data);
        
        // Median (Q2)
        $median = $this->getPercentile($data, 0.5);
        
        // Q1 (25th percentile) dan Q3 (75th percentile)
        $q1 = $this->getPercentile($data, 0.25);
        $q3 = $this->getPercentile($data, 0.75);
        
        $iqr = $q3 - $q1;
        
        // Batas bawah dan atas untuk outlier (1.5 * IQR)
        $lowerFence = $q1 - (1.5 * $iqr);
        $upperFence = $q3 + (1.5 * $iqr);
        
        // Filter Outliers
        $outliers = array_values(array_filter($data, function($val) use ($lowerFence, $upperFence) {
            return $val < $lowerFence || $val > $upperFence;
        }));
        
        // Nilai Min dan Max (yang bukan outlier)
        $nonOutliers = array_filter($data, function($val) use ($lowerFence, $upperFence) {
            return $val >= $lowerFence && $val <= $upperFence;
        });

        return [
            'min' => !empty($nonOutliers) ? min($nonOutliers) : $data[0],
            'q1' => $q1,
            'median' => $median,
            'q3' => $q3,
            'max' => !empty($nonOutliers) ? max($nonOutliers) : $data[$count - 1],
            'outliers' => $outliers,
            'sample_size' => $count
        ];
    }

    private function getPercentile($data, $percentile)
    {
        $index = ($percentile * (count($data) - 1));
        if (floor($index) == $index) {
            return $data[$index];
        } else {
            $low = floor($index);
            $high = ceil($index);
            $fraction = $index - $low;
            return $data[$low] + $fraction * ($data[$high] - $data[$low]);
        }
    }

    public function apiRealtimeDashboard(Request $request)
    {
        $data = $this->getActiveData($request);
        
        $myAvgHealth = 0;
        $myHealthCategory = 'N/A';
        $myFactors = collect();
        
        if ($data['assessment_with_score']) {
            $assessmentId = $data['assessment_with_score']->assessment_id;
            $hs = HealthScore::where('assessment_id', $assessmentId)->first();

            $myAvgHealth = $hs ? round($hs->overall_score, 1) : 0;
            $myHealthCategory = $hs ? $hs->category : 'PENDING';
            
            $myFactors = $hs ? DB::table('factor_scores as fs')
                ->join('factors as f', 'fs.factor_id', '=', 'f.factor_id')
                ->where('fs.health_score_id', $hs->health_score_id)
                ->select('f.nama_factor', 'fs.score as avg_score')
                ->get() : collect();
        }

        $totalUmkm = Umkm::count();
        $totalEmployees = DB::table('responses')
            ->where('respondent_type', 'employee')
            ->distinct('employee_code')
            ->count('employee_code');

        $totalOwners = DB::table('responses')
            ->join('assessments', 'responses.assessment_id', '=', 'assessments.assessment_id')
            ->join('umkms', 'assessments.umkm_id', '=', 'umkms.umkm_id')
            ->where('responses.respondent_type', 'owner')
            ->distinct('umkms.owner_id')
            ->count('umkms.owner_id');

        $totalRespondents = $totalEmployees + $totalOwners;
        
        $healthScores = HealthScore::all();
        $avgHealth = $healthScores->avg('overall_score') ?? 0;
        
        $sehatCount = $healthScores->where('overall_score', '>=', 60)->count();
        $kritisCount = $healthScores->where('overall_score', '<', 60)->count();

        $bizTypes = Umkm::select('sektor_usaha', DB::raw('count(*) as count'))
            ->groupBy('sektor_usaha')
            ->pluck('count', 'sektor_usaha');

        $genderDist = Owner::select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->pluck('count', 'gender');

        $topUmkms = DB::table('health_scores as hs')
            ->join('assessments as a', 'hs.assessment_id', '=', 'a.assessment_id')
            ->join('umkms as u', 'a.umkm_id', '=', 'u.umkm_id')
            ->select('u.nama_umkm', 'u.sektor_usaha', 'hs.overall_score', 'hs.category as health_category')
            ->orderByDesc('hs.overall_score')
            ->limit(5)
            ->get();

        $ageDist = Umkm::select('umur_usaha', DB::raw('count(*) as count'))
            ->groupBy('umur_usaha')
            ->pluck('count', 'umur_usaha');

        return response()->json([
            'stats' => [
                'total_umkm' => $totalUmkm,
                'total_respondents' => $totalRespondents,
                'avg_health' => round($avgHealth, 1),
                'sehat_count' => $sehatCount,
                'kritis_count' => $kritisCount,
            ],
            'biz_types' => $bizTypes,
            'age_dist' => $ageDist,
            'gender_dist' => $genderDist,
            'top_umkms' => $topUmkms,
            'has_umkm' => $data['has_umkm'],
            'has_assessment' => $data['has_assessment'],
            'my_avg_health' => round($myAvgHealth, 1),
            'my_health_category' => $myHealthCategory,
            'my_factors' => $myFactors,
            'selected_umkm_id' => $data['selected_umkm_id'],
        ]);
    }

    public function apiRealtimeAssessment(Request $request)
    {
        $owner = \Illuminate\Support\Facades\Auth::guard('owner')->user();
        $umkms = $owner ? $owner->umkms : collect();
        
        $result = [];
        foreach ($umkms as $umkm) {
            $activeAssessment = \App\Models\Assessment::where('umkm_id', $umkm->umkm_id)
                ->whereIn('status', ['draft', 'open'])
                ->latest()
                ->first();

            if (!$activeAssessment) {
                $activeAssessment = \App\Models\Assessment::where('umkm_id', $umkm->umkm_id)
                    ->where('status', 'completed')
                    ->latest()
                    ->first();
            }

            $status = 'belum_mulai';
            $progress = 0;
            $assessmentId = null;
            $employeeAnswered = 0;
            $employeeTarget = 0;

            if ($activeAssessment) {
                $assessmentId = $activeAssessment->assessment_id;
                $employeeTarget = $activeAssessment->jumlah_karyawan ?? 0;
                
                $totalQuestions = \App\Models\Question::where('question_role', 'owner')->count();
                $answeredQuestions = \Illuminate\Support\Facades\DB::table('responses')
                    ->where('assessment_id', $assessmentId)
                    ->where('respondent_type', 'owner')
                    ->count();

                $employeeAnswered = \Illuminate\Support\Facades\DB::table('responses')
                    ->where('assessment_id', $assessmentId)
                    ->where('respondent_type', 'employee')
                    ->distinct('employee_code')
                    ->count('employee_code');

                if ($activeAssessment->status == 'draft') {
                    $status = $answeredQuestions > 0 ? 'sedang_berlangsung' : 'belum_mulai';
                    $progress = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;
                } elseif ($activeAssessment->status == 'open') {
                    if ($employeeAnswered == 0) {
                        $status = 'menunggu_karyawan';
                    } elseif ($employeeAnswered < $employeeTarget) {
                        $status = 'proses_karyawan';
                    } else {
                       $status = 'selesai_karyawan';
                    }
                    $progress = 100;
                } elseif ($activeAssessment->status == 'completed' || $activeAssessment->status == 'closed') {
                    $status = 'selesai';
                    $progress = 100;
                }
            }

            $result[] = [
                'umkm_id' => $umkm->umkm_id,
                'nama_umkm' => $umkm->nama_umkm,
                'sektor_usaha' => $umkm->sektor_usaha ?? 'Sektor Umum',
                'active_assessment_id' => $assessmentId,
                'assessment_status' => $status,
                'assessment_progress' => $progress,
                'employee_answered' => $employeeAnswered,
                'employee_target' => $employeeTarget
            ];
        }

        return response()->json([
            'umkms' => $result
        ]);
    }

    public function apiRealtimeProfilFaktor(Request $request)
    {
        $data = $this->getActiveData($request);
        $requestedAssessmentId = $request->query('assessment_id');
        $assessment = null;
        if ($requestedAssessmentId) {
            $assessment = Assessment::where('umkm_id', $data['selected_umkm_id'])->find($requestedAssessmentId);
        }
        if (!$assessment) {
            $assessment = $data['assessment_with_score'];
        }
        if (!$assessment) {
            return response()->json(['error' => 'Belum ada data assessment.'], 404);
        }
        
        $detailedData = $this->getDetailedData($assessment->assessment_id);
        
        $factors = DB::table('factors')->get();
        $outlierResults = [];
        foreach ($factors as $factor) {
            $responses = DB::table('responses as r')
                ->join('questions as q', 'r.question_id', '=', 'q.question_id')
                ->where('r.assessment_id', $assessment->assessment_id)
                ->where('q.factor_id', $factor->factor_id)
                ->select('r.answer_value', 'q.max_score')
                ->get();

            $normalizedScores = [];
            foreach ($responses as $resp) {
                $max = $resp->max_score ?? 5;
                $normalizedScores[] = ($resp->answer_value / $max) * 100;
            }

            if (count($normalizedScores) > 0) {
                sort($normalizedScores);
                $outlierResults[] = [
                    'nama_factor' => $factor->nama_factor,
                    'code' => $factor->nama_factor,
                    'stats' => $this->calculateBoxplot($normalizedScores)
                ];
            }
        }

        return response()->json([
            'health_score' => $detailedData['health_score'] ?? null,
            'radar_chart' => $detailedData['radar_chart'] ?? [],
            'rankings' => $detailedData['rankings'] ?? ['all' => []],
            'highlights' => $detailedData['highlights'] ?? ['highest' => null, 'lowest' => null],
            'recommendations' => $detailedData['recommendations'] ?? [],
            'outliers' => $outlierResults,
            'selected_assessment_id' => $assessment->assessment_id
        ]);
    }

    public function apiRealtimeComparison(Request $request)
    {
        $data = $this->getActiveData($request);
        $requestedAssessmentId = $request->query('assessment_id');
        $assessment = null;
        if ($requestedAssessmentId) {
            $assessment = \App\Models\Assessment::where('umkm_id', $data['selected_umkm_id'])->find($requestedAssessmentId);
        }
        if (!$assessment) {
            $assessment = $data['assessment_with_score'];
        }
        if (!$assessment) {
            return response()->json(['error' => 'Belum ada data assessment.'], 404);
        }

        $detailedData = $this->getDetailedData($assessment->assessment_id);

        return response()->json([
            'health_score' => $detailedData['health_score'] ?? null,
            'radar_chart' => $detailedData['radar_chart'] ?? [],
            'industry_benchmark' => $detailedData['industry_benchmark'] ?? ['peer_count' => 0, 'sektor_usaha' => 'N/A'],
            'previous_assessment_info' => $detailedData['previous_assessment_info'] ?? null,
            'selected_assessment_id' => $assessment->assessment_id
        ]);
    }

    public function apiRealtimeRekomendasi(Request $request)
    {
        $data = $this->getActiveData($request);
        $requestedAssessmentId = $request->query('assessment_id');
        $assessment = null;
        if ($requestedAssessmentId) {
            $assessment = \App\Models\Assessment::where('umkm_id', $data['selected_umkm_id'])->find($requestedAssessmentId);
        }
        if (!$assessment) {
            $assessment = $data['assessment_with_score'];
        }
        if (!$assessment) {
            return response()->json(['error' => 'Belum ada data assessment.'], 404);
        }

        $detailedData = $this->getDetailedData($assessment->assessment_id);

        return response()->json([
            'health_score' => $detailedData['health_score'] ?? null,
            'recommendations' => $detailedData['recommendations'] ?? [],
            'radar_chart' => $detailedData['radar_chart'] ?? [],
            'selected_assessment_id' => $assessment->assessment_id
        ]);
    }

    public function apiRealtimeMonitoring(Request $request)
    {
        $data = $this->getActiveData($request);
        $assessment = $data['assessment_with_score'];
        if (!$assessment) {
            return response()->json(['error' => 'Belum ada data assessment.'], 404);
        }

        $id = $assessment->assessment_id;
        $totalEmployees = \App\Models\Response::where('assessment_id', $id)
            ->where('respondent_type', 'employee')
            ->distinct('employee_code')
            ->count('employee_code');

        $hasOwner = \App\Models\Response::where('assessment_id', $id)
            ->where('respondent_type', 'owner')
            ->exists() ? 1 : 0;

        $totalRespondents = $totalEmployees + $hasOwner;
        $targetRespondents = ($assessment->jumlah_karyawan ?? 0) + 1;
        if ($targetRespondents <= 0) $targetRespondents = 1;

        $completionRate = min(round(($totalRespondents / $targetRespondents) * 100), 100);

        $healthService = app(\App\Services\HealthService::class);
        $estimatedScoreData = $healthService->calculate($id);

        $recentActivity = \App\Models\Response::where('assessment_id', $id)
            ->where('respondent_type', 'employee')
            ->select('employee_code', DB::raw('MAX(created_at) as last_active'))
            ->groupBy('employee_code')
            ->orderBy('last_active', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($act) {
                return [
                    'employee_code' => $act->employee_code,
                    'last_active' => \Carbon\Carbon::parse($act->last_active)->diffForHumans()
                ];
            });

        return response()->json([
            'total_respondents' => $totalRespondents,
            'target_respondents' => $targetRespondents,
            'completion_rate' => $completionRate,
            'estimated_score' => round($estimatedScoreData->overall_score ?? 0, 1),
            'estimated_category' => $estimatedScoreData->category ?? 'N/A',
            'recent_activity' => $recentActivity,
            'selected_assessment_id' => $id
        ]);
    }
}
