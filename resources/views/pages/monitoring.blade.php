@extends('layouts.app')

@section('title', 'Monitoring Live')

@section('styles')
<style>
    .monitoring-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }
    @media (max-width: 768px) {
        .monitoring-grid { grid-template-columns: 1fr; }
        .header-section { flex-direction: column; align-items: flex-start; gap: 16px; }
        .header-section > div:last-child { width: 100%; justify-content: space-between; }
    }
</style>
@endsection

@section('content')
<div class="header-section mb-32 flex-between">
    <div>
        <h1 style="font-size: 28px; margin-bottom: 8px;">Monitoring Live</h1>
        <p style="color: var(--text-secondary);">Pantau partisipasi karyawan secara real-time selama periode assessment.</p>
    </div>
    <div style="display: flex; align-items: center; gap: 12px;">
        @if(isset($all_umkms) && $all_umkms->count() > 1)
            <select onchange="window.location.href='?umkm_id=' + this.value" style="background: var(--card-color); border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; color: white; font-size: 14px; outline: none; cursor: pointer;">
                @foreach($all_umkms as $u)
                    <option value="{{ $u->umkm_id }}" {{ $selected_umkm_id == $u->umkm_id ? 'selected' : '' }}>
                        {{ $u->nama_umkm }}
                    </option>
                @endforeach
            </select>
        @endif
        <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; background: rgba(74, 222, 128, 0.1); color: var(--success); padding: 8px 16px; border-radius: 20px;">
            <span style="width: 8px; height: 8px; background: var(--success); border-radius: 50%;"></span> Koneksi Aktif
        </div>
    </div>
</div>

<div class="monitoring-grid">
    <!-- Total Responden -->
    <div class="card">
        <span class="stat-label">Total Responden</span>
        <div class="flex-between" style="margin-top: 12px;">
            <div>
                <div id="total-respondents-val" style="font-size: 36px; font-weight: 700;">{{ number_format($total_respondents) }} <small style="font-size: 16px; font-weight: 400; color: var(--text-secondary);">/ {{ number_format($target_respondents) }}</small></div>
                <div style="font-size: 12px; color: var(--success); margin-top: 8px;"><i class="fa-solid fa-chart-line"></i> Sedang Berlangsung</div>
            </div>
            <i class="fa-solid fa-users" style="font-size: 32px; color: var(--text-secondary); opacity: 0.3;"></i>
        </div>
    </div>

    <!-- Tingkat Penyelesaian -->
    <div class="card">
        <span class="stat-label">Tingkat Penyelesaian</span>
        <div style="display: flex; align-items: center; gap: 24px; margin-top: 12px;">
            <div style="width: 80px; height: 80px; position: relative; display: flex; align-items: center; justify-content: center;">
                <canvas id="completionChart"></canvas>
                <div id="completion-rate-pct" style="position: absolute; font-size: 16px; font-weight: 700; color: #fff;">{{ $completion_rate }}%</div>
            </div>
            <div id="completion-status-wrap">
                <div style="font-size: 14px; margin-bottom: 4px;">Target minimal {{ number_format($target_respondents) }} responden.</div>
                @if($completion_rate >= 80)
                    <div style="font-size: 12px; color: var(--success);"><i class="fa-solid fa-circle-check"></i> Optimal</div>
                @else
                    <div style="font-size: 12px; color: var(--warning);"><i class="fa-solid fa-clock"></i> Belum Tercapai</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Skor Kesehatan Estimasi -->
<div class="card">
    <div class="flex-between mb-32">
        <span class="stat-label">Skor Kesehatan (Estimasi)</span>
        @php
            // Logika Warna Baru (Interval 25%)
            if ($estimated_score > 75) {
                $catColor = 'var(--success)';
                $catBg = 'rgba(74,222,128,0.1)';
            } elseif ($estimated_score > 50) {
                $catColor = '#818cf8'; // Biru Indigo
                $catBg = 'rgba(129, 140, 248, 0.1)';
            } elseif ($estimated_score > 25) {
                $catColor = 'var(--warning)';
                $catBg = 'rgba(250, 204, 21, 0.1)';
            } else {
                $catColor = 'var(--danger)';
                $catBg = 'rgba(248, 113, 113, 0.1)';
            }
        @endphp
        <span id="estimated-category-badge" style="font-size: 10px; padding: 4px 8px; background: {{ $catBg }}; color: {{ $catColor }}; border-radius: 4px; font-weight: bold;">{{ str_replace('_', ' ', strtoupper($estimated_category)) }}</span>
    </div>
    <div id="estimated-score-val" style="font-size: 48px; font-weight: 700; margin-bottom: 8px;">{{ $estimated_score }} <small style="font-size: 20px; font-weight: 400; color: var(--text-secondary);">/ 100</small></div>
    <p style="font-size: 14px; color: var(--text-secondary);">Data fluktuatif hingga assessment ditutup. Skor ini adalah estimasi dari jawaban saat ini.</p>
</div>

<!-- Activity Log -->
<div class="card">
    <h3 id="activity-log-title" class="card-title">Log Aktivitas ({{ count($recent_activity) }} Terbaru)</h3>
    <div class="activity-list" id="activity-list">
        @forelse($recent_activity as $act)
        <div class="flex-between" style="padding: 16px 0; border-bottom: 1px solid var(--border-color);">
            <div style="display: flex; gap: 16px; align-items: center;">
                <div style="width: 32px; height: 32px; background: #222; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-user" style="font-size: 12px;"></i></div>
                <div>
                    <div style="font-size: 14px;">Responden {{ $act->employee_code ?: 'Anonim' }} menyelesaikan assessment.</div>
                    <div style="font-size: 12px; color: var(--text-secondary);">{{ \Carbon\Carbon::parse($act->last_active)->diffForHumans() }}</div>
                </div>
            </div>
        </div>
        @empty
        <div class="flex-between" style="padding: 16px 0; border-bottom: 1px solid var(--border-color);">
            <div style="font-size: 14px; color: var(--text-secondary);">Belum ada aktivitas.</div>
        </div>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<script>
    const compRate = {{ $completion_rate }};
    window.completionChart = new Chart(document.getElementById('completionChart'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [compRate, 100 - compRate],
                backgroundColor: [compRate >= 80 ? '#4ade80' : '#facc15', '#222222'],
                borderWidth: 0,
                cutout: '80%'
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            maintainAspectRatio: false
        }
    });

    function startMonitoringPoller() {
        setInterval(() => {
            fetch('{{ route('api.realtime.monitoring') }}?umkm_id={{ $selected_umkm_id }}')
                .then(res => res.json())
                .then(data => {
                    if (data.error) return;

                    // Helper for formatting integers
                    const formatNumber = num => new Intl.NumberFormat().format(num);

                    // 1. Update Total Responden
                    const totalValEl = document.getElementById('total-respondents-val');
                    if (totalValEl) {
                        totalValEl.innerHTML = `${formatNumber(data.total_respondents)} <small style="font-size: 16px; font-weight: 400; color: var(--text-secondary);">/ ${formatNumber(data.target_respondents)}</small>`;
                    }

                    // 2. Update Completion text & chart
                    const pctEl = document.getElementById('completion-rate-pct');
                    if (pctEl) {
                        pctEl.innerText = `${data.completion_rate}%`;
                    }

                    if (window.completionChart) {
                        window.completionChart.data.datasets[0].data = [data.completion_rate, 100 - data.completion_rate];
                        window.completionChart.data.datasets[0].backgroundColor = [
                            data.completion_rate >= 80 ? '#4ade80' : '#facc15', 
                            '#222222'
                        ];
                        window.completionChart.update();
                    }

                    // 3. Update completion status wrap
                    const statusWrap = document.getElementById('completion-status-wrap');
                    if (statusWrap) {
                        const statusBadgeHtml = data.completion_rate >= 80 ? 
                            `<div style="font-size: 12px; color: var(--success);"><i class="fa-solid fa-circle-check"></i> Optimal</div>` :
                            `<div style="font-size: 12px; color: var(--warning);"><i class="fa-solid fa-clock"></i> Belum Tercapai</div>`;
                        
                        statusWrap.innerHTML = `
                            <div style="font-size: 14px; margin-bottom: 4px;">Target minimal ${formatNumber(data.target_respondents)} responden.</div>
                            ${statusBadgeHtml}
                        `;
                    }

                    // 4. Update Estimated Health Score & Category
                    if (data.estimated_score !== undefined) {
                        let catColor = 'var(--danger)';
                        let catBg = 'rgba(248, 113, 113, 0.1)';
                        if (data.estimated_score > 75) {
                            catColor = 'var(--success)';
                            catBg = 'rgba(74,222,128,0.1)';
                        } else if (data.estimated_score > 50) {
                            catColor = '#818cf8';
                            catBg = 'rgba(129, 140, 248, 0.1)';
                        } else if (data.estimated_score > 25) {
                            catColor = 'var(--warning)';
                            catBg = 'rgba(250, 204, 21, 0.1)';
                        }

                        const estBadge = document.getElementById('estimated-category-badge');
                        if (estBadge) {
                            estBadge.style.color = catColor;
                            estBadge.style.backgroundColor = catBg;
                            estBadge.innerText = data.estimated_category.replace('_', ' ').toUpperCase();
                        }

                        const estScoreVal = document.getElementById('estimated-score-val');
                        if (estScoreVal) {
                            estScoreVal.innerHTML = `${data.estimated_score} <small style="font-size: 20px; font-weight: 400; color: var(--text-secondary);">/ 100</small>`;
                        }
                    }

                    // 5. Update Activity Log
                    if (data.recent_activity) {
                        // Title count
                        const titleEl = document.getElementById('activity-log-title');
                        if (titleEl) {
                            titleEl.innerText = `Log Aktivitas (${data.recent_activity.length} Terbaru)`;
                        }

                        // Activity List items
                        const listEl = document.getElementById('activity-list');
                        if (listEl) {
                            if (data.recent_activity.length === 0) {
                                listEl.innerHTML = `
                                    <div class="flex-between" style="padding: 16px 0; border-bottom: 1px solid var(--border-color);">
                                        <div style="font-size: 14px; color: var(--text-secondary);">Belum ada aktivitas.</div>
                                    </div>`;
                            } else {
                                // Simple time formatter since JavaScript diffForHumans requires library or helper
                                // We can write a simple helper or just use local time
                                const getRelativeTime = (timestamp) => {
                                    const ms = new Date() - new Date(timestamp);
                                    const sec = Math.floor(ms / 1000);
                                    if (sec < 60) return 'baru saja';
                                    const min = Math.floor(sec / 60);
                                    if (min < 60) return `${min} menit yang lalu`;
                                    const hrs = Math.floor(min / 60);
                                    if (hrs < 24) return `${hrs} jam yang lalu`;
                                    const days = Math.floor(hrs / 24);
                                    return `${days} hari yang lalu`;
                                };

                                let listHtml = '';
                                data.recent_activity.forEach(act => {
                                    const code = act.employee_code || 'Anonim';
                                    const timeStr = getRelativeTime(act.last_active);
                                    listHtml += `
                                        <div class="flex-between" style="padding: 16px 0; border-bottom: 1px solid var(--border-color);">
                                            <div style="display: flex; gap: 16px; align-items: center;">
                                                <div style="width: 32px; height: 32px; background: #222; border-radius: 50%; display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-user" style="font-size: 12px;"></i></div>
                                                <div>
                                                    <div style="font-size: 14px;">Responden ${code} menyelesaikan assessment.</div>
                                                    <div style="font-size: 12px; color: var(--text-secondary);">${timeStr}</div>
                                                </div>
                                            </div>
                                        </div>`;
                                });
                                listEl.innerHTML = listHtml;
                            }
                        }
                    }
                })
                .catch(err => console.error('Poller error:', err));
        }, 10000);
    }

    document.addEventListener('DOMContentLoaded', startMonitoringPoller);
</script>
@endsection
