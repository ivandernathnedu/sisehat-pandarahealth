<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $translations = [
            1 => "Apakah bisnis Anda memiliki Nomor Induk Berusaha (NIB)?",
            2 => "Apakah Anda pernah menerima bantuan dana pemerintah untuk mendukung bisnis Anda?",
            3 => "Apakah Anda pernah mengikuti kegiatan yang diselenggarakan pemerintah (misal: pelatihan, lokakarya, atau pameran)?",
            4 => "Apakah Anda berkolaborasi dengan pedagang atau bisnis lain untuk mengembangkan bisnis Anda?",
            5 => "Apakah Anda menggunakan pemasaran digital (misal: media sosial, website, atau iklan online) untuk mempromosikan bisnis Anda?",
            6 => "Apa sumber modal utama untuk bisnis Anda?",
            7 => "Pemimpin Anda peduli terhadap karyawan secara personal",
            8 => "Pemimpin Anda bertindak sebagai panutan bagi karyawan",
            9 => "Pemimpin Anda menerapkan ide atau saran dari karyawan",
            10 => "Pemimpin Anda terlibat langsung dalam pekerjaan sehari-hari",
            11 => "Pemimpin Anda secara aktif berpartisipasi dalam melatih karyawan baru",
            12 => "Pemimpin Anda memperlakukan karyawan secara adil saat memberikan hukuman dan penghargaan",
            13 => "Tempat kerja bebas dari kecelakaan terkait pekerjaan",
            14 => "Saya puas dengan jam kerja yang ditetapkan di perusahaan ini",
            15 => "Beban kerja saya dapat dikelola dengan baik",
            16 => "Kondisi fisik tempat kerja (misal: kebisingan, pencahayaan, suhu) nyaman",
            17 => "Sebagian besar karyawan bertanggung jawab dan jarang membolos tanpa alasan yang sah",
            18 => "Saya merasa bebas untuk membicarakan kesulitan terkait pekerjaan dengan pimpinan atau rekan kerja saya",
            19 => "Suasana di tempat kerja saya positif dan suportif",
            20 => "Semangat kerja karyawan di tempat kerja saya secara umum tinggi",
            21 => "Karyawan di tempat kerja saya saling membantu saat dibutuhkan",
            22 => "Tingkat kepercayaan antar karyawan di tempat kerja saya sangat kuat",
            23 => "Kerja sama tim di tempat kerja saya efektif",
            24 => "Karyawan di tempat kerja saya saling memperlakukan dengan hormat",
            25 => "Karyawan di tempat kerja saya memandang pekerjaan mereka sebagai bentuk dedikasi atau ibadah",
            26 => "Modal kerja memadai untuk operasi bisnis sehari-hari",
            27 => "Perusahaan mampu membayar gaji karyawan tepat waktu",
            28 => "Peralatan/mesin dalam kondisi baik dan berfungsi optimal",
            29 => "Rantai pasokan bisnis berjalan lancar tanpa gangguan signifikan",
            30 => "Permintaan untuk produk/layanan dapat diprediksi",
            31 => "Kualitas hubungan jangka panjang bisnis dengan pelanggan sangat baik",
            32 => "Kemampuan bisnis kami untuk menjangkau pasar atau pelanggan baru sangat baik",
            33 => "Pertumbuhan penjualan bisnis kami selama setahun terakhir sangat memuaskan",
            34 => "Kewajiban bisnis kami (misalnya, pinjaman, hutang) sangat dapat dikelola",
            35 => "Situasi arus kas bisnis kami sangat sehat dan stabil"
        ];

        foreach ($translations as $id => $indonesianText) {
            DB::table('questions')
                ->where('question_id', $id)
                ->update(['pertanyaan' => $indonesianText]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indonesian', function (Blueprint $table) {
            //
        });
    }
};
