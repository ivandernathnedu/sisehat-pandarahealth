@extends('layouts.app')

@section('title', 'Full Rank UMKM')

@section('styles')
<style>
    .umkm-header { margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-end; }
    .umkm-title { font-size: 28px; font-weight: 800; letter-spacing: -0.5px; }
    .umkm-desc { font-size: 13px; color: var(--text-secondary); margin-top: 4px; }
    
    .filter-container { display: flex; gap: 16px; align-items: center; margin-bottom: 24px; flex-wrap: wrap; }
    .search-box { position: relative; flex: 1; min-width: 250px; max-width: 400px; }
    .search-box i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); }
    .search-box input {
        width: 100%; background: var(--card-color); border: 1px solid var(--border-color);
        padding: 12px 16px 12px 40px; border-radius: 12px; color: #fff; font-size: 13px;
        outline: none; transition: border-color 0.2s;
    }
    .search-box input:focus { border-color: #555; }
    
    .filter-box { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-secondary); }
    .filter-select {
        background: var(--card-color); border: 1px solid var(--border-color);
        color: #fff; padding: 10px 14px; border-radius: 10px; outline: none; cursor: pointer;
    }

    .table-card { background: var(--card-color); border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden; }
    .umkm-table { width: 100%; border-collapse: collapse; }
    .umkm-table th {
        background: rgba(255,255,255,0.03); color: var(--text-secondary);
        font-size: 12px; font-weight: 600; text-transform: uppercase;
        padding: 16px 24px; text-align: left; border-bottom: 1px solid var(--border-color);
    }
    .umkm-table td { padding: 16px 24px; border-bottom: 1px solid var(--border-color); font-size: 14px; vertical-align: middle; }
    .umkm-table tbody tr { transition: background 0.2s; }
    .umkm-table tbody tr:hover { background: rgba(255,255,255,0.02); }
    
    .badge { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; }
    .badge.sangat_sehat { background: rgba(74,222,128,0.1); color: #4ade80; }
    .badge.sehat { background: rgba(129,140,248,0.1); color: #818cf8; }
    .badge.cukup_sehat { background: rgba(250,204,21,0.1); color: #facc15; }
    .badge.kurang_sehat { background: rgba(248,113,113,0.1); color: #f87171; }
    .badge.unknown { background: rgba(255,255,255,0.1); color: #a3a3a3; }
    
    .rank-number {
        width: 32px; height: 32px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 13px; color: var(--text-secondary);
        background: rgba(255,255,255,0.05);
    }
    .rank-1 { background: linear-gradient(135deg, #FFD700, #FDB931); color: #000; box-shadow: 0 0 10px rgba(255,215,0,0.3); }
    .rank-2 { background: linear-gradient(135deg, #E0E0E0, #BDBDBD); color: #000; }
    .rank-3 { background: linear-gradient(135deg, #CD7F32, #A0522D); color: #fff; }

    .empty-state { text-align: center; padding: 60px 20px; color: var(--text-secondary); }
    .empty-state i { font-size: 48px; margin-bottom: 16px; opacity: 0.5; }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .5; }
    }
</style>
@endsection

@section('content')
<div class="umkm-header">
    <div>
        <h1 class="umkm-title">Full Rank UMKM</h1>
        <p class="umkm-desc">Papan peringkat dan data lengkap seluruh UMKM terdaftar di ekosistem Anda.</p>
    </div>
</div>

<div class="filter-container">
    <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput" placeholder="Cari berdasarkan nama atau sektor...">
    </div>
    
    <div class="filter-box">
        <span>Urutkan:</span>
        <select class="filter-select" id="sortSelect">
            <option value="score_desc" selected>Skor Tertinggi</option>
            <option value="score_asc">Skor Terendah</option>
            <option value="az">Abjad A - Z</option>
            <option value="za">Abjad Z - A</option>
            <option value="kategori">Kategori</option>
            <option value="terbaru">Terbaru Terdaftar</option>
            <option value="terlama">Terlama Terdaftar</option>
        </select>
    </div>

    <div class="filter-box">
        <span>Tampilkan:</span>
        <select class="filter-select" id="limitSelect">
            <option value="25" selected>25 baris</option>
            <option value="50">50 baris</option>
            <option value="100">100 baris</option>
            <option value="250">250 baris</option>
            <option value="500">500 baris</option>
            <option value="all">Semua Data</option>
        </select>
    </div>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="umkm-table" id="umkmTable">
            <thead>
                <tr>
                    <th style="width: 80px; text-align: center;">Rank</th>
                    <th>Nama UMKM</th>
                    <th>Sektor Usaha</th>
                    <th>Skor Terakhir</th>
                    <th>Status</th>
                    <th>Tgl Terdaftar</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be populated here via AJAX -->
            </tbody>
        </table>
    </div>

    <div id="noResults" class="empty-state" style="display: none;">
        <i class="fa-solid fa-store-slash"></i>
        <h3>Tidak ada hasil</h3>
        <p>Data UMKM tidak ditemukan dengan kriteria tersebut.</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');
    const limitSelect = document.getElementById('limitSelect');
    const tbody = document.querySelector('#umkmTable tbody');
    const noResults = document.getElementById('noResults');
    const table = document.getElementById('umkmTable');

    let searchTimeout;

    function renderSkeleton() {
        table.style.display = 'table';
        noResults.style.display = 'none';
        let skeletonHTML = '';
        for(let i=0; i<5; i++) {
            skeletonHTML += `
                <tr>
                    <td colspan="6" style="padding: 16px;">
                        <div style="height: 30px; background: rgba(255,255,255,0.05); border-radius: 6px; animation: pulse 1.5s infinite; animation-delay: ${i*0.1}s;"></div>
                    </td>
                </tr>
            `;
        }
        tbody.innerHTML = skeletonHTML;
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];
        return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
    }

    function fetchUmkmData() {
        clearTimeout(searchTimeout);
        renderSkeleton();

        searchTimeout = setTimeout(() => {
            const query = searchInput.value.trim();
            const sort = sortSelect.value;
            const limit = limitSelect.value;
            
            const params = new URLSearchParams({
                search: query,
                sort: sort,
                limit: limit
            });

            fetch(`/api/umkm/rank?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    tbody.innerHTML = '';
                    
                    if (data.length === 0) {
                        table.style.display = 'none';
                        noResults.style.display = 'block';
                        return;
                    }

                    table.style.display = 'table';
                    noResults.style.display = 'none';

                    data.forEach((item, index) => {
                        let badgeClass = 'unknown';
                        let category = item.health_category || 'Belum Dinilai';
                        
                        if(item.overall_score !== null) {
                            const sc = item.overall_score;
                            if(sc > 75) { badgeClass = 'sangat_sehat'; scoreColor = '#4ade80'; }
                            else if(sc > 50) { badgeClass = 'sehat'; scoreColor = '#818cf8'; }
                            else if(sc > 25) { badgeClass = 'cukup_sehat'; scoreColor = '#facc15'; }
                            else { badgeClass = 'kurang_sehat'; scoreColor = '#f87171'; }
                        }

                        let rankClass = '';
                        if (item.global_rank == 1) rankClass = 'rank-1';
                        else if (item.global_rank == 2) rankClass = 'rank-2';
                        else if (item.global_rank == 3) rankClass = 'rank-3';

                        let tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td style="text-align: center;">
                                <div class="rank-number ${rankClass}">${item.global_rank}</div>
                            </td>
                            <td style="font-weight: 600;">${item.nama_umkm}</td>
                            <td style="color: var(--text-secondary);">${item.sektor_usaha || '-'}</td>
                            <td>
                                <span style="font-weight: 700; color: ${scoreColor};">
                                    ${item.overall_score !== null ? parseFloat(item.overall_score).toFixed(1) : '-'}
                                </span>
                            </td>
                            <td><span class="badge ${badgeClass}">${category.replace(/_/g, ' ').toUpperCase()}</span></td>
                            <td style="color: var(--text-secondary); font-size: 12px;">
                                ${formatDate(item.created_at)}
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(err => {
                    console.error('Error fetching data:', err);
                    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:#f87171; padding:30px;">Gagal memuat data. Silakan coba lagi.</td></tr>`;
                });
        }, 400); // 400ms debounce
    }

    // Attach Event Listeners
    searchInput.addEventListener('keyup', fetchUmkmData);
    sortSelect.addEventListener('change', fetchUmkmData);
    limitSelect.addEventListener('change', fetchUmkmData);

    // Initial Load
    fetchUmkmData();
</script>
@endsection
