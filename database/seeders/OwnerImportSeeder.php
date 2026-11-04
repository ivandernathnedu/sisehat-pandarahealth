<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Owner;

class OwnerImportSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = base_path('Imports/owners.csv');
        
        if (!file_exists($filePath)) {
            $this->command->error("File tidak ditemukan di: " . $filePath);
            return;
        }

        $file = fopen($filePath, "r");
        $header = fgetcsv($file); // Ambil header (name, email, gender)

        $countUpdated = 0;
        $countCreated = 0;

        while (($row = fgetcsv($file)) !== FALSE) {
            $data = array_combine($header, $row);

            // Mapping Gender: 1/male -> laki-laki, 2/female -> perempuan
            $gender = null;
            $rawGender = strtolower(trim((string)$data['gender']));
            if ($rawGender === '1' || $rawGender === 'male' || $rawGender === 'l' || $rawGender === 'laki-laki') {
                $gender = 'laki-laki';
            } elseif ($rawGender === '2' || $rawGender === 'female' || $rawGender === 'p' || $rawGender === 'perempuan') {
                $gender = 'perempuan';
            } else {
                $gender = $data['gender']; // Jika sudah berbentuk text
            }

            // Cari berdasarkan email
            $owner = Owner::where('email', $data['email'])->first();

            if ($owner) {
                // UPDATE GENDER
                $owner->update([
                    'gender' => $gender
                ]);
                $countUpdated++;
            } else {
                // CREATE NEW (Jika belum ada)
                Owner::create([
                    'name' => $data['name'] ?? 'Owner Baru',
                    'email' => $data['email'],
                    'gender' => $gender,
                    'password' => Hash::make('password123'), // Default password
                ]);
                $countCreated++;
            }
        }

        fclose($file);

        $this->command->info("Proses selesai!");
        $this->command->info("Data diupdate: " . $countUpdated);
        $this->command->info("Data dibuat baru: " . $countCreated);
    }
}
