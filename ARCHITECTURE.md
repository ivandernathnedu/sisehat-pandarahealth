# Dokumentasi Arsitektur SiSehat

Dokumen ini menjelaskan arsitektur teknis dan pola desain yang digunakan dalam platform SiSehat (Sistem Informasi Kesehatan UMKM).

## 1. Gambaran Umum
SiSehat adalah platform berbasis web yang dirancang untuk mengukur kesehatan organisasi UMKM melalui sistem survei dua perspektif: Pemilik (Owner) dan Karyawan (Employee). Platform ini menggunakan logika *Decision Support System* (DSS) untuk menghitung skor kesehatan di berbagai faktor organisasi.

## 2. Teknologi yang Digunakan
- **Framework**: Laravel 11.x (PHP 8.2+)
- **Database**: MySQL (MariaDB)
- **Frontend**: Blade Templating Engine, Vanilla CSS (Aestetik Modern Dark Mode), JavaScript (ES6+)
- **Grafik**: Chart.js untuk visualisasi (Radar, Gauge, Doughnut)

## 3. Arsitektur Inti (MVC)
Aplikasi ini mengikuti pola standar *Model-View-Controller*:

- **Models**: Terletak di `app/Models/`. Mendefinisikan struktur data dan relasi antar tabel (contoh: `Umkm`, `Assessment`, `Question`, `Factor`).
- **Views**: Terletak di `resources/views/`. Menggunakan template Blade dengan sistem layout modular (`layouts/app.blade.php`).
- **Controllers**: Terletak di `app/Http/Controllers/`. `DashboardController` menangani logika kompleks untuk pemilik, sedangkan `EmployeeAssessmentController` menangani input data dari karyawan.

## 4. Komponen Kunci

### A. Mesin Kalkulasi Kesehatan (`HealthService`)
Terletak di `app/Services/HealthService.php`. Ini adalah otak dari platform:
- **Normalisasi**: Mengubah jawaban skala Likert mentah (1-5) menjadi skala persentase 0-100.
- **Agregasi**: Menggabungkan perspektif Pemilik dan Karyawan (bobot 50:50) per pertanyaan.
- **Skor Faktor**: Mengelompokkan pertanyaan ke dalam 6 faktor organisasi:
  1. *Institutional Resources* (Sumber Daya Institusional)
  2. *Leader Involvement* (Keterlibatan Pemimpin)
  3. *Workplace Quality* (Kualitas Lingkungan Kerja)
  4. *Organizational Values* (Nilai-nilai Organisasi)
  5. *Operational Stability* (Stabilitas Operasional)
  6. *Economic Performance* (Kinerja Ekonomi)
- **Pengkategorian**: Memetakan skor akhir ke dalam empat level: `SANGAT_SEHAT`, `SEHAT`, `CUKUP_SEHAT`, dan `KURANG_SEHAT`.

### B. Pemetaan Asesmen
- **Pertanyaan Berbasis Peran**: Pertanyaan ditandai untuk peran `owner` atau `employee`.
- **Instrumen Dinamis**: Pendekatan berbasis database memungkinkan pembaruan kuesioner tanpa mengubah kode inti mesin kalkulasi.

## 5. Sorotan Skema Database

### Manajemen UMKM
- **Identifier String**: UMKM menggunakan format ID string kustom (contoh: `UMKM001`) untuk memberikan identitas entitas bisnis yang lebih profesional.
- **Integritas Relasional**: Asesmen terhubung ke UMKM, dan respons terhubung ke Asesmen serta Pertanyaan.

### Penyimpanan Hasil
- **`health_scores`**: Menyimpan ringkasan keseluruhan dari sesi asesmen.
- **`factor_scores`**: Menyimpan rincian per faktor organisasi untuk analisis grafik radar.
- **`recommendations`**: Basis pengetahuan saran ahli yang dipetakan ke kategori kesehatan tertentu.

## 6. Kontrol Akses & Keamanan
- **Guard Pemilik**: Menggunakan autentikasi sesi standar Laravel. Pemilik mengelola profil bisnis dan melihat hasil monitoring.
- **Token Karyawan**: Menggunakan sistem akses berbasis token (`assessment_tokens`). Karyawan tidak perlu mendaftar akun; mereka mengakses survei melalui tautan unik yang dibatasi oleh kuota.
- **Kontrol Kuota**: Pemilik dapat menetapkan target `jumlah_karyawan`. Sistem secara otomatis mengunci tautan asesmen setelah jumlah responden unik mencapai target tersebut.

## 7. Sistem Visualisasi
- **Radar Chart**: Digunakan pada Profil Faktor untuk membandingkan kinerja di ke-6 faktor.
- **Gauge Chart**: Digunakan untuk pengecekan kesehatan cepat (persentase Skor Kesehatan).
- **Monitoring Hub**: Menyediakan progres partisipasi karyawan secara real-time.

---
*Terakhir Diperbarui: 13 Mei 2026*
