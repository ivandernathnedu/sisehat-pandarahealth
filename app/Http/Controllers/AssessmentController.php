<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Question;
use App\Models\Response;
use App\Models\Umkm;
use App\Models\AssessmentToken;
use App\Services\HealthService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    protected $healthService;

    public function __construct(HealthService $healthService)
    {
        $this->healthService = $healthService;
    }

    /**
     * Owner membuat sesi assessment baru
     */
    public function createAssessment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'umkm_id' => 'required|exists:umkms,umkm_id',
            'assessment_name' => 'nullable|string|max:150',
            'jumlah_karyawan' => 'required|integer|min:1',
            'expiry_days' => 'nullable|integer|min:1' // Masa berlaku link
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $assessment = Assessment::where('umkm_id', $request->umkm_id)
            ->whereIn('status', ['draft', 'open'])
            ->latest()
            ->first();

        if ($assessment && $assessment->status == 'draft') {
            return response()->json(['error' => 'Owner belum menyelesaikan pengisian asesmen. Silakan selesaikan terlebih dahulu.'], 422);
        }

        if (!$assessment) {
            $assessment = Assessment::create([
                'umkm_id' => $request->umkm_id,
                'assessment_name' => $request->assessment_name ?? 'Asesmen ' . now()->format('M Y'),
                'jumlah_karyawan' => $request->jumlah_karyawan ?? 0,
                'tanggal_mulai' => now()->toDateString(),
                'status' => 'open'
            ]);
        }

        // FIND OR GENERATE TOKEN
        $tokenRecord = AssessmentToken::where('assessment_id', $assessment->assessment_id)
            ->where('is_active', true)
            ->where('expired_at', '>', now())
            ->first();

        if (!$tokenRecord) {
            $token = 'UA-' . Str::random(30);
            $expiryDays = $request->input('expiry_days', 7);

            $tokenRecord = AssessmentToken::create([
                'assessment_id' => $assessment->assessment_id,
                'token' => $token,
                'expired_at' => now()->addDays($expiryDays),
                'is_active' => true
            ]);
        }

        $token = $tokenRecord->token;

        return response()->json([
            'message' => 'Assessment berhasil dibuat.',
            'data' => $assessment,
            'employee_link' => url("/employee-assessment/{$token}")
        ], 201);
    }

    /**
     * Mengambil daftar pertanyaan berdasarkan tipe (?type=owner|employee)
     */
    public function getQuestions(Request $request)
    {
        $type = $request->query('type', 'owner');
        
        if (!in_array($type, ['owner', 'employee'])) {
            $type = 'owner';
        }

        $questions = Question::where('question_role', $type)->get();

        return response()->json($questions);
    }

    /**
     * Mengambil pertanyaan berdasarkan token (untuk Employee)
     */
    public function getQuestionsByToken($token)
    {
        $tokenRecord = AssessmentToken::active()->where('token', $token)->first();

        if (!$tokenRecord) {
            return response()->json(['error' => 'Link survey tidak valid atau sudah kadaluarsa.'], 403);
        }

        $questions = Question::where('question_role', 'employee')->get();

        return response()->json([
            'assessment_id' => $tokenRecord->assessment_id,
            'data' => $questions
        ]);
    }

    /**
     * Submit jawaban survey
     */
    public function submitResponse(Request $request)
    {
        // Validasi tetap sama, tapi sekarang kita bisa verifikasi token jika employee
        $validator = Validator::make($request->all(), [
            'assessment_id' => 'required|exists:assessments,assessment_id',
            'respondent_type' => 'required|in:owner,employee',
            'employee_code' => 'nullable|string',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,question_id',
            'answers.*.answer_value' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $responsesToInsert = [];
        foreach ($request->answers as $answer) {
            $responsesToInsert[] = [
                'assessment_id' => $request->assessment_id,
                'question_id' => $answer['question_id'],
                'respondent_type' => $request->respondent_type,
                'employee_code' => $request->employee_code,
                'answer_value' => $answer['answer_value'],
                'created_at' => now(),
            ];
        }

        Response::insert($responsesToInsert);

        return response()->json(['message' => 'Jawaban berhasil disimpan.'], 200);
    }

    public function calculateScore($id)
    {
        $assessment = Assessment::find($id);
        if (!$assessment) return response()->json(['error' => 'Not found'], 404);

        $healthScore = $this->healthService->calculate($id);
        $assessment->update(['status' => 'completed']);

        return response()->json(['message' => 'Success', 'data' => $healthScore]);
    }

    public function getLiveMonitoring($id)
    {
        $assessment = Assessment::find($id);
        if (!$assessment) return response()->json(['error' => 'Not found'], 404);

        $totalEmployees = Response::where('assessment_id', $id)
            ->where('respondent_type', 'employee')
            ->distinct('employee_code')
            ->count('employee_code');

        $hasOwner = Response::where('assessment_id', $id)
            ->where('respondent_type', 'owner')
            ->exists() ? 1 : 0;

        $totalRespondents = $totalEmployees + $hasOwner;

        $estimatedScoreData = $this->healthService->calculate($id);

        $recentActivity = Response::where('assessment_id', $id)
            ->where('respondent_type', 'employee')
            ->select('employee_code', DB::raw('MAX(created_at) as last_active'))
            ->groupBy('employee_code')
            ->orderBy('last_active', 'desc')
            ->limit(4)
            ->get();

        return response()->json([
            'total_respondents' => $totalRespondents,
            'estimated_score' => round($estimatedScoreData->overall_score, 2),
            'estimated_category' => $estimatedScoreData->category,
            'recent_activity' => $recentActivity
        ]);
    }
}
