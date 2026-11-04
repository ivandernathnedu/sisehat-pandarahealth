<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentToken;
use App\Models\Question;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeAssessmentController extends Controller
{
    public function show($token)
    {
        $tokenRecord = AssessmentToken::where('token', $token)->where('is_active', true)->first();

        if (!$tokenRecord) {
            return view('pages.error-link', ['message' => 'Tautan tidak valid atau sudah tidak aktif.']);
        }

        $assessment = Assessment::find($tokenRecord->assessment_id);
        
        if (!$assessment) {
            return view('pages.error-link', ['message' => 'Sesi asesmen tidak ditemukan.']);
        }

        // Hitung kuota 100%
        $totalRespondents = Response::where('assessment_id', $assessment->assessment_id)
            ->where('respondent_type', 'employee')
            ->distinct('employee_code')
            ->count('employee_code');

        if ($assessment->jumlah_karyawan > 0 && $totalRespondents >= $assessment->jumlah_karyawan) {
            return view('pages.error-link', ['message' => 'Tautan Kadaluarsa: Seluruh karyawan telah mengisi asesmen ini.']);
        }

        // Ambil pertanyaan untuk role employee dikelompokkan per faktor (Hanya yang ada pertanyaan untuk employee)
        $factors = \App\Models\Factor::whereHas('questions', function($q) {
            $q->where('question_role', 'employee');
        })->with(['questions' => function($q) {
            $q->where('question_role', 'employee')->orderBy('urutan');
        }])->get();
        
        // Hitung progres (dummy atau dinamis, misal 0% di awal)
        $progress = 0; 

        return view('pages.employee-assessment', compact('token', 'factors', 'assessment', 'progress', 'totalRespondents'));
    }

    public function store(Request $request, $token)
    {
        $tokenRecord = AssessmentToken::where('token', $token)->where('is_active', true)->first();
        if (!$tokenRecord) return response()->json(['error' => 'Link expired'], 403);

        $assessment = Assessment::find($tokenRecord->assessment_id);
        
        $request->validate([
            'employee_code' => 'required|string|max:50',
            'answers' => 'required|array',
            'answers.*' => 'required|integer|min:1'
        ]);

        // Cek apakah sudah pernah mengisi (optional, based on employee_code)
        $alreadyResponded = Response::where('assessment_id', $assessment->assessment_id)
            ->where('employee_code', $request->employee_code)
            ->exists();

        if ($alreadyResponded) {
            return response()->json(['error' => 'ID Karyawan ini sudah digunakan untuk mengisi asesmen ini.'], 422);
        }

        DB::transaction(function () use ($request, $assessment) {
            foreach ($request->answers as $question_id => $value) {
                Response::create([
                    'assessment_id' => $assessment->assessment_id,
                    'question_id' => $question_id,
                    'respondent_type' => 'employee',
                    'employee_code' => $request->employee_code,
                    'answer_value' => $value
                ]);
            }
        });

        return response()->json(['message' => 'Terima kasih! Jawaban Anda telah berhasil dikirim.']);
    }
}
