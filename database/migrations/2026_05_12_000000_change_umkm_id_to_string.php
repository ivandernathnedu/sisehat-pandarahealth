<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        // Untuk MySQL: cek apakah kolom sudah varchar
        if ($driver === 'mysql') {
            $columnInfo = DB::select("SHOW COLUMNS FROM umkms LIKE 'umkm_id'")[0];
            $isAlreadyString = str_contains($columnInfo->Type, 'varchar');
        } else {
            // SQLite atau driver lain: anggap sudah string (skip konversi)
            $isAlreadyString = true;
        }

        if (!$isAlreadyString) {
            // Ambil data lama hanya jika masih integer
            $oldUmkms = DB::table('umkms')->get();

            // 2. Ubah tipe data di assessments dahulu (FK)
            Schema::table('assessments', function (Blueprint $table) {
                $table->string('umkm_id', 10)->nullable()->change();
            });

            // 3. Ubah umkm_id di tabel umkms (MySQL only)
            DB::statement('ALTER TABLE umkms MODIFY umkm_id INT NOT NULL');
            DB::statement('ALTER TABLE umkms DROP PRIMARY KEY');
            DB::statement('ALTER TABLE umkms MODIFY umkm_id VARCHAR(10) NOT NULL PRIMARY KEY');

            // 4. Migrasi Data
            foreach ($oldUmkms as $umkm) {
                $newId = 'UMKM' . str_pad($umkm->umkm_id, 3, '0', STR_PAD_LEFT);
                DB::table('umkms')->where('umkm_id', (string)$umkm->umkm_id)->update(['umkm_id' => $newId]);
                DB::table('assessments')->where('umkm_id', (string)$umkm->umkm_id)->update(['umkm_id' => $newId]);
            }
        } else {
            echo "Kolom umkm_id sudah bertipe string atau bukan MySQL. Melewati perubahan skema.\n";
        }
    }

    public function down(): void
    {
        // Untuk rollback (opsional, namun sebaiknya dikembalikan ke int jika diperlukan)
        // Karena data sudah berubah format string, rollback otomatis cukup berisiko data loss
        // Jadi kita biarkan string atau lakukan konversi balik jika benar-benar dibutuhkan.
    }
};
