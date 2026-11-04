@extends('layouts.app')

@section('title', 'Profil 6 Faktor')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/@sgratzl/chartjs-chart-boxplot@4/build/index.umd.min.js"></script>
<style>
    .profil-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 24px;
    }
    
    @media (max-width: 1024px) {
        .profil-grid { grid-template-columns: repeat(2, 1fr); gap: 16px; }
    }
    
    @media (max-width: 768px) {
        .profil-grid { 
            display: flex; 
            overflow-x: auto; 
            scroll-snap-type: x mandatory; 
            -webkit-overflow-scrolling: touch; 
            padding-bottom: 12px; 
        }
        .profil-grid > .card { 
            min-width: 260px; 
            flex-shrink: 0; 
            scroll-snap-align: start; 
        }
        .header-section { flex-direction: column; align-items: flex-start; gap: 16px; }
        .header-section > div:last-child { flex-direction: column; width: 100%; }
        .header-section select { width: 100%; }
    }

    .chart-scroll-wrap {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        background: rgba(0,0,0,0.1);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
    }
    .chart-inner-container {
        height: 320px;
        position: relative;
    }
    @media (max-width: 768px) {
        .chart-inner-container {
            min-width: 600px;
        }
    }
</style>
@endsection

@section('content')
<div class="header-section mb-32 flex-between">
    <div>
        <h1 style="font-size: 28px; margin-bottom: 8px;">Profil 6 Faktor</h1>
        <p style="color: var(--text-secondary);">Analisis mendalam terhadap dimensi kesehatan internal organisasi.</p>
    </div>
    <div style="display: flex; gap: 12px; width: 100%; max-width: 500px; justify-content: flex-end;">
        @if(isset($all_umkms) && $all_umkms->count() > 1)
            <select onchange="window.location.href='?umkm_id=' + this.value" style="background: var(--card-color); border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; color: white; font-size: 14px; outline: none; cursor: pointer; flex: 1;">
                @foreach($all_umkms as $u)
                    <option value="{{ $u->umkm_id }}" {{ $selected_umkm_id == $u->umkm_id ? 'selected' : '' }}>
                        {{ $u->nama_umkm }}
                    </option>
                @endforeach
            </select>
        @else
            <select style="background: var(--card-color); border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; color: white; font-size: 14px; outline: none; flex: 1;">
                <option>{{ optional($assessment_info)->umkm->nama_umkm ?? 'UMKM' }}</option>
            </select>
        @endif
        @if(isset($assessment_history) && $assessment_history->count() > 0)
            <select onchange="window.location.href='?umkm_id={{ $selected_umkm_id }}&assessment_id=' + this.value" style="background: var(--card-color); border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; color: white; font-size: 14px; outline: none; cursor: pointer; flex: 1;">
                @foreach($assessment_history as $history)
                    <option value="{{ $history->assessment_id }}" {{ $selected_assessment_id == $history->assessment_id ? 'selected' : '' }}>
                        Periode: {{ \Carbon\Carbon::parse($history->assessment_date)->format('F Y') }} (Ke-{{ $history->sequence_in_month ?? 1 }}) - {{ ucfirst($history->status) }}
                    </option>
                @endforeach
            </select>
        @else
            <select style="background: var(--card-color); border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; color: white; font-size: 14px; outline: none; flex: 1;">
                <option>Periode: {{ $assessment_info ? \Carbon\Carbon::parse($assessment_info->assessment_date)->format('F Y') : 'N/A' }} (Ke-1)</option>
            </select>
        @endif
    </div>
</div>

<div class="profil-grid">
    <!-- Overall Score -->
    <div class="card" style="display: flex; flex-direction: column; justify-content: center;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
            <i class="fa-solid fa-shield-heart" style="font-size: 20px; color: var(--success);"></i>
            <span style="font-weight: 600; font-size: 14px;">Skor Kesehatan Keseluruhan</span>
        </div>
        <div style="display: flex; align-items: baseline; gap: 8px;">
            <span id="overall-score-val" style="font-size: 48px; font-weight: 700;">{{ $health_score ? number_format($health_score->overall_score, 1) : '0.0' }}</span>
            <span style="font-size: 20px; color: var(--text-secondary);">/ 100</span>
        </div>
        <div style="margin-top: 16px;">
            @php
                $scoreValue = $health_score->overall_score ?? 0;
                
                // Logika Warna Baru (Interval 25%)
                if ($scoreValue > 75) {
                    $scoreColor = 'var(--success)';
                    $scoreBg = 'rgba(74, 222, 128, 0.1)';
                } elseif ($scoreValue > 50) {
                    $scoreColor = '#818cf8'; // Biru Indigo
                    $scoreBg = 'rgba(129, 140, 248, 0.1)';
                } elseif ($scoreValue > 25) {
                    $scoreColor = 'var(--warning)';
                    $scoreBg = 'rgba(250, 204, 21, 0.1)';
                } else {
                    $scoreColor = 'var(--danger)';
                    $scoreBg = 'rgba(248, 113, 113, 0.1)';
                }
                
                // Kategori sekarang sudah dalam bahasa Indonesia dari DB
                $category = $health_score ? $health_score->category : 'N/A';
            @endphp
            <span id="overall-score-category" style="font-size: 10px; padding: 4px 8px; background: {{ $scoreBg }}; color: {{ $scoreColor }}; border-radius: 4px; font-weight: 700;">{{ str_replace('_', ' ', strtoupper($category)) }}</span>
        </div>
    </div>

    <!-- Highest Factor -->
    <div class="card">
        <div class="flex-between mb-16">
            <span style="font-size: 12px; color: var(--text-secondary); text-transform: uppercase;">Faktor Tertinggi</span>
            <i class="fa-solid fa-arrow-trend-up" style="color: var(--success);"></i>
        </div>
        <h3 id="highest-factor-title" style="font-size: 20px; margin-bottom: 12px;">{{ $highlights['highest']['factor'] ?? 'N/A' }}</h3>
        <div style="height: 8px; background: #222; border-radius: 4px; margin-bottom: 8px;"><div id="highest-factor-fill" style="width: {{ $highlights['highest']['score'] ?? 0 }}%; height: 100%; background: var(--success); border-radius: 4px;"></div></div>
        <span id="highest-factor-score" style="font-size: 12px; font-weight: 600;">{{ $highlights['highest']['score'] ?? 0 }}%</span>
    </div>

    <!-- Lowest Factor -->
    <div class="card">
        <div class="flex-between mb-16">
            <span style="font-size: 12px; color: var(--text-secondary); text-transform: uppercase;">Faktor Terendah</span>
            <i class="fa-solid fa-arrow-trend-down" style="color: var(--danger);"></i>
        </div>
        <h3 id="lowest-factor-title" style="font-size: 20px; margin-bottom: 12px;">{{ $highlights['lowest']['factor'] ?? 'N/A' }}</h3>
        <div style="height: 8px; background: #222; border-radius: 4px; margin-bottom: 8px;"><div id="lowest-factor-fill" style="width: {{ $highlights['lowest']['score'] ?? 0 }}%; height: 100%; background: var(--danger); border-radius: 4px;"></div></div>
        <span id="lowest-factor-score" style="font-size: 12px; font-weight: 600;">{{ $highlights['lowest']['score'] ?? 0 }}%</span>
    </div>
</div>

<!-- Active Insight -->
<div class="card" style="background: linear-gradient(90deg, #161616 0%, #1a1a1a 100%); border: 1px solid var(--border-color); display: flex; gap: 20px; align-items: center; padding: 20px;">
    <div style="width: 40px; height: 40px; background: rgba(99, 102, 241, 0.1); color: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
        <i class="fa-solid fa-lightbulb"></i>
    </div>
    <div>
        <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px; font-weight: 600;">Active Insight</div>
        <p id="active-insight-text" style="font-size: 14px;">{!! $recommendations->first()->recommendation_text ?? 'Tidak ada wawasan aktif.' !!}</p>
    </div>
</div>

<div style="display: flex; flex-direction: column; gap: 24px; margin-top: 24px;">
    <!-- Radar Chart -->
    <div class="card">
        <h3 class="card-title">Factor Connectivity Map</h3>
        <div style="height: 400px;">
            <canvas id="factorRadarChart"></canvas>
        </div>
    </div>

    <!-- Outlier Analysis -->
    <div class="card">
        <div class="flex-between mb-24">
            <div>
                <h3 class="card-title" style="margin-bottom: 4px;">Outlier Analysis</h3>
                <p style="font-size: 12px; color: var(--text-secondary);">Deteksi anomali skor kesehatan pada tingkat ekosistem UMKM.</p>
            </div>
            <i class="fa-solid fa-ellipsis" style="color: var(--text-secondary); cursor: pointer;"></i>
        </div>
        
        <div class="chart-scroll-wrap">
            <div class="chart-inner-container">
                <canvas id="outlierChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const radarData = @json($radar_chart);
    const radarLabels = radarData.map(d => [d.factor, d.score + '%']);
    const radarScores = radarData.map(d => d.score);

    // Radar Chart
    window.factorRadarChart = new Chart(document.getElementById('factorRadarChart'), {
        type: 'radar',
        data: {
            labels: radarLabels,
            datasets: [{
                label: 'Profil Saat Ini',
                data: radarScores,
                fill: true,
                backgroundColor: 'rgba(255, 255, 255, 0.1)',
                borderColor: '#ffffff',
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#ffffff'
            }]
        },
        options: {
            scales: {
                r: {
                    angleLines: { color: '#333' },
                    grid: { color: '#333' },
                    pointLabels: { color: '#a3a3a3', font: { size: 12 } },
                    ticks: { display: false },
                    suggestedMin: 0,
                    suggestedMax: 100
                }
            },
            plugins: { legend: { display: false } },
            maintainAspectRatio: false
        }
    });

    window.outlierData = @json($outliers);
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
    const boxplotLabels = window.outlierData.map(d => d.nama_factor);
    const boxplotDataset = window.outlierData.map(d => d.stats);

    // Outlier Box Plot Chart
    window.outlierChart = new Chart(document.getElementById('outlierChart'), {
        type: 'boxplot',
        data: {
            labels: boxplotLabels,
            datasets: [
                {
                    label: 'Sebaran Skor (0-100%)',
                    data: boxplotDataset,
                    backgroundColor: 'rgba(99, 102, 241, 0.3)',
                    borderColor: '#818cf8',
                    borderWidth: 1.5,
                    outlierBackgroundColor: '#f87171',
                    outlierBorderColor: '#f87171',
                    outlierRadius: 5,
                    medianColor: '#facc15',
                    itemRadius: 2,
                    itemBackgroundColor: 'rgba(129,140,248,0.4)'
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    labels: { color: '#a3a3a3', font: { size: 11 }, boxWidth: 12 }
                },
                tooltip: {
                    backgroundColor: '#1a1a1a',
                    borderColor: '#333',
                    borderWidth: 1,
                    titleColor: '#a3a3a3',
                    bodyColor: '#fff',
                    callbacks: {
                        title: (tooltipItems) => {
                            const index = tooltipItems[0].dataIndex;
                            return window.outlierData[index] ? window.outlierData[index].nama_factor : '';
                        },
                        label: (ctx) => {
                            const v = ctx.raw;
                            return [
                                `Q1: ${v.q1} | Median: ${v.median}`,
                                `Q3: ${v.q3} | Min: ${v.min} | Max: ${v.max}`
                            ];
                        }
                    }
                }
            },
            scales: {
                y: {
                    min: 0, max: 105,
                    title: {
                        display: true,
                        text: 'Tingkat Kepuasan/Kepatuhan (%)'
                    },
                    grid: { color: '#222' },
                    ticks: { color: '#a3a3a3', stepSize: 10 }
                },
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

    function startProfilFaktorPoller() {
        setInterval(() => {
            fetch('{{ route('api.realtime.profil-faktor') }}?umkm_id={{ $selected_umkm_id }}&assessment_id={{ $selected_assessment_id }}')
                .then(res => res.json())
                .then(data => {
                    if (data.error) return;

                    // 1. Update overall score text
                    if (data.health_score) {
                        const score = parseFloat(data.health_score.overall_score).toFixed(1);
                        document.getElementById('overall-score-val').innerText = score;
                        
                        let bColor = '#f87171';
                        let bBg = 'rgba(248,113,113,.12)';
                        if (score > 75) { bColor = '#4ade80'; bBg = 'rgba(74,222,128,.12)'; }
                        else if (score > 50) { bColor = '#818cf8'; bBg = 'rgba(129,140,248,.12)'; }
                        else if (score > 25) { bColor = '#facc15'; bBg = 'rgba(250,204,21,.12)'; }

                        const badge = document.getElementById('overall-score-category');
                        if (badge) {
                            badge.style.color = bColor;
                            badge.style.backgroundColor = bBg;
                            badge.innerText = data.health_score.category.replace('_', ' ').toUpperCase();
                        }
                    }

                    // 2. Update Highlights
                    if (data.highlights) {
                        if (data.highlights.highest) {
                            document.getElementById('highest-factor-title').innerText = data.highlights.highest.factor || 'N/A';
                            document.getElementById('highest-factor-fill').style.width = (data.highlights.highest.score || 0) + '%';
                            document.getElementById('highest-factor-score').innerText = (data.highlights.highest.score || 0) + '%';
                        }
                        if (data.highlights.lowest) {
                            document.getElementById('lowest-factor-title').innerText = data.highlights.lowest.factor || 'N/A';
                            document.getElementById('lowest-factor-fill').style.width = (data.highlights.lowest.score || 0) + '%';
                            document.getElementById('lowest-factor-score').innerText = (data.highlights.lowest.score || 0) + '%';
                        }
                    }

                    // 3. Update Active Insight
                    if (data.recommendations && data.recommendations.length > 0) {
                        document.getElementById('active-insight-text').innerHTML = data.recommendations[0].recommendation_text;
                    }

                    // 4. Update Radar Chart
                    if (window.factorRadarChart && data.radar_chart) {
                        const radarLabels = data.radar_chart.map(d => [d.factor, d.score + '%']);
                        const radarScores = data.radar_chart.map(d => d.score);
                        window.factorRadarChart.data.labels = radarLabels;
                        window.factorRadarChart.data.datasets[0].data = radarScores;
                        window.factorRadarChart.update();
                    }

                    // 5. Update Outlier Boxplot Chart
                    if (window.outlierChart && data.outliers) {
                        window.outlierData = data.outliers; // update global ref for tooltip
                        const boxplotLabels = data.outliers.map(d => d.nama_factor);
                        const boxplotDataset = data.outliers.map(d => d.stats);
                        window.outlierChart.data.labels = boxplotLabels;
                        window.outlierChart.data.datasets[0].data = boxplotDataset;
                        window.outlierChart.update();
                    }
                })
                .catch(err => console.error('Poller error:', err));
        }, 10000);
    }

    document.addEventListener('DOMContentLoaded', startProfilFaktorPoller);
</script>
@endsection
