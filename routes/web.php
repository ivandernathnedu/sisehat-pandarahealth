<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeAssessmentController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('pages.welcome');
})->name('welcome');

// Web App UI Routes (Frontend Integration)
Route::middleware(['auth:owner'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'webDashboard'])->name('dashboard');
    Route::get('/assessment', [DashboardController::class, 'webAssessment'])->name('assessment');
    Route::get('/assessment/fill/{umkm_id}', [DashboardController::class, 'webFillAssessment'])->name('assessment.fill');
    Route::post('/assessment/finish/{id}', [DashboardController::class, 'webFinishAssessment'])->name('assessment.finish');
    Route::post('/assessment/new/{umkm_id}', [DashboardController::class, 'webCreateNewAssessment'])->name('assessment.new');
    Route::get('/monitoring', [DashboardController::class, 'webMonitoring'])->name('monitoring');
    Route::get('/comparison', [DashboardController::class, 'webComparison'])->name('comparison');
    Route::get('/rekomendasi', [DashboardController::class, 'webRekomendasi'])->name('rekomendasi');
    Route::get('/profil-faktor', [DashboardController::class, 'webProfilFaktor'])->name('profil-faktor');
    Route::get('/umkm-rank', [DashboardController::class, 'webUmkmRank'])->name('umkm-rank');
    Route::get('/api/umkm/rank', [DashboardController::class, 'apiUmkmRank'])->name('api.umkm.rank');
    
    // API Polling Realtime Baru (mengembalikan JSON)
    Route::prefix('api/realtime')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'apiRealtimeDashboard'])->name('api.realtime.dashboard');
        Route::get('/assessment', [DashboardController::class, 'apiRealtimeAssessment'])->name('api.realtime.assessment');
        Route::get('/profil-faktor', [DashboardController::class, 'apiRealtimeProfilFaktor'])->name('api.realtime.profil-faktor');
        Route::get('/comparison', [DashboardController::class, 'apiRealtimeComparison'])->name('api.realtime.comparison');
        Route::get('/rekomendasi', [DashboardController::class, 'apiRealtimeRekomendasi'])->name('api.realtime.rekomendasi');
        Route::get('/monitoring', [DashboardController::class, 'apiRealtimeMonitoring'])->name('api.realtime.monitoring');
    });
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/name', [ProfileController::class, 'showChangeName'])->name('profile.name');
    Route::post('/profile/name', [ProfileController::class, 'updateName'])->name('profile.name.post');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.post');

    Route::get('/beranda', function () { return view('pages.beranda'); })->name('beranda');
    Route::get('/tambah-umkm', function () { return view('pages.tambah-umkm'); })->name('tambah-umkm');
    Route::post('/tambah-umkm', [UmkmController::class, 'webStore'])->name('tambah-umkm.post');
    Route::delete('/umkm/{id}', [UmkmController::class, 'webDestroy'])->name('umkm.destroy');
});

// Routes untuk Ganti Password (bisa diakses user login maupun guest)
Route::get('/profile/password', [ProfileController::class, 'showChangePassword'])->name('profile.password');
Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.post');

Route::post('/assessment/generate', [AssessmentController::class, 'createAssessment'])->name('assessment.generate');
Route::post('/api/responses/submit', [AssessmentController::class, 'submitResponse'])->name('api.responses.submit');
Route::get('/employee-assessment/{token}', [EmployeeAssessmentController::class, 'show'])->name('employee.assessment');
Route::post('/employee-assessment/{token}', [EmployeeAssessmentController::class, 'store'])->name('employee.assessment.submit');
// Route Rahasia untuk Migrasi & Impor Data via Browser (Berguna untuk cPanel / Hosting tanpa SSH)
Route::get('/run-migration', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $outputMigrate = \Illuminate\Support\Facades\Artisan::output();
        
        \Illuminate\Support\Facades\Artisan::call('import:csv');
        $outputImport = \Illuminate\Support\Facades\Artisan::output();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Migrasi database dan impor CSV berhasil dilakukan!',
            'migrate_output' => $outputMigrate,
            'import_output' => $outputImport
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
});

// Route Ujicoba Tembak Webhook n8n Langsung dari Browser
Route::get('/test-n8n', function (\App\Services\HealthService $healthService) {
    // Cari asesmen aktif terbaru dari UMKM yang terdaftar di sistem
    $assessment = \App\Models\Assessment::with('umkm.owner')->latest()->first();

    if (!$assessment) {
        $umkm = \App\Models\Umkm::with('owner')->latest()->first();
        if ($umkm) {
            $assessment = \App\Models\Assessment::create([
                'umkm_id'         => $umkm->umkm_id,
                'assessment_name' => 'Asesmen ' . $umkm->nama_umkm,
                'status'          => 'open',
                'tanggal_mulai'   => now(),
                'jumlah_karyawan' => 10,
            ]);
            $assessment->load('umkm.owner');
        } else {
            return response()->json(['error' => 'Belum ada data UMKM terdaftar di sistem.'], 404);
        }
    }

    $healthScore = $healthService->calculate($assessment->assessment_id);
    $result = $healthService->sendToN8n($assessment->assessment_id, $healthScore);

    return response()->json([
        'status'         => 'success',
        'message'        => 'Data asesmen riil berhasil dikalkulasi dan dikirim ke n8n!',
        'umkm'           => $assessment->umkm?->nama_umkm,
        'owner'          => $assessment->umkm?->owner?->name,
        'overall_score'  => $healthScore->overall_score,
        'category'       => $healthScore->category,
        'n8n_result'     => $result
    ]);
});

Route::get('/login', function () { return view('pages.login'); })->name('login');
Route::get('/register', function () { return view('pages.register'); })->name('register');

Route::get('/api/docs', function () { return view('pages.api-docs'); })->name('api.docs');
Route::get('/api/docs/full', function () { return view('pages.api-docs-full'); })->name('api.docs.full');

// Status API (Agar ketika dosen membuka /api tidak error 404 & langsung melihat peta dokumentasi API!)
Route::get('/api', function () {
    $baseUrl = url('/');
    return response()->json([
        'status' => 'success',
        'message' => 'SiSehat API is running smoothly!',
        'version' => '1.0',
        'developer' => 'Pandara Health Team',
        'documentation' => [
            'base_url' => $baseUrl,
            'endpoints' => [
                'authentication' => [
                    'register' => [
                        'method' => 'POST',
                        'url' => $baseUrl . '/api/auth/register',
                        'description' => 'Mendaftarkan akun Owner baru'
                    ],
                    'login' => [
                        'method' => 'POST',
                        'url' => $baseUrl . '/api/auth/login',
                        'description' => 'Autentikasi login Owner untuk mendapatkan sesi'
                    ],
                    'logout' => [
                        'method' => 'POST',
                        'url' => $baseUrl . '/api/auth/logout',
                        'description' => 'Mengeluarkan sesi Owner'
                    ],
                    'me' => [
                        'method' => 'GET',
                        'url' => $baseUrl . '/api/auth/me',
                        'description' => 'Mengambil data profil Owner aktif'
                    ]
                ],
                'umkm_management' => [
                    'list' => [
                        'method' => 'GET',
                        'url' => $baseUrl . '/api/umkm',
                        'description' => 'Mengambil seluruh daftar UMKM milik Owner'
                    ],
                    'create' => [
                        'method' => 'POST',
                        'url' => $baseUrl . '/api/umkm',
                        'description' => 'Menambahkan data UMKM baru ke sistem'
                    ],
                    'show' => [
                        'method' => 'GET',
                        'url' => $baseUrl . '/api/umkm/{id}',
                        'description' => 'Mengambil informasi detail satu UMKM'
                    ],
                    'update' => [
                        'method' => 'PUT',
                        'url' => $baseUrl . '/api/umkm/{id}',
                        'description' => 'Memperbarui informasi data profil UMKM'
                    ]
                ],
                'assessment_dss' => [
                    'create_session' => [
                        'method' => 'POST',
                        'url' => $baseUrl . '/api/assessment/create',
                        'description' => 'Menginisiasi sesi kuesioner baru untuk UMKM'
                    ],
                    'get_questions' => [
                        'method' => 'GET',
                        'url' => $baseUrl . '/api/assessment/questions?type=owner',
                        'description' => 'Mengambil daftar soal kuesioner (?type=owner|employee)'
                    ],
                    'submit_responses_v1' => [
                        'method' => 'POST',
                        'url' => $baseUrl . '/api/responses/submit',
                        'description' => 'Mengirimkan jawaban kuesioner (Jalur Endpoint Umum)'
                    ],
                    'submit_responses_v2' => [
                        'method' => 'POST',
                        'url' => $baseUrl . '/api/assessment/submit',
                        'description' => 'Mengirimkan jawaban kuesioner (Jalur Sesi Kuesioner)'
                    ],
                    'calculate_scores' => [
                        'method' => 'POST',
                        'url' => $baseUrl . '/api/assessment/{id}/calculate',
                        'description' => 'Memicu kalkulasi skor 6 faktor kesehatan organisasi via DSS'
                    ],
                    'live_monitoring' => [
                        'method' => 'GET',
                        'url' => $baseUrl . '/api/assessment/{id}/monitoring',
                        'description' => 'Memantau persentase progres pengisian kuesioner karyawan'
                    ]
                ],
                'analytics_dashboard' => [
                    'latest_radar_scores' => [
                        'method' => 'GET',
                        'url' => $baseUrl . '/api/dashboard/umkm/{id}/latest',
                        'description' => 'Mengambil skor periode terbaru untuk grafik Radar'
                    ],
                    'historical_trend' => [
                        'method' => 'GET',
                        'url' => $baseUrl . '/api/dashboard/umkm/{id}/trend',
                        'description' => 'Mengambil data historis tren nilai UMKM antar periode'
                    ],
                    'factor_breakdown' => [
                        'method' => 'GET',
                        'url' => $baseUrl . '/api/dashboard/assessment/{id}/factors',
                        'description' => 'Mengambil nilai rata-rata per-faktor kesehatan'
                    ],
                    'detailed_analysis' => [
                        'method' => 'GET',
                        'url' => $baseUrl . '/api/dashboard/assessment/{id}/details',
                        'description' => 'Mengambil rincian jawaban terperinci setiap kuesioner'
                    ],
                    'outliers_boxplot' => [
                        'method' => 'GET',
                        'url' => $baseUrl . '/api/dashboard/assessment/{id}/outliers',
                        'description' => 'Mengambil batas-batas statistik pencilan untuk grafik Boxplot'
                    ]
                ],
                'system_administration' => [
                    'run_migration_and_seed' => [
                        'method' => 'GET',
                        'url' => $baseUrl . '/run-migration',
                        'description' => 'Memicu inisialisasi tabel database cloud & impor CSV otomatis'
                    ]
                ]
            ]
        ]
    ], 200, [], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
});

// Authentication Routes (Khusus Owner)
Route::prefix('api/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout.post');
    Route::get('/me', [AuthController::class, 'me']);
});

// UMKM Routes
Route::prefix('api/umkm')->group(function () {
    Route::get('/', [UmkmController::class, 'index']);
    Route::post('/', [UmkmController::class, 'store']);
    Route::get('/{id}', [UmkmController::class, 'show']);
    Route::put('/{id}', [UmkmController::class, 'update']);
});

// Assessment & Survey Routes
Route::prefix('api/assessment')->group(function () {
    Route::post('/create', [AssessmentController::class, 'createAssessment']);
    Route::get('/questions', [AssessmentController::class, 'getQuestions']); // ?type=owner|employee
    Route::post('/submit', [AssessmentController::class, 'submitResponse']);
    Route::post('/{id}/calculate', [AssessmentController::class, 'calculateScore']);
    Route::get('/{id}/monitoring', [AssessmentController::class, 'getLiveMonitoring']);
});

// Dashboard & Charts Routes
Route::prefix('api/dashboard')->group(function () {
    Route::get('/umkm/{id}/latest', [DashboardController::class, 'getLatestScore']);
    Route::get('/umkm/{id}/trend', [DashboardController::class, 'getHistoricalTrend']);
    Route::get('/assessment/{id}/factors', [DashboardController::class, 'getFactorBreakdown']);
    Route::get('/assessment/{id}/details', [DashboardController::class, 'getDetailedAnalysis']);
    Route::get('/assessment/{id}/outliers', [DashboardController::class, 'getFactorOutliers']);
});
