<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\Response;
use App\Models\Umkm;
use App\Models\Owner;

class ExportCsvCommand extends Command
{
    protected $signature = 'db:export-csv';
    protected $description = 'Eksplorasi dan ekspor data database lokal Laragon menjadi berkas CSV di folder Imports';

    public function handle()
    {
        $this->info('Starting database export to CSV files...');

        $importsPath = is_dir(base_path('Imports')) ? base_path('Imports') : base_path('imports');
        if (!is_dir($importsPath)) {
            mkdir($importsPath, 0755, true);
        }

        // 1. Export Factors
        $this->info('Exporting factors.csv...');
        $factors = DB::table('factors')->orderBy('urutan')->get();
        $factorsFile = fopen($importsPath . '/factors.csv', 'w');
        fputcsv($factorsFile, ['nama_factor', 'deskripsi', 'urutan']);
        foreach ($factors as $factor) {
            fputcsv($factorsFile, [$factor->nama_factor, $factor->deskripsi, $factor->urutan]);
        }
        fclose($factorsFile);

        // 2. Export Questions
        $this->info('Exporting questions.csv...');
        $questions = DB::table('questions')
            ->join('factors', 'questions.factor_id', '=', 'factors.factor_id')
            ->select('factors.nama_factor', 'questions.pertanyaan', 'questions.question_role', 'questions.urutan')
            ->orderBy('questions.urutan')
            ->get();
        $questionsFile = fopen($importsPath . '/questions.csv', 'w');
        fputcsv($questionsFile, ['nama_factor', 'pertanyaan', 'question_role', 'urutan']);
        foreach ($questions as $q) {
            fputcsv($questionsFile, [$q->nama_factor, $q->pertanyaan, $q->question_role, $q->urutan]);
        }
        fclose($questionsFile);

        // 3. Export Recommendations
        $this->info('Exporting recommendations.csv...');
        $recommendations = DB::table('recommendations')
            ->join('factors', 'recommendations.factor_id', '=', 'factors.factor_id')
            ->select('factors.nama_factor', 'recommendations.category_level', 'recommendations.recommendation_text')
            ->get();
        $recsFile = fopen($importsPath . '/recommendations.csv', 'w');
        fputcsv($recsFile, ['nama_factor', 'category_level', 'recommendation_text']);
        foreach ($recommendations as $rec) {
            fputcsv($recsFile, [$rec->nama_factor, $rec->category_level, $rec->recommendation_text]);
        }
        fclose($recsFile);

        // 4. Export Owners (Data Master Akun)
        $this->info('Exporting owners.csv...');
        $owners = DB::table('owners')->get();
        $ownersFile = fopen($importsPath . '/owners.csv', 'w');
        fputcsv($ownersFile, ['name', 'email', 'gender']);
        foreach ($owners as $owner) {
            fputcsv($ownersFile, [$owner->name, $owner->email, $owner->gender]);
        }
        fclose($ownersFile);

        // 5. Export Responses & Assessments (responses.csv)
        $this->info('Exporting responses.csv (Transactional Data)...');
        
        // Ambil semua assessment yang memiliki jawaban
        $assessments = Assessment::with(['umkm.owner'])->get();
        
        $responsesFile = fopen($importsPath . '/responses.csv', 'w');
        
        // Header responses.csv
        $header = ['nama_owner', 'email_owner', 'nama_umkm', 'sektor_usaha', 'umur_usaha'];
        for ($i = 1; $i <= 35; $i++) {
            $header[] = 'q' . $i;
        }
        fputcsv($responsesFile, $header);

        $questionsByUrutan = Question::all()->keyBy('urutan');
        $exportCount = 0;

        foreach ($assessments as $assessment) {
            $umkm = $assessment->umkm;
            if (!$umkm || !$umkm->owner) continue;

            // Cari semua jawaban kuesioner untuk assessment ini
            $responses = Response::where('assessment_id', $assessment->assessment_id)->get();
            if ($responses->isEmpty()) continue;

            // Buat row data
            $row = [
                $umkm->owner->name,
                $umkm->owner->email,
                $umkm->nama_umkm,
                $umkm->sektor_usaha,
                $umkm->umur_usaha
            ];

            // Petakan jawaban q1-q35
            for ($i = 1; $i <= 35; $i++) {
                $q = $questionsByUrutan->get($i);
                if ($q) {
                    $answer = $responses->where('question_id', $q->question_id)->first();
                    $row[] = $answer ? $answer->answer_value : null;
                } else {
                    $row[] = null;
                }
            }

            fputcsv($responsesFile, $row);
            $exportCount++;
        }

        fclose($responsesFile);

        $this->info("Success! Exported {$exportCount} assessment records into responses.csv.");
        $this->info("All files are updated in the " . basename($importsPath) . " directory.");
    }
}
