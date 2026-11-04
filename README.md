<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


Keputusan yang bijak! Jika memang hostingnya membatasi Postman, menulis dokumentasi manual di **README.md** GitHub adalah solusi paling aman dan tetap terlihat profesional di mata penilai.

Berikut adalah draf lengkap **API Documentation** yang bisa kamu langsung salin ke file `README.md` di repository backend kamu.

---

### 📝 API Documentation (Manual)

**Base URL:** `https://pandarahealth.infinityfree.me`

#### 1. Authentication
| Method | Endpoint | Deskripsi | Auth |
|:---|:---|:---|:---|
| POST | `/api/auth/register` | Mendaftarkan akun Owner baru | No |
| POST | `/api/auth/login` | Login untuk mendapatkan akses | No |
| POST | `/api/auth/logout` | Menghapus sesi login | Yes |
| GET | `/api/auth/me` | Mengambil data profil user login | Yes |

**Contoh Body Register (JSON):**
```json
{
    "name": "Nama User",
    "email": "user@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

---

#### 2. UMKM Management
| Method | Endpoint | Deskripsi | Auth |
|:---|:---|:---|:---|
| GET | `/api/umkm` | List semua UMKM milik owner | Yes |
| POST | `/api/umkm` | Menambah data UMKM baru | Yes |
| GET | `/api/umkm/{id}` | Melihat detail satu UMKM | Yes |

**Contoh Body Tambah UMKM (JSON):**
```json
{
    "nama_umkm": "Toko Berkah",
    "bidang": "Perdagangan",
    "jumlah_karyawan": 5
}
```

---

#### 3. Assessment & Survey
| Method | Endpoint | Deskripsi | Auth |
|:---|:---|:---|:---|
| GET | `/api/assessment/questions` | Ambil daftar soal (query: `?type=owner`) | No |
| POST | `/api/assessment/submit` | Mengirim jawaban survei | No |
| GET | `/api/assessment/{id}/monitoring` | Cek progres pengisian karyawan | Yes |

**Contoh Body Submit Jawaban (JSON):**
```json
{
    "assessment_id": 1,
    "answers": [
        { "question_id": 1, "answer": 5 },
        { "question_id": 2, "answer": 3 }
    ]
}
```

---

#### 4. Dashboard & Analytics
| Method | Endpoint | Deskripsi | Auth |
|:---|:---|:---|:---|
| GET | `/api/dashboard/umkm/{id}/latest` | Skor kesehatan terakhir | Yes |
| GET | `/api/dashboard/assessment/{id}/factors` | Data untuk Grafik Radar | Yes |

---