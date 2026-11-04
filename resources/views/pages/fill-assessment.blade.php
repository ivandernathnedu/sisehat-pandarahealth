@extends('layouts.app')

@section('title', 'Isi Asesmen: ' . $umkm->nama_umkm)

@section('styles')
<style>
    .stepper-container { max-width: 800px; margin: 40px auto; }
    
    /* Progress Bar */
    .progress-nav {
        display: flex;
        justify-content: space-between;
        margin-bottom: 40px;
        position: relative;
    }
    .progress-nav::before {
        content: '';
        position: absolute;
        top: 15px; left: 0; right: 0;
        height: 2px; background: #222;
        z-index: 1;
    }
    .progress-step {
        width: 32px; height: 32px;
        background: #151515; border: 2px solid #222;
        border-radius: 50%; display: flex;
        align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; color: #444;
        z-index: 2; position: relative;
        transition: all 0.3s ease;
    }
    .progress-step.active { border-color: #fff; color: #fff; background: #000; box-shadow: 0 0 15px rgba(255,255,255,0.1); }
    .progress-step.completed { border-color: #4ade80; background: #4ade80; color: #000; }

    /* Factor Card */
    .factor-card {
        background: var(--card-color);
        border: 1px solid var(--border-color);
        border-radius: 24px;
        padding: 40px;
        display: none; /* Hidden by default */
        animation: fadeIn 0.4s ease-out;
    }
    .factor-card.active { display: block; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .factor-header { margin-bottom: 32px; }
    .factor-title { font-size: 12px; font-weight: 800; color: var(--accent); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px; }
    .factor-name { font-size: 32px; font-weight: 800; margin-bottom: 12px; color: #fff; }
    .factor-desc { font-size: 14px; color: var(--text-secondary); line-height: 1.6; }

    /* Questions */
    .question-box { margin-bottom: 40px; padding-top: 32px; border-top: 1px solid #222; }
    .question-text { font-size: 16px; font-weight: 500; margin-bottom: 20px; color: #fff; }

    /* Scale Buttons */
    .options-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    .option-btn {
        background: #151515; border: 1px solid #222;
        border-radius: 12px; padding: 18px;
        color: var(--text-secondary); font-size: 13px; font-weight: 600;
        cursor: pointer; text-align: center; transition: all 0.2s;
        display: flex; align-items: center; justify-content: center;
        min-height: 60px;
    }
    .option-btn:hover { border-color: #444; background: #1a1a1a; }
    .option-btn.active { background: #fff; border-color: #fff; color: #000; }

    /* Navigation */
    .nav-footer { display: flex; justify-content: space-between; margin-top: 32px; }
    .btn-nav {
        padding: 14px 28px; border-radius: 12px; font-size: 14px; font-weight: 600;
        cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 10px;
    }
    .btn-prev { background: transparent; border: 1px solid #333; color: var(--text-secondary); }
    .btn-next { background: #fff; border: none; color: #000; }
    .btn-next:disabled { opacity: 0.3; cursor: not-allowed; }

    @media (max-width: 768px) {
        .stepper-container { margin: 24px auto; padding: 0 16px; }
        .factor-card { padding: 24px 20px; }
        .factor-name { font-size: 24px; }
        
        .options-grid {
            display: flex !important;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 12px;
            gap: 12px;
        }
        .option-btn {
            min-width: 100px;
            flex-shrink: 0;
            scroll-snap-align: start;
        }
        
        .nav-footer { flex-direction: column; gap: 16px; }
        .btn-nav { justify-content: center; width: 100%; }
        
        .progress-step { width: 28px; height: 28px; font-size: 11px; }
    }
</style>
@endsection

@section('content')
<div class="stepper-container">
    @if($factors->isEmpty())
        <div style="text-align: center; padding: 60px 20px;">
            <i class="fa-solid fa-file-circle-question" style="font-size: 48px; color: #fff; margin-bottom: 16px;"></i>
            <h3 style="color: #fff; font-size: 18px; margin-bottom: 8px;">Soal Asesmen Sedang Disiapkan</h3>
            <p style="color: var(--text-secondary); font-size: 13px; margin-bottom: 24px;">Data faktor dan pertanyaan kuesioner sedang diinisialisasi ke database.</p>
            <button onclick="window.location.reload()" class="btn-nav btn-next" style="margin: 0 auto; display: inline-flex;">
                <i class="fa-solid fa-rotate-right"></i> Muat Ulang Halaman
            </button>
        </div>
    @else
    {{-- Progress Navigation --}}
    <div class="progress-nav">
        @foreach($factors as $index => $factor)
            <div class="progress-step {{ $index === 0 ? 'active' : '' }}" id="step-dot-{{ $index + 1 }}">
                {{ $index + 1 }}
            </div>
        @endforeach
    </div>

    <form id="multiStepForm">
        @csrf
        <input type="hidden" name="assessment_id" value="{{ $assessment->assessment_id }}">
        
        @php $globalQCount = 1; @endphp
        @foreach($factors as $index => $factor)
        @php $stepNum = $index + 1; @endphp
        <div class="factor-card {{ $index === 0 ? 'active' : '' }}" id="step-card-{{ $stepNum }}" data-step="{{ $stepNum }}">
            <div class="factor-header">
                <div class="factor-title">FAKTOR {{ $stepNum }}</div>
                <h2 class="factor-name">{{ $factor->nama_factor }}</h2>
                <p class="factor-desc">{{ $factor->deskripsi ?? 'Analisis dimensi ini membantu memahami efektivitas operasional dan strategis UMKM Anda.' }}</p>
            </div>

            <div class="questions-list">
                @foreach($factor->questions as $q)
                <div class="question-box" data-q-id="{{ $q->question_id }}">
                    <p class="question-text">{{ $globalQCount }}. {{ $q->pertanyaan }}</p>
                    
                    @php
                        $options = null;
                        if ($globalQCount >= 1 && $globalQCount <= 5) {
                            $options = [1 => 'Tidak', 2 => 'Dalam Proses', 3 => 'Iya'];
                        } elseif ($globalQCount == 6) {
                            $options = [1 => 'Modal Sendiri', 2 => 'Keluarga', 3 => 'Bank/Kredit'];
                        }
                        $maxScore = $options ? 3 : ($q->max_score ?? 5);
                    @endphp

                    @if($options)
                        <div class="options-grid" style="grid-template-columns: repeat(3, 1fr);">
                            @foreach($options as $val => $label)
                            <div class="option-btn {{ (isset($existingResponses[$q->question_id]) && $existingResponses[$q->question_id] == $val) ? 'active' : '' }}" 
                                 onclick="selectOption(this, {{ $q->question_id }}, {{ $val }})">
                                {{ $label }}
                            </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Skala Standar 1-5 --}}
                        <div class="options-grid" style="grid-template-columns: repeat({{ $maxScore }}, 1fr);">
                            @for($i = 1; $i <= $maxScore; $i++)
                            <div class="option-btn {{ (isset($existingResponses[$q->question_id]) && $existingResponses[$q->question_id] == $i) ? 'active' : '' }}" 
                                 onclick="selectOption(this, {{ $q->question_id }}, {{ $i }})">
                                {{ $i }}
                            </div>
                            @endfor
                        </div>
                        <div class="scale-labels" style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 10px; color: #444; font-weight: 700; text-transform: uppercase;">
                            <span>Sangat Buruk</span>
                            <span>Sangat Baik</span>
                        </div>
                    @endif

                    <input type="hidden" name="answers[{{ $q->question_id }}]" 
                           id="ans_{{ $q->question_id }}" 
                           value="{{ $existingResponses[$q->question_id] ?? '' }}"
                           class="answer-input">
                </div>
                @php $globalQCount++; @endphp
                @endforeach
            </div>

            <div class="nav-footer">
                @if($stepNum > 1)
                    <button type="button" class="btn-nav btn-prev" onclick="changeStep(-1)">
                        <i class="fa-solid fa-arrow-left"></i> Sebelumnya
                    </button>
                @else
                    <div></div> {{-- Spacer --}}
                @endif

                @if($stepNum < count($factors))
                    <button type="button" class="btn-nav btn-next" onclick="changeStep(1)" id="next-btn-{{ $stepNum }}">
                        Selanjutnya <i class="fa-solid fa-arrow-right"></i>
                    </button>
                @else
                    <button type="button" class="btn-nav btn-next" onclick="submitFinal()" style="background: #4ade80;">
                        Simpan & Selesai <i class="fa-solid fa-circle-check"></i>
                    </button>
                @endif
            </div>
        </div>
        @endforeach
    </form>
    @endif
</div>
@endsection

@section('scripts')
<script>
    let currentStep = 1;
    const totalSteps = {{ count($factors) }};

    function selectOption(element, qId, value) {
        const parent = element.parentElement;
        parent.querySelectorAll('.option-btn').forEach(btn => btn.classList.remove('active'));
        element.classList.add('active');
        
        document.getElementById('ans_' + qId).value = value;
        validateStep(currentStep);
    }

    function validateStep(step) {
        const currentCard = document.getElementById('step-card-' + step);
        const inputs = currentCard.querySelectorAll('.answer-input');
        let allFilled = true;
        
        inputs.forEach(input => {
            if (!input.value) allFilled = false;
        });

        const nextBtn = document.getElementById('next-btn-' + step);
        if (nextBtn) nextBtn.disabled = !allFilled;
        
        return allFilled;
    }

    function changeStep(delta) {
        if (delta > 0 && !validateStep(currentStep)) {
            alert('Mohon isi semua pertanyaan di halaman ini.');
            return;
        }

        // Autosave before moving
        if (delta > 0) saveProgress();

        // Update UI
        document.getElementById('step-card-' + currentStep).classList.remove('active');
        document.getElementById('step-dot-' + currentStep).classList.remove('active');
        if (delta > 0) document.getElementById('step-dot-' + currentStep).classList.add('completed');

        currentStep += delta;

        document.getElementById('step-card-' + currentStep).classList.add('active');
        document.getElementById('step-dot-' + currentStep).classList.add('active');
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function saveProgress() {
        const formData = new FormData(document.getElementById('multiStepForm'));
        const assessmentId = formData.get('assessment_id');
        
        const answers = [];
        const currentCard = document.getElementById('step-card-' + currentStep);
        currentCard.querySelectorAll('.answer-input').forEach(input => {
            const qId = input.id.replace('ans_', '');
            if (input.value) {
                answers.push({ question_id: parseInt(qId), answer_value: parseInt(input.value) });
            }
        });

        if (answers.length > 0) {
            fetch('{{ route('api.responses.submit') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    assessment_id: assessmentId,
                    respondent_type: 'owner',
                    answers: answers
                })
            });
        }
    }

    function submitFinal() {
        if (!validateStep(currentStep)) {
            alert('Mohon isi semua pertanyaan sebelum menyelesaikan.');
            return;
        }
        
        saveProgress();
        
        const assessmentId = document.querySelector('input[name="assessment_id"]').value;
        
        fetch(`/assessment/finish/${assessmentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message || 'Asesmen Anda telah berhasil disimpan!');
            window.location.href = '{{ route('assessment') }}';
        })
        .catch(err => {
            console.error('Error finalizing assessment:', err);
            alert('Terjadi kesalahan saat menyelesaikan asesmen.');
        });
    }

    // Initial validation for steps
    document.addEventListener('DOMContentLoaded', () => {
        for(let i=1; i<=totalSteps; i++) validateStep(i);
    });
</script>
@endsection
