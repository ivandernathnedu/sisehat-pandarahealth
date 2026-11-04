<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Owner
        $ownerId = DB::table('owners')->insertGetId([
            'name' => 'Admin Owner',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'created_at' => Carbon::now()
        ]);

        // 2. Create UMKM
        $umkmId1 = DB::table('umkms')->insertGetId([
            'owner_id' => $ownerId,
            'nama_umkm' => 'PT Nusantara Tech',
            'bidang_usaha' => 'Teknologi',
            'alamat' => 'Jakarta Selatan',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $umkmId2 = DB::table('umkms')->insertGetId([
            'owner_id' => $ownerId,
            'nama_umkm' => 'Warung Maju Bersama',
            'bidang_usaha' => 'F&B',
            'alamat' => 'Bandung',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        // 3. Create Assessment
        $assessmentId1 = DB::table('assessments')->insertGetId([
            'umkm_id' => $umkmId1,
            'assessment_name' => 'Q1 2026 Health Check',
            'assessment_date' => Carbon::now()->subMonths(1),
            'status' => 'completed',
            'created_at' => Carbon::now()
        ]);

        $assessmentId2 = DB::table('assessments')->insertGetId([
            'umkm_id' => $umkmId2,
            'assessment_name' => 'Q1 2026 Assessment',
            'assessment_date' => Carbon::now()->subDays(10),
            'status' => 'in_progress',
            'created_at' => Carbon::now()
        ]);

        // 4. Create Responses for Assessment 1
        $questions = DB::table('questions')->get();
        foreach ($questions as $q) {
            DB::table('responses')->insert([
                'assessment_id' => $assessmentId1,
                'question_id' => $q->question_id,
                'respondent_type' => $q->answered_by,
                'employee_code' => $q->answered_by === 'employee' ? 'EMP-001' : null,
                'answer_value' => rand(3, 5),
                'created_at' => Carbon::now()
            ]);
        }

        // 5. Create Health Score for Assessment 1
        DB::table('health_scores')->insert([
            'assessment_id' => $assessmentId1,
            'overall_score' => 85.5,
            'category' => 'Sangat Sehat',
            'calculated_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        // 6. Create Factor Scores for Assessment 1
        $factors = DB::table('factors')->get();
        foreach ($factors as $factor) {
            $score = rand(60, 95);
            $category = $score >= 80 ? 'Sangat Baik' : ($score >= 60 ? 'Cukup' : 'Buruk');
            
            DB::table('factor_scores')->insert([
                'assessment_id' => $assessmentId1,
                'factor_id' => $factor->factor_id,
                'score' => $score,
                'category' => $category,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        // 7. Seed Recommendation rules if not exist
        if (DB::table('recommendations')->count() == 0) {
            $recId1 = DB::table('recommendations')->insertGetId([
                'factor_id' => 1,
                'category_level' => 'Buruk',
                'recommendation_text' => 'Perkuat nilai organisasi melalui sosialisasi.',
                'suggested_action' => 'Buat sesi training nilai perusahaan.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            DB::table('assessment_recommendations')->insert([
                'assessment_id' => $assessmentId1,
                'recommendation_id' => $recId1,
                'generated_at' => Carbon::now()
            ]);
        }

    }
}
