@extends('layouts.app')

@section('title', 'Pusat Asesmen SiSehat')

@section('styles')
<style>
    .hub-container { max-width: 1000px; margin: 0 auto; }
    .hub-header { margin-bottom: 32px; }
    .hub-title { font-size: 32px; font-weight: 800; margin-bottom: 8px; }
    .hub-subtitle { color: var(--text-secondary); font-size: 14px; }

    /* UMKM Status Grid */
    .status-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 40px; }
    
    @media (max-width: 768px) {
        .status-grid { grid-template-columns: 1fr; }
        .status-card { padding: 20px; }
        .hub-title { font-size: 26px; }
    }
    .status-card {
        background: var(--card-color);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 24px;
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .status-card:hover { border-color: #444; transform: translateY(-2px); }
    
    .card-top { display: flex; justify-content: space-between; align-items: flex-start; }
    .umkm-name { font-size: 18px; font-weight: 700; color: #fff; }
    .umkm-sector { font-size: 11px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; }
    
    .badge-pill { padding: 4px 12px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
    .badge-yet { background: rgba(255,255,255,0.05); color: #888; }
    .badge-doing { background: rgba(250,204,21,0.1); color: #facc15; }
    .badge-done { background: rgba(74,222,128,0.1); color: #4ade80; }

    .progress-wrap { margin-top: 8px; }
    .progress-label { display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 6px; color: var(--text-secondary); }
    .progress-bar-bg { height: 6px; background: #222; border-radius: 3px; overflow: hidden; }
    .progress-bar-fill { height: 100%; background: #fff; border-radius: 3px; transition: width 0.5s; }

    .card-actions { display: flex; gap: 10px; margin-top: auto; padding-top: 16px; }
    .btn-action {
        flex: 1; padding: 12px; border-radius: 10px; font-size: 13px; font-weight: 600;
        cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: all 0.2s;
    }
    .btn-fill { background: #fff; color: #000; border: none; }
    .btn-fill:hover { background: #e5e5e5; }
    .btn-link { background: transparent; border: 1px solid #333; color: #fff; }
    .btn-link:hover { border-color: #555; background: rgba(255,255,255,0.02); }

    /* Empty State */
    .empty-card {
        grid-column: 1 / -1;
        padding: 60px; text-align: center;
        background: rgba(255,255,255,0.01); border: 2px dashed #222;
        border-radius: 24px; color: var(--text-secondary);
    }
</style>
@endsection

@section('content')
<div class="hub-container">
    <div class="hub-header">
        <h1 class="hub-title">Manajemen Asesmen</h1>
        <p class="hub-subtitle">Pilih UMKM Anda dan mulai lakukan evaluasi kesehatan organisasi.</p>
    </div>

    <div class="status-grid" id="assessment-grid">
        @forelse($umkms as $umkm)
            <div class="status-card">
                <div class="card-top">
                    <div>
                        <div class="umkm-sector">{{ $umkm->sektor_usaha ?? 'Sektor Umum' }}</div>
                        <h3 class="umkm-name">{{ $umkm->nama_umkm }}</h3>
                    </div>
                    @if($umkm->assessment_status == 'selesai')
                        <span class="badge-pill badge-done">Selesai</span>
                    @elseif($umkm->assessment_status == 'selesai_karyawan')
                        <span class="badge-pill badge-done" style="background: #4ade80; color: #000;">Kuota Terpenuhi</span>
                    @elseif($umkm->assessment_status == 'proses_karyawan')
                        <span class="badge-pill badge-doing">Berlangsung ({{ $umkm->employee_answered }}/{{ $umkm->employee_target }})</span>
                    @elseif($umkm->assessment_status == 'menunggu_karyawan')
                        <span class="badge-pill badge-doing">Tunggu Karyawan</span>
                    @elseif($umkm->assessment_status == 'sedang_berlangsung')
                        <span class="badge-pill badge-doing">Proses Owner</span>
                    @else
                        <span class="badge-pill badge-yet">Belum Mulai</span>
                    @endif
                </div>

                <div class="progress-wrap">
                    @if($umkm->assessment_status == 'sedang_berlangsung' || $umkm->assessment_status == 'belum_mulai')
                        <div class="progress-label">
                            <span>Progres Owner</span>
                            <span>{{ $umkm->assessment_progress }}%</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: {{ $umkm->assessment_progress }}%"></div>
                        </div>
                    @else
                        @php 
                            $empPct = ($umkm->employee_target > 0) ? round(($umkm->employee_answered / $umkm->employee_target) * 100) : 0;
                        @endphp
                        <div class="progress-label">
                            <span>Progres Karyawan</span>
                            <span>{{ $empPct }}% ({{ $umkm->employee_answered }}/{{ $umkm->employee_target }})</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill" style="width: {{ $empPct }}%; background: #818cf8;"></div>
                        </div>
                    @endif
                </div>

                <div class="card-actions" style="display: flex; flex-direction: column; gap: 10px;">
                    <div style="display: flex; gap: 10px; width: 100%;">
                        @if($umkm->assessment_status == 'selesai' || $umkm->assessment_status == 'selesai_karyawan' || $umkm->assessment_status == 'proses_karyawan' || $umkm->assessment_status == 'menunggu_karyawan')
                            <a href="{{ route('monitoring') }}?umkm_id={{ $umkm->umkm_id }}" class="btn-action btn-fill" style="text-decoration: none; flex: 2;">
                                <i class="fa-solid fa-chart-line"></i> 
                                {{ $umkm->assessment_status == 'selesai' ? 'Lihat Hasil' : 'Monitoring' }}
                            </a>
                        @else
                            @if($umkm->assessment_status == 'belum_mulai' && !$umkm->active_assessment_id)
                                <button type="button" class="btn-action btn-fill" style="flex: 2;"
                                        onclick="openNewPeriodModal('{{ $umkm->umkm_id }}', '{{ $umkm->nama_umkm }}', true)">
                                    <i class="fa-solid fa-play"></i> Mulai Asesmen
                                </button>
                            @else
                                <a href="{{ route('assessment.fill', $umkm->umkm_id) }}" class="btn-action btn-fill" style="text-decoration: none; flex: 2;">
                                    <i class="fa-solid fa-pen-to-square"></i> 
                                    {{ $umkm->assessment_status == 'belum_mulai' ? 'Mulai Asesmen' : 'Lanjut Mengisi' }}
                                </a>
                            @endif
                        @endif

                        <button class="btn-action btn-link" onclick="manageLinks('{{ $umkm->umkm_id }}', '{{ $umkm->nama_umkm }}')" 
                                style="flex: 1; {{ $umkm->assessment_status == 'belum_mulai' ? 'opacity:0.5; cursor:not-allowed;' : '' }}"
                                {{ $umkm->assessment_status == 'belum_mulai' ? 'disabled' : '' }}>
                            <i class="fa-solid fa-link"></i>
                        </button>
                    </div>

                    <button type="button" class="btn-action btn-link" style="width: 100%; border-style: dashed; border-color: #333; color: #888;" 
                            onclick="openNewPeriodModal('{{ $umkm->umkm_id }}', '{{ $umkm->nama_umkm }}')">
                        <i class="fa-solid fa-plus-circle"></i> Buat Periode Baru
                    </button>
                </div>
            </div>
        @empty
            <div class="empty-card">
                <i class="fa-solid fa-store-slash" style="font-size: 40px; margin-bottom: 16px; opacity: 0.3;"></i>
                <h3 style="color: #fff; margin-bottom: 8px;">Belum Ada UMKM</h3>
                <p style="margin-bottom: 24px;">Daftarkan UMKM Anda terlebih dahulu untuk memulai asesmen.</p>
                <a href="{{ route('tambah-umkm') }}" class="btn-fill btn-action" style="max-width: 200px; margin: 0 auto; text-decoration: none;">Daftar Sekarang</a>
            </div>
        @endforelse
    </div>
</div>

{{-- Modal Periode Baru --}}
<div id="newPeriodModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.8); z-index:110; align-items:center; justify-content:center; backdrop-filter:blur(8px);">
    <div style="background:#151515; border:1px solid #333; border-radius:24px; padding:32px; width:90%; max-width:450px; position:relative;">
        <button onclick="closeNewPeriodModal()" style="position:absolute; top:20px; right:20px; background:none; border:none; color:#666; cursor:pointer; font-size:20px;"><i class="fa-solid fa-xmark"></i></button>
        
        <h3 id="newPeriodModalTitle" style="font-size:24px; font-weight:800; margin-bottom:8px; color:#fff;">Buat Periode Baru</h3>
        <p id="newPeriodUmkmName" style="color:var(--text-secondary); font-size:13px; margin-bottom:24px;">UMKM Name</p>
        
        <form id="newPeriodForm" action="" method="POST">
            @csrf
            <div style="margin-bottom:24px;">
                <label style="display:block; font-size:10px; font-weight:700; color:#666; text-transform:uppercase; margin-bottom:8px;">Target Jumlah Karyawan</label>
                <input type="number" name="jumlah_karyawan" required min="1" placeholder="Contoh: 10" 
                       style="width:100%; background:#000; border:1px solid #222; border-radius:10px; padding:12px; color:#fff; font-size:14px; outline:none; border-color:#444;">
                <p style="font-size:11px; color:#555; margin-top:8px;">Link kuesioner akan otomatis ditutup jika jumlah responden sudah mencapai angka ini.</p>
            </div>

            <button type="submit" class="btn-action btn-fill" style="width:100%; padding:14px; border-radius:12px; font-weight:700;">
                Mulai Asesmen
            </button>
        </form>
    </div>
</div>

{{-- Modal Link Karyawan --}}
<div id="linkModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.8); z-index:100; align-items:center; justify-content:center; backdrop-filter:blur(8px);">
    <div style="background:#151515; border:1px solid #333; border-radius:24px; padding:32px; width:90%; max-width:500px; position:relative;">
        <button onclick="closeModal()" style="position:absolute; top:20px; right:20px; background:none; border:none; color:#666; cursor:pointer; font-size:20px;"><i class="fa-solid fa-xmark"></i></button>
        
        <h3 id="modalUmkmName" style="font-size:24px; font-weight:800; margin-bottom:8px; color:#fff;">UMKM Name</h3>
        <p style="color:var(--text-secondary); font-size:13px; margin-bottom:24px;">Gunakan link di bawah ini untuk mengumpulkan data dari karyawan Anda.</p>
        
        <div style="margin-bottom:20px;">
            <label style="display:block; font-size:10px; font-weight:700; color:#666; text-transform:uppercase; margin-bottom:8px;">Link Asesmen Karyawan</label>
            <div style="display:flex; gap:10px;">
                <input type="text" id="employeeLink" readonly style="flex:1; background:#000; border:1px solid #222; border-radius:10px; padding:12px; color:#fff; font-size:13px; outline:none;">
                <button onclick="copyLink()" style="background:#fff; color:#000; border:none; border-radius:10px; padding:0 16px; font-weight:700; cursor:pointer;">Salin</button>
            </div>
        </div>

        <button onclick="regenerateLink()" style="width:100%; padding:14px; background:transparent; border:1px solid #222; border-radius:12px; color:#666; font-size:12px; font-weight:600; cursor:pointer;">
            <i class="fa-solid fa-arrows-rotate"></i> Perbarui Link (Reset)
        </button>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let activeUmkmId = null;
    let syncRouteBase = "{{ route('assessment.new', ':id') }}";

    function openNewPeriodModal(id, name, isFirst = false) {
        activeUmkmId = id;
        document.getElementById('newPeriodUmkmName').innerText = name;
        document.getElementById('newPeriodModalTitle').innerText = isFirst ? 'Mulai Asesmen Pertama' : 'Buat Periode Baru';
        document.getElementById('newPeriodForm').action = syncRouteBase.replace(':id', id);
        document.getElementById('newPeriodModal').style.display = 'flex';
    }

    function closeNewPeriodModal() {
        document.getElementById('newPeriodModal').style.display = 'none';
    }

    function manageLinks(id, name) {
        activeUmkmId = id;
        document.getElementById('modalUmkmName').innerText = name;
        
        // Fetch or Generate link via API
        fetch('{{ route('assessment.generate') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ umkm_id: id, jumlah_karyawan: 50 }) // Default 50 for now
        })
        .then(res => res.json())
        .then(data => {
            if (data.employee_link) {
                document.getElementById('employeeLink').value = data.employee_link;
                document.getElementById('linkModal').style.display = 'flex';
            }
        });
    }

    function closeModal() {
        document.getElementById('linkModal').style.display = 'none';
    }

    function copyLink() {
        const input = document.getElementById('employeeLink');
        input.select();
        navigator.clipboard.writeText(input.value);
        alert('Link berhasil disalin!');
    }

    function regenerateLink() {
        if(confirm('Apakah Anda yakin ingin memperbarui link? Link lama tidak akan bisa diakses lagi.')) {
            manageLinks(activeUmkmId, document.getElementById('modalUmkmName').innerText);
        }
    }

    function startAssessmentPoller() {
        setInterval(() => {
            fetch('{{ route('api.realtime.assessment') }}')
                .then(res => res.json())
                .then(data => {
                    let gridHtml = '';
                    if (data.umkms && data.umkms.length > 0) {
                        data.umkms.forEach(umkm => {
                            let badgeHtml = '';
                            if (umkm.assessment_status === 'selesai') {
                                badgeHtml = '<span class="badge-pill badge-done">Selesai</span>';
                            } else if (umkm.assessment_status === 'selesai_karyawan') {
                                badgeHtml = '<span class="badge-pill badge-done" style="background: #4ade80; color: #000;">Kuota Terpenuhi</span>';
                            } else if (umkm.assessment_status === 'proses_karyawan') {
                                badgeHtml = `<span class="badge-pill badge-doing">Berlangsung (${umkm.employee_answered}/${umkm.employee_target})</span>`;
                            } else if (umkm.assessment_status === 'menunggu_karyawan') {
                                badgeHtml = '<span class="badge-pill badge-doing">Tunggu Karyawan</span>';
                            } else if (umkm.assessment_status === 'sedang_berlangsung') {
                                badgeHtml = '<span class="badge-pill badge-doing">Proses Owner</span>';
                            } else {
                                badgeHtml = '<span class="badge-pill badge-yet">Belum Mulai</span>';
                            }

                            let progressHtml = '';
                            if (umkm.assessment_status === 'sedang_berlangsung' || umkm.assessment_status === 'belum_mulai') {
                                progressHtml = `
                                    <div class="progress-label">
                                        <span>Progres Owner</span>
                                        <span>${umkm.assessment_progress}%</span>
                                    </div>
                                    <div class="progress-bar-bg">
                                        <div class="progress-bar-fill" style="width: ${umkm.assessment_progress}%"></div>
                                    </div>`;
                            } else {
                                const empPct = umkm.employee_target > 0 ? Math.round((umkm.employee_answered / umkm.employee_target) * 100) : 0;
                                progressHtml = `
                                    <div class="progress-label">
                                        <span>Progres Karyawan</span>
                                        <span>${empPct}% (${umkm.employee_answered}/${umkm.employee_target})</span>
                                    </div>
                                    <div class="progress-bar-bg">
                                        <div class="progress-bar-fill" style="width: ${empPct}%; background: #818cf8;"></div>
                                    </div>`;
                            }

                            let actionButtonsHtml = '';
                            if (umkm.assessment_status === 'selesai' || umkm.assessment_status === 'selesai_karyawan' || umkm.assessment_status === 'proses_karyawan' || umkm.assessment_status === 'menunggu_karyawan') {
                                const btnText = umkm.assessment_status === 'selesai' ? 'Lihat Hasil' : 'Monitoring';
                                actionButtonsHtml = `
                                    <a href="{{ route('monitoring') }}?umkm_id=${umkm.umkm_id}" class="btn-action btn-fill" style="text-decoration: none; flex: 2;">
                                        <i class="fa-solid fa-chart-line"></i> 
                                        ${btnText}
                                    </a>`;
                            } else {
                                if (umkm.assessment_status === 'belum_mulai' && !umkm.active_assessment_id) {
                                    actionButtonsHtml = `
                                        <button type="button" class="btn-action btn-fill" style="flex: 2;"
                                                onclick="openNewPeriodModal('${umkm.umkm_id}', '${umkm.nama_umkm.replace(/'/g, "\\'")}', true)">
                                            <i class="fa-solid fa-play"></i> Mulai Asesmen
                                        </button>`;
                                } else {
                                    const fillUrl = "{{ route('assessment.fill', ':id') }}".replace(':id', umkm.umkm_id);
                                    const btnText = umkm.assessment_status === 'belum_mulai' ? 'Mulai Asesmen' : 'Lanjut Mengisi';
                                    actionButtonsHtml = `
                                        <a href="${fillUrl}" class="btn-action btn-fill" style="text-decoration: none; flex: 2;">
                                            <i class="fa-solid fa-pen-to-square"></i> 
                                            ${btnText}
                                        </a>`;
                                }
                            }

                            const linkBtnDisabledAttr = umkm.assessment_status === 'belum_mulai' ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : '';

                            gridHtml += `
                                <div class="status-card">
                                    <div class="card-top">
                                        <div>
                                            <div class="umkm-sector">${umkm.sektor_usaha || 'Sektor Umum'}</div>
                                            <h3 class="umkm-name">${umkm.nama_umkm}</h3>
                                        </div>
                                        ${badgeHtml}
                                    </div>

                                    <div class="progress-wrap">
                                        ${progressHtml}
                                    </div>

                                    <div class="card-actions" style="display: flex; flex-direction: column; gap: 10px;">
                                        <div style="display: flex; gap: 10px; width: 100%;">
                                            ${actionButtonsHtml}
                                            <button class="btn-action btn-link" onclick="manageLinks('${umkm.umkm_id}', '${umkm.nama_umkm.replace(/'/g, "\\'")}')" 
                                                    style="flex: 1;"
                                                    ${linkBtnDisabledAttr}>
                                                <i class="fa-solid fa-link"></i>
                                            </button>
                                        </div>

                                        <button type="button" class="btn-action btn-link" style="width: 100%; border-style: dashed; border-color: #333; color: #888;" 
                                                onclick="openNewPeriodModal('${umkm.umkm_id}', '${umkm.nama_umkm.replace(/'/g, "\\'")}')">
                                            <i class="fa-solid fa-plus-circle"></i> Buat Periode Baru
                                        </button>
                                    </div>
                                </div>`;
                        });
                    } else {
                        gridHtml = `
                            <div class="empty-card">
                                <i class="fa-solid fa-store-slash" style="font-size: 40px; margin-bottom: 16px; opacity: 0.3;"></i>
                                <h3 style="color: #fff; margin-bottom: 8px;">Belum Ada UMKM</h3>
                                <p style="margin-bottom: 24px;">Daftarkan UMKM Anda terlebih dahulu untuk memulai asesmen.</p>
                                <a href="{{ route('tambah-umkm') }}" class="btn-fill btn-action" style="max-width: 200px; margin: 0 auto; text-decoration: none;">Daftar Sekarang</a>
                            </div>`;
                    }
                    document.getElementById('assessment-grid').innerHTML = gridHtml;
                })
                .catch(err => console.error('Poller error:', err));
        }, 10000);
    }

    document.addEventListener('DOMContentLoaded', startAssessmentPoller);
</script>
@endsection
