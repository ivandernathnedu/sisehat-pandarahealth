import http from 'k6/http';
import { sleep, check } from 'k6';

/* =========================================================================
   PANDUAN PEMILIHAN TARGET UJI BEBAN (STRESS TEST TARGET CONFIGURATION)
   ========================================================================= */

// --- TARGET PILIHAN A: NGROK / LOCALHOST (DIREKOMENDASIKAN UNTUK TRAFIK TINGGI) ---
const TARGET_ENV = 'INFINITY_FREE'; // Menggunakan target InfinityFree
const BASE_URL = (TARGET_ENV === 'INFINITY_FREE') 
    ? 'http://pandarahealth.infinityfree.me' 
    : 'http://sisehat.test'; // URL virtual host Laragon Anda

// --- TARGET PILIHAN B: INFINITYFREE ONLINE (WAJIB BYPASS FIREWALL) ---
// Trik Bypass: Buka web Anda di Chrome -> Tekan F12 -> Application -> Cookies -> Salin Value "__test"
const INFINITYFREE_COOKIE_TEST = '43f0f2e3a1bc084a0ee8188ccf7dd558'; 

/* =========================================================================
   PENGATURAN VIRTUAL USERS (VUs) & DURASI UJI BEBAN
   ========================================================================= */
export const options = (TARGET_ENV === 'NGROK') 
    ? {
        // Skenario Lokal (50 - 100 VU)
        stages: [
            { duration: '30s', target: 20 },   // Ramp up ke 20 VU dalam 30 detik
            { duration: '1m', target: 50 },    // Naik ke 50 VU dalam 1 menit
            { duration: '1m', target: 100 },   // Naik ke 100 VU dalam 1 menit
            { duration: '30s', target: 0 },    // Ramp down ke 0 dalam 30 detik
        ],
        thresholds: {
            http_req_duration: ['p(95)<500'],  // KKM diperketat: 95% request harus < 500ms
            http_req_failed: ['rate<0.01'],    // Tingkat error maksimal 1%
        }
    }
    : {
        // Skenario Online InfinityFree (AMAN, Trafik dibatasi agar tidak di-suspend 24 jam)
        stages: [
            { duration: '20s', target: 4 },   // Cukup 4 pengguna aktif bersamaan
            { duration: '30s', target: 7 },   // Maksimal 7 pengguna aktif (aman dari suspend)
            { duration: '20s', target: 0 },   // Cooldown ke 0
        ],
        thresholds: {
            http_req_duration: ['p(95)<4000'], // Batas toleransi latensi hosting gratis 4 detik
            http_req_failed: ['rate<0.05'],    // Toleransi error maksimal 5%
        }
    };

// Kredensial Akun Owner yang disesuaikan secara dinamis
const OWNER_EMAIL = (TARGET_ENV === 'INFINITY_FREE') 
    ? 'akubaruloginlagi@gmail.com' 
    : 'owner_1@example.com'; 
const OWNER_PASSWORD = (TARGET_ENV === 'INFINITY_FREE') 
    ? 'akubisa12' 
    : 'password123'; 

export default function () {
    // Siapkan parameter header untuk bypass firewall & penyamaran User-Agent
    const params = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36',
        },
    };

    // Jika menguji InfinityFree, suntikkan cookie bypass keamanan
    if (TARGET_ENV === 'INFINITY_FREE') {
        params.headers['Cookie'] = `__test=${INFINITYFREE_COOKIE_TEST}`;
    }

    // --- TAHAP 1: Mengakses Halaman Utama ---
    let resWelcome = http.get(`${BASE_URL}/`, params);
    check(resWelcome, {
        'Halaman utama status 200': (r) => r.status === 200,
        'Halaman utama load time < 500ms': (r) => r.timings.duration < 500,
    });
    sleep(1);

    // --- TAHAP 2: Mengakses Halaman Login ---
    let resLoginPage = http.get(`${BASE_URL}/login`, params);
    check(resLoginPage, {
        'Halaman login status 200': (r) => r.status === 200,
        'Halaman login load time < 500ms': (r) => r.timings.duration < 500,
    });
    sleep(1);

    // --- TAHAP 3: Simulasi Login Owner (Menguji Kecepatan Autentikasi API) ---
    const payload = JSON.stringify({
        email: OWNER_EMAIL,
        password: OWNER_PASSWORD,
    });

    let resLogin = http.post(`${BASE_URL}/api/auth/login`, payload, params);
    let loginOk = check(resLogin, {
        'API Login sukses (200)': (r) => r.status === 200,
        'API Login response valid JSON': (r) => {
            try {
                const body = JSON.parse(r.body);
                return body.message === 'Login berhasil' || body.hasOwnProperty('data');
            } catch (e) {
                return false;
            }
        },
        'API Login load time < 500ms': (r) => r.timings.duration < 500,
    });

    // Jika login sukses, uji kueri database berat dengan cookie sesi
    if (resLogin.status === 200) {
        let ownerId = null;
        try {
            const loginBody = JSON.parse(resLogin.body);
            ownerId = loginBody.data.owner_id;
        } catch (e) {}

        const sessionParams = {
            headers: Object.assign({}, params.headers, {
                'Cookie': (TARGET_ENV === 'INFINITY_FREE') 
                    ? `__test=${INFINITYFREE_COOKIE_TEST}; ${resLogin.headers['Set-Cookie']}`
                    : resLogin.headers['Set-Cookie']
            })
        };

        if (ownerId) {
            // --- TAHAP 4: Dynamic Assessment Fetch & Submission ---
            // A. Dapatkan daftar UMKM milik Owner ini
            let resUmkmList = http.get(`${BASE_URL}/api/umkm?owner_id=${ownerId}`, sessionParams);
            let umkmList = null;
            try {
                umkmList = JSON.parse(resUmkmList.body);
            } catch (e) {}

            if (umkmList && umkmList.data && umkmList.data.length > 0) {
                const firstUmkm = umkmList.data[0];
                const umkmId = firstUmkm.umkm_id;

                // B. Ambil detail UMKM untuk mendapatkan ID Assessment yang aktif
                let resUmkm = http.get(`${BASE_URL}/api/umkm/${umkmId}`, sessionParams);
                let umkmData = null;
                try {
                    umkmData = JSON.parse(resUmkm.body);
                } catch (e) {}

                if (umkmData && umkmData.data && umkmData.data.assessments && umkmData.data.assessments.length > 0) {
                    const activeAssessment = umkmData.data.assessments[0];
                    const assessmentId = activeAssessment.assessment_id;

                    // C. Ambil daftar pertanyaan untuk Employee secara dinamis
                    let resQuestions = http.get(`${BASE_URL}/api/assessment/questions?type=employee`, sessionParams);
                    let questionsData = null;
                    try {
                        questionsData = JSON.parse(resQuestions.body);
                    } catch (e) {}

                    if (questionsData && Array.isArray(questionsData)) {
                        // D. Buat payload jawaban kuesioner acak (nilai 1-5)
                        const answers = questionsData.map(q => {
                            return {
                                question_id: q.question_id,
                                answer_value: Math.floor(Math.random() * 5) + 1 // Skor acak 1-5
                            };
                        });

                        const submitPayload = JSON.stringify({
                            assessment_id: assessmentId,
                            respondent_type: 'employee',
                            employee_code: 'EMP-' + Math.floor(Math.random() * 1000000), // Kode karyawan acak
                            answers: answers
                        });

                        // E. Kirim jawaban kuesioner ke API
                        let resSubmit = http.post(`${BASE_URL}/api/responses/submit`, submitPayload, sessionParams);
                        check(resSubmit, {
                            'API Submit Jawaban sukses (200)': (r) => r.status === 200,
                            'API Submit response valid JSON': (r) => {
                                try {
                                    return JSON.parse(r.body).message === 'Jawaban berhasil disimpan.';
                                } catch (e) {
                                    return false;
                                }
                            },
                            'API Submit load time < 500ms': (r) => r.timings.duration < 500,
                        });
                    }
                }
            }
        }

        // --- TAHAP 5: Mengambil Data Urutan UMKM (Kueri Hitung Bobot DSS Berat) ---
        let resRank = http.get(`${BASE_URL}/api/umkm/rank`, sessionParams);
        check(resRank, {
            'API rangking UMKM status 200': (r) => r.status === 200,
            'API rangking load time < 500ms': (r) => r.timings.duration < 500,
        });
        sleep(2);
    } else {
        // Fallback: Menguji kuesioner publik jika login gagal
        let resQuestions = http.get(`${BASE_URL}/api/assessment/questions?type=owner`, params);
        check(resQuestions, {
            'API Kuesioner status 200': (r) => r.status === 200,
            'API Kuesioner load time < 500ms': (r) => r.timings.duration < 500,
        });
        sleep(2);
    }
}
