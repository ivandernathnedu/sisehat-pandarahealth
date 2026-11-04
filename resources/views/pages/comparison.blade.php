@extends('layouts.app')

@section('title', 'Perbandingan Faktor')

@section('styles')
<style>
    /* Header */
    .comp-header { margin-bottom: 32px; }
    .comp-title { font-size: 34px; font-weight: 800; margin-bottom: 10px; letter-spacing: -0.5px; }
    .comp-desc { font-size: 13px; color: var(--text-secondary); line-height: 1.7; max-width: 520px; }
    .comp-header-inner { display: flex; justify-content: space-between; align-items: flex-start; gap: 24px; }
    .comp-actions { display: flex; gap: 10px; align-items: flex-start; flex-shrink: 0; }
    .btn-date {
        background: var(--card-color); border: 1px solid var(--border-color);
        border-radius: 12px; padding: 0 18px; color: #fff; font-size: 13px;
        font-family: 'Inter', sans-serif; cursor: pointer; display: flex; align-items: center;
        gap: 12px; text-align: left; line-height: 1.3; transition: all 0.2s;
        height: 56px;
    }
    .btn-date:hover { border-color: #444; background: #1a1a1a; }
    .btn-analisis {
        background: #fff; border: none; border-radius: 12px;
        padding: 0 20px; color: #000; font-size: 13px; font-weight: 700;
        font-family: 'Inter', sans-serif; cursor: pointer; display: flex; align-items: center;
        justify-content: center; gap: 10px; height: 56px; transition: all 0.2s;
    }
    .btn-analisis:hover { background: #e5e5e5; transform: translateY(-1px); }

    /* Main Grid */
    .main-grid { display: grid; grid-template-columns: 1.65fr 1fr; gap: 20px; margin-bottom: 20px; }
    .hide-on-desktop { display: none !important; }
    .show-on-desktop { display: flex !important; }

    @media (max-width: 1024px) {
        .main-grid { grid-template-columns: 1fr; }
        .comp-header-inner { flex-direction: column; align-items: flex-start; }
        .comp-actions { width: 100%; }
        .comp-actions select, .comp-actions .btn-date { flex: 1; }
        .hide-on-desktop { display: flex !important; }
        .show-on-desktop { display: none !important; }
    }

    /* Cards */
    .card2 { background: var(--card-color); border: 1px solid var(--border-color); border-radius: 14px; padding: 24px; }

    .card-title-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
    .card-icon-title { display: flex; align-items: center; gap: 14px; }
    .card-icon-title i { font-size: 20px; color: var(--text-secondary); }
    .card-h { font-size: 18px; font-weight: 700; line-height: 1.3; }
    .legend-row { display: flex; gap: 14px; font-size: 11px; color: var(--text-secondary); align-items: center; flex-shrink: 0; padding-top: 4px; }
    .legend-row span { display: inline-flex; align-items: center; gap: 5px; }
    .leg-sq { width: 10px; height: 10px; border-radius: 2px; flex-shrink: 0; }
    .leg-ci { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

    /* Ranking */
    .rank-list { display: flex; flex-direction: column; gap: 6px; }
    .rank-item {
        display: flex; align-items: center; gap: 14px;
        padding: 13px 14px; border-radius: 10px;
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--border-color);
    }
    .rn { font-size: 13px; font-weight: 700; min-width: 16px; color: var(--text-secondary); }
    .rn.g { color: #ffffffff; } .rn.r { color: #f87171; }
    .rb { flex: 1; min-width: 0; }
    .rb-name { font-size: 13px; font-weight: 600; margin-bottom: 6px; }
    .rb-track { height: 3px; background: #2a2a2a; border-radius: 2px; }
    .rb-fill { height: 100%; border-radius: 2px; }
    .rr { text-align: right; flex-shrink: 0; }
    .rr-score { font-size: 17px; font-weight: 700; }
    .rr-score.g { color: #4ade80; } .rr-score.r { color: #f87171; }
    .rr-delta { font-size: 10px; font-weight: 600; margin-top: 2px; }
    .rr-delta.g { color: #4ade80; } .rr-delta.r { color: #f87171; }

    /* --- UMKM Dropdown --- */
    .umkm-select {
        background: #1a1a1a;
        border: 1px solid var(--border-color);
        color: var(--text-primary);
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 6px;
        outline: none;
        cursor: pointer;
    }
    .umkm-select:hover { border-color: #444; }
</style>
@endsection

@section('content')

{{-- Header --}}
<div class="comp-header">
    <div class="comp-header-inner">
        <div>
            <h1 class="comp-title" style="display:flex; align-items:center; gap:12px;">
                {{ optional($assessment_info)->umkm->nama_umkm ?? 'Analisis UMKM' }}
                @if(isset($all_umkms) && $all_umkms->count() > 1)
                    <select class="umkm-select" onchange="window.location.href='?umkm_id=' + this.value">
                        @foreach($all_umkms as $u)
                            <option value="{{ $u->umkm_id }}" {{ $selected_umkm_id == $u->umkm_id ? 'selected' : '' }}>
                                {{ $u->nama_umkm }}
                            </option>
                        @endforeach
                    </select>
                @endif
            </h1>
            <p class="comp-desc">Perbandingan mendalam performa kesehatan organisasi berdasarkan 6 faktor utama dibandingkan dengan periode sebelumnya dan rata-rata industri.</p>
        </div>
        <div class="comp-actions">
            @if(isset($assessment_history) && $assessment_history->count() > 0)
                <select onchange="window.location.href='?umkm_id={{ $selected_umkm_id }}&assessment_id=' + this.value" style="background: var(--card-color); border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; color: white; font-size: 14px; outline: none; cursor: pointer; height: 56px;">
                    @foreach($assessment_history as $history)
                        <option value="{{ $history->assessment_id }}" {{ $selected_assessment_id == $history->assessment_id ? 'selected' : '' }}>
                            Periode: {{ \Carbon\Carbon::parse($history->assessment_date)->format('F Y') }} (Ke-{{ $history->sequence_in_month ?? 1 }})
                        </option>
                    @endforeach
                </select>
            @else
                <button class="btn-date">
                    <i class="fa-regular fa-calendar-days" style="color:var(--text-secondary); font-size:16px;"></i>
                    <span>{{ $assessment_info ? \Carbon\Carbon::parse($assessment_info->assessment_date)->format('F, Y') : 'N/A' }}</span>
                </button>
            @endif
            <button class="btn-analisis" onclick="window.location.href='{{ route('assessment') }}'">
                <i class="fa-solid fa-chart-simple"></i> Analisis Baru
            </button>
        </div>
    </div>
</div>

{{-- Main 2-col Grid --}}
<div class="main-grid">

    {{-- Perbandingan Faktor Antar Periode --}}
    <div class="card2">
        <div class="card-title-row">
            <div class="card-icon-title">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <div class="card-h">Perbandingan Faktor<br>Antar Periode</div>
            </div>
            <div class="legend-row show-on-desktop">
                <span><span class="leg-sq" style="background:#818cf8;"></span> Periode Saat Ini</span>
                <span><span class="leg-sq" style="background:rgba(255,255,255,0.2);"></span> Periode Sebelumnya</span>
            </div>
        </div>
        <div style="height:300px;">
            <canvas id="periodComparisonChart"></canvas>
        </div>
        <div class="legend-row hide-on-desktop" style="justify-content: center; margin-top: 16px;">
            <span><span class="leg-sq" style="background:#818cf8;"></span> Periode Saat Ini</span>
            <span><span class="leg-sq" style="background:rgba(255,255,255,0.2);"></span> Periode Sebelumnya</span>
        </div>
    </div>

    {{-- Peringkat Faktor --}}
    <div class="card2">
        <div style="font-size: 18px; font-weight: 700; margin-bottom: 24px; display: flex; align-items: center; gap: 14px;">
            <i class="fa-solid fa-bars-staggered" style="font-size: 20px; color: var(--text-secondary);"></i> Peringkat Faktor
        </div>
        <div class="rank-list" id="rank-list">
            @foreach($rankings['all'] as $index => $item)
                @php
                    $scoreColorClass = '';
                    if ($item['trend'] === 'up') $scoreColorClass = 'g';
                    if ($item['trend'] === 'down') $scoreColorClass = 'r';
                @endphp
                <div class="rank-item">
                    <div class="rn {{ $scoreColorClass }}">{{ $index + 1 }}</div>
                    <div class="rb">
                        <div class="rb-name">{{ $item['factor'] }}</div>
                        <div class="rb-track">
                            <div class="rb-fill" style="width:{{ $item['score'] }}%; background:{{ $item['score'] >= 60 ? '#4ade80' : ($item['score'] >= 40 ? '#facc15' : '#f87171') }};"></div>
                        </div>
                    </div>
                    <div class="rr">
                        <div class="rr-score {{ $scoreColorClass }}">{{ $item['score'] }}</div>
                        @if($item['change_label'] !== 'Data Baru')
                            <div class="rr-delta {{ $scoreColorClass }}">
                                {{ $item['change_label'] }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Industry Comparison --}}
<div class="card2" style="margin-bottom:32px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap: 16px;" class="comp-header-inner">
        <div>
            <div style="font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 14px; margin-bottom: 4px;">
                <i class="fa-solid fa-city" style="font-size: 20px; color: var(--text-secondary);"></i>
                Perbandingan Industri
            </div>
            <div style="font-size:12px; color:var(--text-secondary);">
                {{ optional($assessment_info)->umkm->nama_umkm ?? 'UMKM' }} vs Rata-rata {{ $industry_benchmark['peer_count'] ?? 0 }} UMKM sejenis ({{ $industry_benchmark['sektor_usaha'] ?? 'Semua Sektor' }})
            </div>
        </div>
        <div class="legend-row show-on-desktop">
            <span><span class="leg-ci" style="background:#22d3ee;"></span> {{ optional($assessment_info)->umkm->nama_umkm ?? 'UMKM' }}</span>
            <span><span class="leg-ci" style="background:rgba(255,255,255,0.2);"></span> Rata-rata Industri</span>
        </div>
    </div>
    <div style="height:320px;">
        <canvas id="industryComparisonChart"></canvas>
    </div>
    <div class="legend-row hide-on-desktop" style="justify-content: center; margin-top: 16px;">
        <span><span class="leg-ci" style="background:#22d3ee;"></span> {{ optional($assessment_info)->umkm->nama_umkm ?? 'UMKM' }}</span>
        <span><span class="leg-ci" style="background:rgba(255,255,255,0.2);"></span> Rata-rata Industri</span>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const radarData = @json($radar_chart);
    const factors = radarData.map(d => d.factor);
    const currVals = radarData.map(d => d.score);
    const prevVals = radarData.map(d => d.prev_score || 0);
    const avgVals = radarData.map(d => d.industry_avg || 0);

    const abbreviationMap = {
        'organizational values': 'OV',
        'organization value': 'OV',
        'leader involvement': 'LDI',
        'institutional resources': 'INS',
        'institutional resource': 'INS',
        'operational stability': 'OPS',
        'operational stablity': 'OPS',
        'work environment quality': 'WEQ',
        'work enviorment quality': 'WEQ',
        'economics performance': 'ECT',
        'economic performance': 'ECT',
        'economic performence': 'ECT',
        'echonomic performence': 'ECT'
    };

    const tooltipCfg = {
        backgroundColor: '#1a1a1a', borderColor: '#333', borderWidth: 1,
        titleColor: '#a3a3a3', bodyColor: '#fff', padding: 10,
        callbacks: {
            title: (tooltipItems) => {
                const index = tooltipItems[0].dataIndex;
                return factors[index] || '';
            }
        }
    };

    // ── Period Comparison ──────────────────────────────────
    window.periodComparisonChart = new Chart(document.getElementById('periodComparisonChart'), {
        type: 'bar',
        data: {
            labels: factors,
            datasets: [
                { label: 'Saat Ini', data: currVals, backgroundColor: '#818cf8', borderRadius: 6, borderSkipped: false },
                { label: 'Sebelumnya', data: prevVals, backgroundColor: 'rgba(255,255,255,0.1)', borderRadius: 6, borderSkipped: false }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: tooltipCfg },
            scales: {
                y: { beginAtZero: true, max: 100, grid: { color: '#222' }, ticks: { color: '#a3a3a3', stepSize: 25 } },
                x: { 
                    grid: { display: false }, 
                    ticks: { 
                        color: '#a3a3a3', 
                        font: { size: 10 },
                        callback: function(value, index, values) {
                            const label = this.getLabelForValue(value);
                            if (window.innerWidth <= 1024) {
                                const factorLower = label.toLowerCase().trim();
                                return abbreviationMap[factorLower] || label;
                            }
                            return label;
                        }
                    } 
                }
            }
        }
    });

    // ── Industry Comparison ────────────────────────────────
    // Custom plugin: draw score labels below each bar pair
    const scoreLabelPlugin = {
        id: 'scoreLabelPlugin',
        afterDraw(chart) {
            const { ctx, chartArea, data } = chart;
            const meta0 = chart.getDatasetMeta(0).data;
            const meta1 = chart.getDatasetMeta(1).data;
            const y = chartArea.bottom + 12; // Adjusted to sit just below the bars
            data.labels.forEach((_, i) => {
                const midX = (meta0[i].x + meta1[i].x) / 2;
                const v0 = data.datasets[0].data[i];
                const v1 = data.datasets[1].data[i];
                ctx.save();
                ctx.fillStyle = '#666';
                ctx.font = '700 10px Inter, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText(`${v0}   ${v1}`, midX, y);
                ctx.restore();
            });
        }
    };

    window.industryComparisonChart = new Chart(document.getElementById('industryComparisonChart'), {
        type: 'bar',
        data: {
            labels: factors,
            datasets: [
                {
                    label: 'Skor Saat Ini',
                    data: currVals,
                    backgroundColor: '#22d3ee',
                    borderRadius: 6, borderSkipped: false
                },
                {
                    label: 'Rata-rata Industri',
                    data: avgVals,
                    backgroundColor: 'rgba(255,255,255,0.1)',
                    borderRadius: 6, borderSkipped: false
                }
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            layout: { padding: { bottom: 20 } },
            plugins: { legend: { display: false }, tooltip: tooltipCfg },
            scales: {
                y: { beginAtZero: true, max: 100, grid: { color: '#222' }, ticks: { color: '#a3a3a3', stepSize: 25 } },
                x: { 
                    grid: { display: false }, 
                    ticks: { 
                        color: '#a3a3a3', 
                        font: { size: 10 },
                        padding: 18, // Increase padding to make room for scores
                        callback: function(value, index, values) {
                            const label = this.getLabelForValue(value);
                            if (window.innerWidth <= 1024) {
                                const factorLower = label.toLowerCase().trim();
                                return abbreviationMap[factorLower] || label;
                            }
                            return label;
                        }
                    } 
                }
            }
        },
        plugins: [scoreLabelPlugin]
    });

    function startComparisonPoller() {
        setInterval(() => {
            fetch('{{ route('api.realtime.comparison') }}?umkm_id={{ $selected_umkm_id }}&assessment_id={{ $selected_assessment_id }}')
                .then(res => res.json())
                .then(data => {
                    if (data.error) return;

                    // 1. Update charts
                    if (data.radar_chart) {
                        const newFactors = data.radar_chart.map(d => d.factor);
                        const newCurrVals = data.radar_chart.map(d => d.score);
                        const newPrevVals = data.radar_chart.map(d => d.prev_score || 0);
                        const newAvgVals = data.radar_chart.map(d => d.industry_avg || 0);

                        if (window.periodComparisonChart) {
                            window.periodComparisonChart.data.labels = newFactors;
                            window.periodComparisonChart.data.datasets[0].data = newCurrVals;
                            window.periodComparisonChart.data.datasets[1].data = newPrevVals;
                            window.periodComparisonChart.update();
                        }

                        if (window.industryComparisonChart) {
                            window.industryComparisonChart.data.labels = newFactors;
                            window.industryComparisonChart.data.datasets[0].data = newCurrVals;
                            window.industryComparisonChart.data.datasets[1].data = newAvgVals;
                            window.industryComparisonChart.update();
                        }
                    }

                    // 2. Update rankings list
                    if (data.rankings && data.rankings.all) {
                        let rankHtml = '';
                        data.rankings.all.forEach((item, index) => {
                            let scoreColorClass = '';
                            if (item.trend === 'up') scoreColorClass = 'g';
                            if (item.trend === 'down') scoreColorClass = 'r';

                            const barColor = item.score >= 60 ? '#4ade80' : (item.score >= 40 ? '#facc15' : '#f87171');
                            const changeLabelHtml = item.change_label !== 'Data Baru' ? `<div class="rr-delta ${scoreColorClass}">${item.change_label}</div>` : '';

                            rankHtml += `
                                <div class="rank-item">
                                    <div class="rn ${scoreColorClass}">${index + 1}</div>
                                    <div class="rb">
                                        <div class="rb-name">${item.factor}</div>
                                        <div class="rb-track">
                                            <div class="rb-fill" style="width:${item.score}%; background:${barColor};"></div>
                                        </div>
                                    </div>
                                    <div class="rr">
                                        <div class="rr-score ${scoreColorClass}">${item.score}</div>
                                        ${changeLabelHtml}
                                    </div>
                                </div>`;
                        });
                        const rankListEl = document.getElementById('rank-list');
                        if (rankListEl) {
                            rankListEl.innerHTML = rankHtml;
                        }
                    }
                })
                .catch(err => console.error('Poller error:', err));
        }, 10000);
    }

    document.addEventListener('DOMContentLoaded', startComparisonPoller);
</script>
@endsection
