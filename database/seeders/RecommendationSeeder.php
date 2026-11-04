<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecommendationSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan foreign key checks agar bisa menghapus data dengan aman
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        // Bersihkan tabel terkait sebelum diisi ulang
        DB::table('recommendations')->truncate();
        DB::table('assessment_recommendations')->truncate();

        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $now = Carbon::now();

        $recommendations = [
            // Factor 1: Kepemimpinan
            [
                'factor_id' => 1,
                'category_level' => 'KURANG_SEHAT',
                'recommendation_text' => 'Karyawan merasa pimpinan sangat tidak peduli dan tidak ada keterlibatan dalam operasional tim.',
                'suggested_action' => 'Segera lakukan dialog terbuka dengan tim. Pimpinan harus mulai menunjukkan kehadiran fisik dan emosional dalam pekerjaan sehari-hari.',
            ],
            [
                'factor_id' => 1,
                'category_level' => 'CUKUP_SEHAT',
                'recommendation_text' => 'Kepemimpinan dirasakan kurang konsisten. Arahan sering membingungkan atau kurangnya apresiasi terhadap kerja keras karyawan.',
                'suggested_action' => 'Adakan pertemuan mingguan rutin untuk menyamakan persepsi dan mulai berikan pujian atau feedback positif secara berkala.',
            ],
            [
                'factor_id' => 1,
                'category_level' => 'SEHAT',
                'recommendation_text' => 'Pimpinan sudah cukup baik, namun delegasi tugas dan pengembangan bakat karyawan belum maksimal.',
                'suggested_action' => 'Mulai identifikasi karyawan potensial dan berikan pelatihan atau tanggung jawab baru untuk meningkatkan kemampuan mereka.',
            ],
            [
                'factor_id' => 1,
                'category_level' => 'SANGAT_SEHAT',
                'recommendation_text' => 'Kepemimpinan sangat kuat dan menjadi panutan yang baik bagi seluruh anggota tim.',
                'suggested_action' => 'Pertahankan gaya komunikasi yang ada. Fokus pada persiapan regenerasi pimpinan di masa depan.',
            ],

            // Factor 2: Kondisi Lingkungan Kerja
            [
                'factor_id' => 2,
                'category_level' => 'KURANG_SEHAT',
                'recommendation_text' => 'Lingkungan kerja dirasakan tidak aman atau sangat membuat stres karena beban kerja yang berlebihan.',
                'suggested_action' => 'Segera evaluasi beban kerja individu. Pastikan aspek keselamatan dan kesehatan kerja (K3) menjadi prioritas utama.',
            ],
            [
                'factor_id' => 2,
                'category_level' => 'CUKUP_SEHAT',
                'recommendation_text' => 'Fasilitas fisik kantor atau jam kerja seringkali dikeluhkan dan mulai mengganggu produktivitas.',
                'suggested_action' => 'Perbaiki fasilitas yang rusak (lampu, AC, kursi) dan pastikan karyawan memiliki waktu istirahat yang cukup.',
            ],
            [
                'factor_id' => 2,
                'category_level' => 'SEHAT',
                'recommendation_text' => 'Kondisi kerja stabil, namun variasi tugas atau kenyamanan tambahan masih bisa ditingkatkan.',
                'suggested_action' => 'Pertimbangkan untuk menata ulang ruang kerja agar lebih segar atau memberikan opsi waktu kerja yang lebih fleksibel.',
            ],
            [
                'factor_id' => 2,
                'category_level' => 'SANGAT_SEHAT',
                'recommendation_text' => 'Lingkungan kerja sangat mendukung produktivitas dan kesejahteraan karyawan terjamin.',
                'suggested_action' => 'Lakukan survei kepuasan berkala untuk menjaga standar kenyamanan yang sudah sangat baik ini.',
            ],

            // Factor 3: Kohesi Sosial & Moral
            [
                'factor_id' => 3,
                'category_level' => 'KURANG_SEHAT',
                'recommendation_text' => 'Terjadi perpecahan atau konflik internal yang parah antar karyawan, kerjasama tim hampir tidak ada.',
                'suggested_action' => 'Lakukan audit budaya kerja. Intervensi pimpinan diperlukan untuk meredam konflik dan membangun kembali rasa percaya.',
            ],
            [
                'factor_id' => 3,
                'category_level' => 'CUKUP_SEHAT',
                'recommendation_text' => 'Komunikasi antar karyawan formal dan kaku. Kurang adanya inisiatif untuk saling membantu.',
                'suggested_action' => 'Fasilitasi kegiatan informal bersama (seperti makan siang bersama) untuk membangun kedekatan personal antar anggota tim.',
            ],
            [
                'factor_id' => 3,
                'category_level' => 'SEHAT',
                'recommendation_text' => 'Hubungan kerja cukup harmonis, namun semangat kerja tim cenderung fluktuatif.',
                'suggested_action' => 'Ciptakan target-target kecil yang bisa dicapai bersama untuk membangkitkan kembali semangat kolaborasi.',
            ],
            [
                'factor_id' => 3,
                'category_level' => 'SANGAT_SEHAT',
                'recommendation_text' => 'Budaya saling menghormati dan kerjasama tim sangat kuat. Semangat kerja sangat tinggi.',
                'suggested_action' => 'Gunakan tim yang solid ini untuk membantu proses on-boarding karyawan baru agar cepat beradaptasi dengan budaya positif.',
            ],

            // Factor 4: Operasional & Rantai Pasok
            [
                'factor_id' => 4,
                'category_level' => 'KURANG_SEHAT',
                'recommendation_text' => 'Rantai pasok sering terputus dan mesin produksi sering rusak, menghambat kelangsungan bisnis.',
                'suggested_action' => 'Audit seluruh vendor dan kondisi mesin. Pastikan ada dana darurat untuk perbaikan mesin vital.',
            ],
            [
                'factor_id' => 4,
                'category_level' => 'CUKUP_SEHAT',
                'recommendation_text' => 'Efisiensi operasional rendah. Masih banyak pemborosan waktu atau bahan baku dalam proses harian.',
                'suggested_action' => 'Mulai terapkan standar operasional (SOP) yang lebih ketat dan pantau penggunaan bahan baku secara harian.',
            ],
            [
                'factor_id' => 4,
                'category_level' => 'SEHAT',
                'recommendation_text' => 'Operasional berjalan lancar, namun teknologi yang digunakan mulai tertinggal dari kompetitor.',
                'suggested_action' => 'Lakukan riset teknologi sederhana atau software yang dapat membantu mempercepat proses administrasi atau produksi.',
            ],
            [
                'factor_id' => 4,
                'category_level' => 'SANGAT_SEHAT',
                'recommendation_text' => 'Sistem operasional dan rantai pasok sangat handal dan efisien.',
                'suggested_action' => 'Pertimbangkan untuk melakukan sertifikasi kualitas (seperti ISO sederhana) untuk meningkatkan nilai jual bisnis.',
            ],

            // Factor 5: Penjualan & Hubungan Pelanggan
            [
                'factor_id' => 5,
                'category_level' => 'KURANG_SEHAT',
                'recommendation_text' => 'Bisnis kehilangan pelanggan setia secara drastis dan tidak ada strategi pemasaran yang berjalan.',
                'suggested_action' => 'Segera hubungi kembali pelanggan lama untuk menanyakan keluhan mereka. Lakukan promosi darurat untuk menarik traffic.',
            ],
            [
                'factor_id' => 5,
                'category_level' => 'CUKUP_SEHAT',
                'recommendation_text' => 'Upaya pemasaran hanya mengandalkan pelanggan lama tanpa adanya penambahan pasar baru.',
                'suggested_action' => 'Mulai aktif di platform digital atau marketplace. Targetkan segmen pasar baru yang belum tergarap.',
            ],
            [
                'factor_id' => 5,
                'category_level' => 'SEHAT',
                'recommendation_text' => 'Pertumbuhan penjualan stabil, namun loyalitas pelanggan jangka panjang belum terukur dengan baik.',
                'suggested_action' => 'Buat database pelanggan dan kirimkan ucapan atau promo khusus di hari-hari tertentu untuk membangun kedekatan.',
            ],
            [
                'factor_id' => 5,
                'category_level' => 'SANGAT_SEHAT',
                'recommendation_text' => 'Hubungan dengan pelanggan sangat baik dan strategi pemasaran sangat efektif.',
                'suggested_action' => 'Gunakan testimoni pelanggan puas sebagai materi iklan untuk memperluas jangkauan brand secara organik.',
            ],

            // Factor 6: Finansial & Arus Kas
            [
                'factor_id' => 6,
                'category_level' => 'KURANG_SEHAT',
                'recommendation_text' => 'Bisnis berada dalam ancaman kebangkrutan karena arus kas yang sangat buruk dan hutang menumpuk.',
                'suggested_action' => 'Lakukan restrukturisasi keuangan total. Kurangi biaya tetap dan fokus pada penjualan tunai (tanpa hutang piutang).',
            ],
            [
                'factor_id' => 6,
                'category_level' => 'CUKUP_SEHAT',
                'recommendation_text' => 'Margin keuntungan sangat kecil sehingga bisnis sulit untuk berkembang atau menabung.',
                'suggested_action' => 'Tinjau ulang harga jual dan biaya produksi. Pastikan ada selisih keuntungan yang cukup untuk operasional masa depan.',
            ],
            [
                'factor_id' => 6,
                'category_level' => 'SEHAT',
                'recommendation_text' => 'Keuangan stabil namun belum memiliki perencanaan investasi atau cadangan dana darurat yang kuat.',
                'suggested_action' => 'Mulai alokasikan profit untuk tabungan dana darurat (minimal 3 bulan biaya operasional).',
            ],
            [
                'factor_id' => 6,
                'category_level' => 'SANGAT_SEHAT',
                'recommendation_text' => 'Struktur keuangan sangat sehat dengan arus kas yang kuat dan pertumbuhan modal yang konsisten.',
                'suggested_action' => 'Optimalkan dana menganggur (idle cash) untuk ekspansi bisnis atau investasi aset produktif lainnya.',
            ],
        ];

        $insertData = [];
        foreach ($recommendations as $rec) {
            $rec['created_at'] = $now;
            $rec['updated_at'] = $now;
            $insertData[] = $rec;
        }

        DB::table('recommendations')->insert($insertData);
    }
}
