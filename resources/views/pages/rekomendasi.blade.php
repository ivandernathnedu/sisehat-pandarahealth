@extends('layouts.app')

@section('title', 'Rekomendasi Strategis')

@section('styles')
<style>
    /* ── Page Header ── */
    .rek-title { font-size: 36px; font-weight: 800; margin-bottom: 14px; letter-spacing: -0.5px; }

    .rek-select {
        appearance: none; background: var(--card-color);
        border: 1px solid var(--border-color); border-radius: 10px;
        padding: 10px 40px 10px 16px; color: #fff; font-size: 14px;
        font-family: 'Inter', sans-serif; cursor: pointer; outline: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 14px center;
        margin-bottom: 20px; min-width: 220px;
    }

    .rek-meta-row { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 28px; }
    .rek-desc { font-size: 13px; color: var(--text-secondary); line-height: 1.7; max-width: 480px; }
    .badge-status {
        display: inline-flex; align-items: center; gap: 7px;
        background: var(--card-color); border: 1px solid var(--border-color);
        border-radius: 20px; padding: 7px 16px; font-size: 12px; font-weight: 600;
        color: #fff; white-space: nowrap;
    }
    .badge-status-dot { width: 8px; height: 8px; border-radius: 50%; background: #4ade80; }

    /* ── Main Grid ── */
    .rek-grid { display: grid; grid-template-columns: 1fr 1.6fr; gap: 24px; align-items: flex-start; }
    
    /* ── Left Column Sticky ── */
    .rek-left-col { position: sticky; top: 24px; }

    /* ── Left: Gauge Card ── */
    .gauge-card {
        background: var(--card-color); border: 1px solid var(--border-color);
        border-radius: 16px; padding: 28px 24px 24px;
        height: 100%; display: flex; flex-direction: column;
    }
    .gauge-card-header { display: flex; align-items: center; gap: 14px; margin-bottom: 60px; }
    .gauge-card-icon {
        width: 32px; height: 32px; border-radius: 8px;
        background: transparent; border: 1px solid rgba(255,255,255,0.1);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .gauge-card-icon i { color: #fff; font-size: 14px; }
    .gauge-card-title { font-size: 26px; font-weight: 600; line-height: 1.1; color: #fff; }
    .gauge-canvas-wrap { position: relative; margin: 0 auto 40px; width: 220px; height: 130px; }
    .gauge-center-text {
        position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%);
        text-align: center; line-height: 1;
    }
    .gauge-pct { font-size: 52px; font-weight: 700; color: #fff; }
    .gauge-pct span { font-size: 24px; font-weight: 400; color: #aaa; margin-left: -5px; }
    .gauge-label { font-size: 12px; font-weight: 700; letter-spacing: 1px; color: #00d285; margin-top: 8px; }
    .gauge-stats { display: flex; justify-content: space-between; border-top: 1px solid #222; padding-top: 24px; margin-top: 40px; }
    .gauge-stat { flex: 1; }
    .gauge-stat:first-child { text-align: left; }
    .gauge-stat:last-child { text-align: right; }
    .gauge-stat-label { font-size: 12px; font-weight: 600; letter-spacing: 0.5px; color: #666; text-transform: uppercase; margin-bottom: 8px; }
    .gauge-stat-val { font-size: 20px; font-weight: 500; color: #fff; }
    .gauge-stat-val.green { color: #00d285; }
    .gauge-note { font-size: 12px; color: #666; line-height: 1.6; margin-top: auto; padding-top: 24px; font-weight: 400; }

    /* ── Right: Priority Cards ── */
    .prio-header { display: flex; align-items: center; gap: 10px; margin-bottom: 18px; }
    .prio-header-icon { font-size: 20px; color: #facc15; }
    .prio-header-title { font-size: 20px; font-weight: 700; }

    .prio-card {
        background: #141414; border: 1px solid var(--border-color);
        border-radius: 14px; padding: 20px 20px 16px; margin-bottom: 14px;
        border-left: 4px solid;
        position: relative; overflow: hidden;
    }
    .prio-card.high  { border-left-color: #f87171; background: rgba(248,113,113,.04); }
    .prio-card.warn  { border-left-color: #fb923c; background: rgba(251,146,60,.04); }
    .prio-card.med   { border-left-color: #facc15; background: rgba(250,204,21,.04); }
    .prio-card.low   { border-left-color: #4ade80; background: rgba(74,222,128,.04); }

    .prio-card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
    .prio-card-left { display: flex; align-items: center; gap: 12px; }
    .prio-icon-wrap {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .prio-card.high  .prio-icon-wrap { background: rgba(248,113,113,.15); }
    .prio-card.med   .prio-icon-wrap { background: rgba(250,204,21,.12); }
    .prio-card.low   .prio-icon-wrap { background: rgba(74,222,128,.12); }
    .prio-card.high  .prio-icon-wrap i { color: #f87171; }
    .prio-card.warn  .prio-icon-wrap { background: rgba(251,146,60,.12); }
    .prio-card.warn  .prio-icon-wrap i { color: #fb923c; }
    .prio-card.med   .prio-icon-wrap i { color: #facc15; }
    .prio-card.low   .prio-icon-wrap i { color: #4ade80; }

    .prio-name { font-size: 14px; font-weight: 700; }
    .prio-badge {
        font-size: 10px; font-weight: 800; padding: 4px 10px;
        border-radius: 6px; letter-spacing: .5px; flex-shrink: 0;
    }
    .prio-badge.high { background: rgba(248,113,113,.2); color: #f87171; }
    .prio-badge.warn { background: rgba(251,146,60,.15); color: #fb923c; }
    .prio-badge.med  { background: rgba(250,204,21,.15); color: #facc15; }
    .prio-badge.low  { background: rgba(74,222,128,.15); color: #4ade80; }

    .prio-desc { font-size: 12px; color: var(--text-secondary); line-height: 1.65; margin-bottom: 14px; }
    .prio-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .prio-btn {
        font-size: 11px; font-weight: 600; padding: 7px 14px; border-radius: 8px;
        border: 1px solid var(--border-color); background: var(--card-color);
        color: var(--text-secondary); cursor: pointer; font-family: 'Inter', sans-serif;
        display: flex; align-items: center; gap: 6px; transition: all .2s;
    }
    .prio-btn:hover { background: #222; color: #fff; border-color: #555; }

    .rek-dropdowns { display: flex; gap: 12px; }

    @media (max-width: 1024px) {
        .rek-grid { grid-template-columns: 1fr; }
        .rek-left-col { position: relative; top: 0; }
        .rek-meta-row { flex-direction: column; align-items: flex-start; gap: 16px; }
    }
    
    @media (max-width: 768px) {
        .rek-dropdowns { flex-direction: column; }
        .rek-select { width: 100%; min-width: 100%; }
        .rek-title { font-size: 28px; }
        .gauge-card { padding: 24px 20px; }
    }
</style>
@endsection

@section('content')

{{-- Page Title --}}
<div class="rek-title">Rekomendasi Strategis</div>

{{-- Dropdowns --}}
<div class="rek-dropdowns">
    @if(isset($all_umkms) && $all_umkms->count() > 1)
        <select class="rek-select" onchange="window.location.href='?umkm_id=' + this.value">
            @foreach($all_umkms as $u)
                <option value="{{ $u->umkm_id }}" {{ $selected_umkm_id == $u->umkm_id ? 'selected' : '' }}>
                    {{ $u->nama_umkm }}
                </option>
            @endforeach
        </select>
    @else
        <select class="rek-select">
            <option>{{ optional($assessment_info)->umkm->nama_umkm ?? 'Nama UMKM' }}</option>
        </select>
    @endif

    @if(isset($assessment_history) && $assessment_history->count() > 0)
        <select class="rek-select" onchange="window.location.href='?umkm_id={{ $selected_umkm_id }}&assessment_id=' + this.value">
            @foreach($assessment_history as $hist)
                <option value="{{ $hist->assessment_id }}" {{ $selected_assessment_id == $hist->assessment_id ? 'selected' : '' }}>
                    Periode: {{ \Carbon\Carbon::parse($hist->assessment_date)->format('F Y') }} (Ke-{{ $hist->sequence_in_month ?? 1 }})
                </option>
            @endforeach
        </select>
    @endif
</div>

{{-- Meta row: desc + status badge --}}
<div class="rek-meta-row">
    <p class="rek-desc">Berdasarkan analisis terbaru, berikut adalah tindakan yang disarankan untuk meningkatkan kesehatan organisasi secara keseluruhan. Prioritaskan area kritis terlebih dahulu.</p>
    <div class="badge-status">
        @php
            $overallCat = strtoupper($health_score->category ?? '');
            $dotColor = '#fff';
            if ($overallCat == 'SANGAT_SEHAT') $dotColor = '#4ade80';
            elseif ($overallCat == 'SEHAT') $dotColor = '#818cf8';
            elseif ($overallCat == 'CUKUP_SEHAT') $dotColor = '#facc15';
            elseif ($overallCat == 'KURANG_SEHAT') $dotColor = '#f87171';
        @endphp
        <span id="badge-status-dot" class="badge-status-dot" style="background: {{ $dotColor }}"></span> <span id="badge-status-text">Status: {{ str_replace('_', ' ', strtoupper($overallCat ?: 'N/A')) }}</span>
    </div>
</div>

{{-- Main 2-col Grid --}}
<div class="rek-grid">

    {{-- LEFT: Gauge Card --}}
    <div class="rek-left-col">
        <div class="gauge-card">
        <div class="gauge-card-header">
            <div class="gauge-card-icon">
                <i class="fa-solid fa-gauge-high"></i>
            </div>
            <div class="gauge-card-title">Kesehatan<br>Keseluruhan</div>
        </div>

        <div class="gauge-canvas-wrap">
            <canvas id="gaugeChart" style="width:220px; height:130px;"></canvas>
            <div class="gauge-center-text">
                <div class="gauge-pct"><span id="gauge-pct-val">{{ number_format($health_score->overall_score ?? 0, 0) }}</span><span>%</span></div>
                <div class="gauge-label">SKOR AKTIF</div>
            </div>
        </div>

        <div class="gauge-stats">
            <div class="gauge-stat">
                <div class="gauge-stat-label">Target Capaian</div>
                <div class="gauge-stat-val">85%</div>
            </div>
            <div class="gauge-stat">
                <div class="gauge-stat-label">Status Saat Ini</div>
                @php
                    $cat = strtoupper($health_score->category ?? '');
                    $catColor = '#fff';
                    if ($cat == 'SANGAT_SEHAT') $catColor = '#4ade80';
                    elseif ($cat == 'SEHAT') $catColor = '#818cf8';
                    elseif ($cat == 'CUKUP_SEHAT') $catColor = '#facc15';
                    elseif ($cat == 'KURANG_SEHAT') $catColor = '#f87171';
                @endphp
                <div id="current-status-val" class="gauge-stat-val" style="color: {{ $catColor }};">
                    {{ str_replace('_', ' ', strtoupper($health_score->category ?? 'N/A')) }}
                </div>
            </div>
        </div>

        <p class="gauge-note">*Skor dihitung berdasarkan rata-rata 6 faktor kesehatan organisasi.</p>
        </div>
    </div>

    {{-- RIGHT: Priority Cards --}}
    <div>
        <div class="prio-header">
            <span class="prio-header-icon">!</span>
            <span class="prio-header-title">Prioritas Perbaikan</span>
        </div>

        <div id="priority-cards-container">
        @foreach($recommendations as $rec)
            @php
                // Map category level to priority styling
                $level = strtolower($rec->category_level);
                $prioClass = 'low';
                $prioText = 'RENDAH';
                $icon = 'fa-square-check';

                if ($level == 'kurang_sehat') {
                    $prioClass = 'high';
                    $prioText = 'KRITIS';
                    $icon = 'fa-triangle-exclamation';
                } elseif ($level == 'cukup_sehat') {
                    $prioClass = 'warn';
                    $prioText = 'WASPADA';
                    $icon = 'fa-circle-exclamation';
                } elseif ($level == 'sehat') {
                    $prioClass = 'med';
                    $prioText = 'SEHAT';
                    $icon = 'fa-circle-info';
                } elseif ($level == 'sangat_sehat') {
                    $prioClass = 'low';
                    $prioText = 'SANGAT SEHAT';
                    $icon = 'fa-circle-check';
                }

                // Match factor name from radar_chart
                $factorName = collect($radar_chart)->firstWhere('id', $rec->factor_id)['factor'] ?? 'Faktor ' . $rec->factor_id;
            @endphp
            <div class="prio-card {{ $prioClass }}">
                <div class="prio-card-top">
                    <div class="prio-card-left">
                        <div class="prio-icon-wrap">
                            <i class="fa-solid {{ $icon }}"></i>
                        </div>
                        <div class="prio-name">{{ $factorName }}</div>
                    </div>
                    <span class="prio-badge {{ $prioClass }}">{{ $prioText }}</span>
                </div>
                <p class="prio-desc">{{ $rec->recommendation_text }}</p>
                @if($rec->suggested_action)
                <div class="prio-actions">
                    <button class="prio-btn"><i class="fa-solid fa-bolt"></i> {{ $rec->suggested_action }}</button>
                </div>
                @endif
            </div>
        @endforeach
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const healthScore = {{ number_format($health_score->overall_score ?? 0, 0) }};
    let gaugeColor = '#f87171';
    if (healthScore > 75) gaugeColor = '#4ade80';
    else if (healthScore > 50) gaugeColor = '#818cf8';
    else if (healthScore > 25) gaugeColor = '#facc15';

    // Half-donut gauge chart
    const gaugeCtx = document.getElementById('gaugeChart').getContext('2d');
    window.gaugeChart = new Chart(gaugeCtx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [healthScore, 100 - healthScore],
                backgroundColor: [gaugeColor, '#222'],
                borderWidth: 0,
                borderRadius: 10,
                circumference: 180,
                rotation: 270
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            cutout: '80%',
            plugins: { legend: { display: false }, tooltip: { enabled: false } }
        }
    });

    function startRekomendasiPoller() {
        setInterval(() => {
            fetch('{{ route('api.realtime.rekomendasi') }}?umkm_id={{ $selected_umkm_id }}&assessment_id={{ $selected_assessment_id }}')
                .then(res => res.json())
                .then(data => {
                    if (data.error) return;

                    // 1. Update stats & badge
                    if (data.health_score) {
                        const score = Math.round(data.health_score.overall_score);
                        document.getElementById('gauge-pct-val').innerText = score;
                        
                        let catColor = '#fff';
                        let dotColor = '#fff';
                        const catClean = data.health_score.category.replace('_', ' ').toUpperCase();
                        if (score > 75) { catColor = '#4ade80'; dotColor = '#4ade80'; }
                        else if (score > 50) { catColor = '#818cf8'; dotColor = '#818cf8'; }
                        else if (score > 25) { catColor = '#facc15'; dotColor = '#facc15'; }
                        else { catColor = '#f87171'; dotColor = '#f87171'; }

                        const badgeText = document.getElementById('badge-status-text');
                        if (badgeText) badgeText.innerText = 'Status: ' + catClean;
                        
                        const badgeDot = document.getElementById('badge-status-dot');
                        if (badgeDot) badgeDot.style.backgroundColor = dotColor;

                        const currStatusVal = document.getElementById('current-status-val');
                        if (currStatusVal) {
                            currStatusVal.style.color = catColor;
                            currStatusVal.innerText = catClean;
                        }

                        // 2. Update Gauge chart
                        if (window.gaugeChart) {
                            window.gaugeChart.data.datasets[0].data = [score, 100 - score];
                            window.gaugeChart.data.datasets[0].backgroundColor = [catColor, '#222'];
                            window.gaugeChart.update();
                        }
                    }

                    // 3. Update Priority Cards
                    if (data.recommendations && data.radar_chart) {
                        let prioCardsHtml = '';
                        data.recommendations.forEach(rec => {
                            let level = rec.category_level.toLowerCase();
                            let prioClass = 'low';
                            let prioText = 'RENDAH';
                            let icon = 'fa-square-check';

                            if (level === 'kurang_sehat') {
                                prioClass = 'high';
                                prioText = 'KRITIS';
                                icon = 'fa-triangle-exclamation';
                            } else if (level === 'cukup_sehat') {
                                prioClass = 'warn';
                                prioText = 'WASPADA';
                                icon = 'fa-circle-exclamation';
                            } else if (level === 'sehat') {
                                prioClass = 'med';
                                prioText = 'SEHAT';
                                icon = 'fa-circle-info';
                            } else if (level === 'sangat_sehat') {
                                prioClass = 'low';
                                prioText = 'SANGAT SEHAT';
                                icon = 'fa-circle-check';
                            }

                            // find factor name
                            const factorObj = data.radar_chart.find(f => f.id == rec.factor_id);
                            const factorName = factorObj ? factorObj.factor : ('Faktor ' + rec.factor_id);

                            const actionBtnHtml = rec.suggested_action ? `
                                <div class="prio-actions">
                                    <button class="prio-btn"><i class="fa-solid fa-bolt"></i> ${rec.suggested_action}</button>
                                </div>` : '';

                            prioCardsHtml += `
                                <div class="prio-card ${prioClass}">
                                    <div class="prio-card-top">
                                        <div class="prio-card-left">
                                            <div class="prio-icon-wrap">
                                                <i class="fa-solid ${icon}"></i>
                                            </div>
                                            <div class="prio-name">${factorName}</div>
                                        </div>
                                        <span class="prio-badge ${prioClass}">${prioText}</span>
                                    </div>
                                    <p class="prio-desc">${rec.recommendation_text}</p>
                                    ${actionBtnHtml}
                                </div>`;
                        });
                        const container = document.getElementById('priority-cards-container');
                        if (container) {
                            container.innerHTML = prioCardsHtml;
                        }
                    }
                })
                .catch(err => console.error('Poller error:', err));
        }, 10000);
    }

    document.addEventListener('DOMContentLoaded', startRekomendasiPoller);
</script>
@endsection
