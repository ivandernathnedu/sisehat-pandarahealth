<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Factor;
use App\Models\Question;
use App\Models\Recommendation;
use App\Models\Owner;
use App\Models\Umkm;
use App\Models\Assessment;
use App\Models\Response;
use App\Services\HealthService;

class ImportCsvCommand extends Command
{
    protected $signature = 'import:csv';
    protected $description = 'Import CSV files into staging tables and normalize them to main tables';

    public function handle()
    {
        $this->info('Starting CSV Import Process...');

        $importsPath = is_dir(storage_path('app/Imports')) ? storage_path('app/Imports') : storage_path('app/imports');
        // Let's also check project root 'imports' folder since we told user to put it there.
        $rootImportsPath = is_dir(base_path('Imports')) ? base_path('Imports') : base_path('imports');
        
        $path = is_dir($rootImportsPath) ? $rootImportsPath : $importsPath;

        if (!is_dir($path)) {
            $this->error("Imports directory not found at {$path}");
            return;
        }

        // 1. Truncate staging tables
        $this->info('Truncating staging tables...');
        DB::table('staging_factors')->truncate();
        DB::table('staging_questions')->truncate();
        DB::table('staging_recommendations')->truncate();
        DB::table('staging_excel_imports')->truncate();

        // 2. Load Factors
        $this->loadCsvToStaging($path . '/factors.csv', 'staging_factors', ['nama_factor', 'deskripsi', 'urutan']);
        
        // 3. Load Questions
        $this->loadCsvToStaging($path . '/questions.csv', 'staging_questions', ['nama_factor', 'pertanyaan', 'question_role', 'urutan']);

        // 4. Load Recommendations
        $this->loadCsvToStaging($path . '/recommendations.csv', 'staging_recommendations', ['nama_factor', 'category_level', 'recommendation_text']);

        // 5. Load Responses
        $responsesColumns = ['nama_owner', 'email_owner', 'nama_umkm', 'sektor_usaha', 'umur_usaha'];
        for ($i = 1; $i <= 35; $i++) {
            $responsesColumns[] = 'q' . $i;
        }
        $this->loadCsvToStaging($path . '/responses.csv', 'staging_excel_imports', $responsesColumns);

        $this->info('CSVs loaded into staging. Starting normalization...');

        $this->normalizeMasterData();
        $this->normalizeTransactionalData();

        $this->info('Import & Normalization Complete!');
    }

    private function loadCsvToStaging($filePath, $tableName, $expectedColumns)
    {
        if (!file_exists($filePath)) {
            $this->warn("File missing: {$filePath}");
            return;
        }

        $this->info("Loading {$filePath} into {$tableName}...");
        
        if (($handle = fopen($filePath, "r")) !== false) {
            $header = fgetcsv($handle, 1000, ",");
            if (!$header) {
                // Try semicolon if comma fails
                rewind($handle);
                $header = fgetcsv($handle, 1000, ";");
            }
            
            // Assume the rest are data
            $batch = [];
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                // If it split into 1 column, maybe it's semicolon separated
                if (count($data) == 1 && count($expectedColumns) > 1) {
                    $data = explode(";", $data[0]);
                }

                $row = [];
                foreach ($expectedColumns as $index => $col) {
                    $row[$col] = isset($data[$index]) ? trim($data[$index]) : null;
                }
                $batch[] = $row;

                if (count($batch) >= 100) {
                    DB::table($tableName)->insert($batch);
                    $batch = [];
                }
            }

            if (!empty($batch)) {
                DB::table($tableName)->insert($batch);
            }

            fclose($handle);
        }
    }

    private function normalizeMasterData()
    {
        $this->info('Normalizing Master Data (Factors, Questions, Recommendations)...');

        // Factors
        $stagingFactors = DB::table('staging_factors')->where('is_processed', false)->get();
        foreach ($stagingFactors as $sf) {
            Factor::updateOrCreate(
                ['nama_factor' => $sf->nama_factor],
                ['deskripsi' => $sf->deskripsi, 'urutan' => $sf->urutan]
            );
            DB::table('staging_factors')->where('id', $sf->id)->update(['is_processed' => true]);
        }

        // Questions
        $stagingQuestions = DB::table('staging_questions')->where('is_processed', false)->get();
        foreach ($stagingQuestions as $sq) {
            $factor = Factor::where('nama_factor', $sq->nama_factor)->first();
            if ($factor) {
                Question::updateOrCreate(
                    ['pertanyaan' => $sq->pertanyaan],
                    [
                        'factor_id' => $factor->factor_id,
                        'question_role' => strtolower(trim($sq->question_role)) === 'owner' ? 'owner' : 'employee',
                        'urutan' => $sq->urutan
                    ]
                );
                DB::table('staging_questions')->where('id', $sq->id)->update(['is_processed' => true]);
            }
        }

        // Recommendations
        $stagingRecs = DB::table('staging_recommendations')->where('is_processed', false)->get();
        foreach ($stagingRecs as $sr) {
            $factor = Factor::where('nama_factor', $sr->nama_factor)->first();
            if ($factor) {
                Recommendation::updateOrCreate(
                    [
                        'factor_id' => $factor->factor_id,
                        'category_level' => $sr->category_level
                    ],
                    [
                        'recommendation_text' => $sr->recommendation_text
                    ]
                );
                DB::table('staging_recommendations')->where('id', $sr->id)->update(['is_processed' => true]);
            }
        }
    }

    private function normalizeTransactionalData()
    {
        $this->info('Normalizing Transactional Data (UMKMs, Assessments, Responses)...');

        // Cache questions mapped by urutan
        $questionsByUrutan = Question::all()->keyBy('urutan');
        $healthService = new HealthService();

        $stagingResponses = DB::table('staging_excel_imports')->where('is_processed', false)->get();
        
        $bar = $this->output->createProgressBar(count($stagingResponses));
        $bar->start();

        foreach ($stagingResponses as $sr) {
            // Owner
            $owner = Owner::firstOrCreate(
                ['email' => $sr->email_owner],
                ['name' => $sr->nama_owner]
            );

            // UMKM
            $umkm = Umkm::firstOrCreate(
                ['nama_umkm' => $sr->nama_umkm, 'owner_id' => $owner->owner_id],
                ['sektor_usaha' => $sr->sektor_usaha, 'umur_usaha' => $sr->umur_usaha]
            );

            // Assessment
            $assessment = Assessment::create([
                'umkm_id' => $umkm->umkm_id,
                'assessment_name' => 'Assessment for ' . $sr->nama_umkm,
                'token' => \Illuminate\Support\Str::random(10),
                'tanggal_mulai' => now(),
                'tanggal_selesai' => now(),
                'status' => 'closed'
            ]);

            // Responses
            $responsesToInsert = [];
            for ($i = 1; $i <= 35; $i++) {
                $qCol = 'q' . $i;
                $answerValue = $sr->$qCol;

                if ($answerValue !== null && isset($questionsByUrutan[$i])) {
                    $question = $questionsByUrutan[$i];
                    $responsesToInsert[] = [
                        'assessment_id' => $assessment->assessment_id,
                        'question_id' => $question->question_id,
                        'respondent_type' => $question->question_role,
                        'employee_code' => 'EMP-' . $owner->owner_id, // Dummy employee code for imported data
                        'answer_value' => $answerValue,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($responsesToInsert)) {
                Response::insert($responsesToInsert);
            }

            // Calculate Health Score
            $healthService->calculate($assessment->assessment_id);

            DB::table('staging_excel_imports')->where('id', $sr->id)->update(['is_processed' => true]);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }
}
