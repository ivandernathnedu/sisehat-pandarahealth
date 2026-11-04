@extends('layouts.clean')

@section('title', 'Evaluasi Organisasi - ' . ($assessment->umkm->nama_umkm ?? 'Pandara Health'))

@section('styles')
<style>
    body { background-color: #0a0a0a; color: #fff; font-family: 'Inter', sans-serif; overflow-x: hidden; }
    .stepper-container { max-width: 800px; margin: 60px auto; padding: 0 20px; }
    
    /* Progress Header */
    .header-info { text-align: center; margin-bottom: 40px; }
    .umkm-name { font-size: 14px; font-weight: 700; color: #666; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; }
    .page-title { font-size: 32px; font-weight: 800; margin-bottom: 24px; }

    .progress-nav { display: flex; justify-content: space-between; position: relative; margin-bottom: 40px; }
    .progress-nav::before { content: ''; position: absolute; top: 15px; left: 0; right: 0; height: 2px; background: #1a1a1a; z-index: 1; }
    .progress-step {
        width: 32px; height: 32px; background: #000; border: 2px solid #1a1a1a;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 800; color: #444; z-index: 2; position: relative;
        transition: all 0.3s;
    }
    .progress-step.active { border-color: #fff; color: #fff; box-shadow: 0 0 15px rgba(255,255,255,0.1); }
    .progress-step.completed { border-color: #4ade80; background: #4ade80; color: #000; }

    /* Card Styling */
    .factor-card {
        background: #111; border: 1px solid #222; border-radius: 24px;
        padding: 40px; display: none; animation: slideUp 0.4s ease-out;
    }
    .factor-card.active { display: block; }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .factor-header { margin-bottom: 32px; }
    .factor-label { font-size: 11px; font-weight: 800; color: #818cf8; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px; }
    .factor-name { font-size: 28px; font-weight: 800; margin-bottom: 12px; }
    .factor-desc { color: #888; font-size: 14px; line-height: 1.6; }

    /* Questions */
    .question-item { padding-top: 32px; margin-top: 32px; border-top: 1px solid #1a1a1a; }
    .question-text { font-size: 16px; font-weight: 500; margin-bottom: 20px; line-height: 1.5; }

    .options-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; }
    .opt-btn {
        background: #000; border: 1px solid #1a1a1a; border-radius: 12px;
        height: 50px; display: flex; align-items: center; justify-content: center;
        font-size: 16px; font-weight: 700; color: #666; cursor: pointer; transition: all 0.2s;
    }
    .opt-btn:hover { border-color: #333; background: #0a0a0a; }
    .opt-btn.active { background: #fff; border-color: #fff; color: #000; }

    .scale-labels { display: flex; justify-content: space-between; margin-top: 8px; font-size: 10px; font-weight: 700; color: #444; text-transform: uppercase; }

    /* Identity Input */
    .identity-input { width: 100%; background: #000; border: 1px solid #222; border-radius: 12px; padding: 18px; color: #fff; font-size: 18px; font-weight: 600; text-align: center; margin-bottom: 20px; outline: none; transition: border-color 0.2s; }
    .identity-input:focus { border-color: #fff; }

    /* Footer Nav */
    .nav-footer { display: flex; justify-content: space-between; margin-top: 40px; }
    .btn-nav { padding: 14px 28px; border-radius: 12px; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.2s; }
    .btn-prev { background: transparent; border: 1px solid #222; color: #666; }
    .btn-next { background: #fff; border: none; color: #000; }
    .btn-next:disabled { opacity: 0.2; cursor: not-allowed; }

    /* Media Queries for Responsiveness */
    @media (max-width: 1024px) {
        .stepper-container { max-width: 90%; margin: 40px auto; }
    }

    @media (max-width: 768px) {
        .stepper-container { margin: 24px auto; }
        .page-title { font-size: 26px; margin-bottom: 16px; }
        
        .progress-nav { margin-bottom: 30px; }
        .progress-nav::before { top: 12px; }
        .progress-step { width: 26px; height: 26px; font-size: 10px; }

        .factor-card { padding: 28px 20px; border-radius: 20px; }
        .factor-name { font-size: 22px; margin-bottom: 8px; }
        .factor-desc { font-size: 13px; }

        .question-item { padding-top: 24px; margin-top: 24px; }
        .question-text { font-size: 14px; margin-bottom: 16px; }
        
        .options-grid {
            display: flex !important;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 12px;
            gap: 10px;
        }
        .opt-btn { 
            min-width: 60px; 
            height: 44px; 
            font-size: 14px; 
            border-radius: 10px; 
            flex-shrink: 0;
            scroll-snap-align: start;
        }
        
        .identity-input { font-size: 16px; padding: 14px; }
        .nav-footer { flex-direction: column; gap: 12px; }
        .btn-nav { padding: 12px 20px; font-size: 13px; justify-content: center; width: 100%; }
    }

    @media (max-width: 480px) {
        .stepper-container { margin: 16px auto; }
        .page-title { font-size: 22px; }
        
        .progress-step { width: 22px; height: 22px; font-size: 9px; }
        .progress-nav::before { top: 10px; }
        .progress-step i { font-size: 9px; }

        .options-grid { gap: 6px; }
        .opt-btn { height: 38px; font-size: 13px; border-radius: 8px; }
        .scale-labels { font-size: 9px; }

        .nav-footer { gap: 10px; }
        .btn-nav { flex: 1; justify-content: center; padding: 12px 10px; font-size: 12px; }
    }
</style>
@endsection

@section('content')
<div class="stepper-container">
    <div class="header-info">
        <div class="umkm-name">{{ $assessment->umkm->nama_umkm ?? 'Ekosistem SiSehat' }}</div>
        <h1 class="page-title">Evaluasi Kesehatan</h1>
    </div>

    {{-- Progress --}}
    <div class="progress-nav">
        <div class="progress-step active" id="dot-0"><i class="fa-solid fa-user-check"></i></div>
        @foreach($factors as $index => $factor)
            <div class="progress-step" id="dot-{{ $index + 1 }}">{{ $index + 1 }}</div>
        @endforeach
    </div>

    <form id="employeeAssessmentForm">
        @csrf
        {{-- Step 0: ID Karyawan --}}
        <div class="factor-card active" id="card-0" data-step="0">
            <div class="factor-header" style="text-align: center;">
                <div class="factor-label">Langkah 1</div>
                <h2 class="factor-name">Verifikasi Identitas</h2>
                <p class="factor-desc">Masukkan ID Karyawan Anda untuk memulai sesi asesmen anonim ini.</p>
            </div>
            
            <input type="text" name="employee_code" id="employee_code" class="identity-input" placeholder="Contoh: PH-2024-001" maxlength="50">
            <span id="codeLengthMsg" style="font-size: 11px; margin-top: -10px; margin-bottom: 20px; display: block; color: var(--text-secondary); text-align: center;"></span>
            
            <div class="nav-footer">
                <div></div>
                <button type="button" class="btn-nav btn-next" onclick="changeStep(1)" id="next-0">Lanjut ke Pertanyaan <i class="fa-solid fa-arrow-right"></i></button>
            </div>
        </div>

        {{-- Steps 1-N: Factors --}}
        @foreach($factors as $index => $factor)
        @php $stepNum = $index + 1; @endphp
        <div class="factor-card" id="card-{{ $stepNum }}" data-step="{{ $stepNum }}">
            <div class="factor-header">
                <div class="factor-label">Faktor {{ $stepNum }}</div>
                <h2 class="factor-name">{{ $factor->nama_factor }}</h2>
                <p class="factor-desc">{{ $factor->deskripsi ?? 'Analisis faktor ini akan membantu perusahaan memahami perspektif karyawan terhadap kondisi internal.' }}</p>
            </div>

            <div class="questions-list">
                @foreach($factor->questions as $q)
                <div class="question-item">
                    <p class="question-text">{{ $q->pertanyaan }}</p>
                    <div class="options-grid">
                        @for($i = 1; $i <= $q->max_score; $i++)
                            <div class="opt-btn" onclick="selectOpt(this, {{ $q->question_id }}, {{ $i }})">{{ $i }}</div>
                        @endfor
                    </div>
                    <div class="scale-labels">
                        <span>Sangat Kurang</span>
                        <span>Sangat Baik</span>
                    </div>
                    <input type="hidden" name="answers[{{ $q->question_id }}]" id="ans_{{ $q->question_id }}" class="ans-field">
                </div>
                @endforeach
            </div>

            <div class="nav-footer">
                <button type="button" class="btn-nav btn-prev" onclick="changeStep(-1)"><i class="fa-solid fa-arrow-left"></i> Kembali</button>
                @if($stepNum < count($factors))
                    <button type="button" class="btn-nav btn-next" onclick="changeStep(1)" id="next-{{ $stepNum }}">Selanjutnya <i class="fa-solid fa-arrow-right"></i></button>
                @else
                    <button type="button" class="btn-nav btn-next" onclick="finalSubmit()" style="background: #4ade80;">Kirim Jawaban <i class="fa-solid fa-paper-plane"></i></button>
                @endif
            </div>
        </div>
        @endforeach
    </form>

    <footer style="margin-top: 80px; text-align: center; color: #444; font-size: 11px; padding-bottom: 40px;">
        <p>© 2026 {{ $assessment->umkm->nama_umkm ?? 'SiSehat System' }} • Data diproses secara anonim.</p>
    </footer>
</div>
@endsection

@section('scripts')
<script>
    let currentStep = 0;
    const totalFactors = {{ count($factors) }};

    function selectOpt(element, qId, val) {
        const parent = element.parentElement;
        parent.querySelectorAll('.opt-btn').forEach(btn => btn.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('ans_' + qId).value = val;
        validate(currentStep);
    }

    function validate(step) {
        let valid = true;
        if (step === 0) {
            const input = document.getElementById('employee_code');
            const msg = document.getElementById('codeLengthMsg');
            const len = input.value.trim().length;

            if (len === 0) {
                msg.textContent = "Maksimal karakter 50";
                msg.style.color = "#737373"; // Abu-abu netral
                input.style.borderColor = "";
                valid = false;
            } else if (len < 3) {
                msg.textContent = "Minimal 3 karakter (" + len + "/50)";
                msg.style.color = "#f87171"; // Merah
                input.style.borderColor = "#f87171";
                valid = false;
            } else {
                msg.textContent = "Maksimal karakter 50 (" + len + "/50)";
                msg.style.color = "#4ade80"; // Hijau
                input.style.borderColor = "#4ade80";
                valid = true;
            }
        } else {
            const card = document.getElementById('card-' + step);
            card.querySelectorAll('.ans-field').forEach(input => {
                if (!input.value) valid = false;
            });
        }
        
        const nextBtn = document.getElementById('next-' + step);
        if (nextBtn) nextBtn.disabled = !valid;
        return valid;
    }

    function changeStep(delta) {
        if (delta > 0 && !validate(currentStep)) return;

        document.getElementById('card-' + currentStep).classList.remove('active');
        document.getElementById('dot-' + currentStep).classList.remove('active');
        if (delta > 0) document.getElementById('dot-' + currentStep).classList.add('completed');

        currentStep += delta;

        document.getElementById('card-' + currentStep).classList.add('active');
        document.getElementById('dot-' + currentStep).classList.add('active');
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function finalSubmit() {
        const employeeCode = document.getElementById('employee_code').value;
        const answers = {};
        document.querySelectorAll('.ans-field').forEach(input => {
            answers[input.id.replace('ans_', '')] = input.value;
        });

        fetch('{{ route('employee.assessment.submit', $token) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                employee_code: employeeCode,
                answers: answers
            })
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                if (res.status === 422 && data.errors) {
                    throw new Error("Terdapat format isian yang belum lengkap atau tidak valid.");
                }
                throw new Error(data.error || data.message || "Terjadi kesalahan pada server");
            }
            return data;
        })
        .then(data => {
            alert(data.message);
            window.location.reload();
        })
        .catch(err => {
            alert(err.message);
        });
    }

    document.getElementById('employee_code').addEventListener('input', () => validate(0));
    document.addEventListener('DOMContentLoaded', () => {
        for(let i=0; i<=totalFactors; i++) validate(i);
    });
</script>
@endsection

