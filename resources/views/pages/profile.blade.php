@extends('layouts.app')

@section('title', 'Profil Akun')

@section('content')
<style>
    .profile-header {
        display: flex; align-items: center; gap: 32px;
        background: var(--card-color); border: 1px solid var(--border-color);
        padding: 40px; border-radius: 20px; margin-bottom: 32px;
        position: relative; overflow: hidden;
    }
    .profile-header::after {
        content: ''; position: absolute; top: -50px; right: -50px;
        width: 150px; height: 150px; background: rgba(255,255,255,0.03);
        border-radius: 50%;
    }
    .profile-avatar {
        width: 100px; height: 100px; border-radius: 50%;
        background: linear-gradient(135deg, #444, #222);
        display: flex; align-items: center; justify-content: center;
        font-size: 40px; font-weight: 700; color: #fff;
        border: 4px solid #2a2a2a; box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        position: relative; cursor: pointer; overflow: hidden;
    }
    .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .profile-avatar:hover .avatar-overlay { opacity: 1; }
    .avatar-overlay {
        position: absolute; inset: 0; background: rgba(0,0,0,0.5);
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; color: #fff; opacity: 0; transition: opacity 0.2s;
    }
    .profile-info h1 { font-size: 24px; font-weight: 700; margin-bottom: 8px; }
    .profile-info p { color: var(--text-secondary); font-size: 14px; margin-bottom: 16px; }
    
    .profile-meta { display: flex; gap: 24px; }
    .meta-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #aaa; }
    .meta-item i { color: #555; }

    .section-title { font-size: 18px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; }
    .section-title i { color: #555; }

    .umkm-card {
        background: var(--card-color); border: 1px solid var(--border-color);
        border-radius: 16px; padding: 0; overflow: hidden;
    }
    .umkm-table { width: 100%; border-collapse: collapse; }
    .umkm-table th { 
        text-align: left; padding: 16px 24px; font-size: 12px; 
        font-weight: 600; color: #555; text-transform: uppercase;
        border-bottom: 1px solid var(--border-color);
        background: rgba(255,255,255,0.02);
    }
    .umkm-table td { padding: 16px 24px; font-size: 13px; border-bottom: 1px solid rgba(255,255,255,0.03); }
    .umkm-table tr:last-child td { border-bottom: none; }
    
    .status-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;
    }
    .status-done { background: rgba(74, 222, 128, 0.1); color: #4ade80; }
    .status-pending { background: rgba(255, 255, 255, 0.05); color: #888; }

    .action-bar { display: flex; justify-content: flex-end; margin-top: 24px; }
    .btn-password {
        display: inline-flex; align-items: center; gap: 8px;
        background: transparent; color: #fff; border: 1px solid var(--border-color);
        padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 500;
        cursor: pointer; transition: all 0.2s; text-decoration: none;
    }
    .btn-password:hover { border-color: #555; background: #1a1a1a; }

    /* Action Buttons */
    .btn-delete {
        background: transparent; color: #666; border: none;
        padding: 8px; border-radius: 6px; cursor: pointer;
        transition: all 0.2s;
    }
    .btn-delete:hover { color: #f87171; background: rgba(248, 113, 113, 0.1); }

    /* Sort Styling */
    .sort-wrapper {
        display: flex; align-items: center; gap: 12px;
    }
    .sort-select {
        background: var(--card-color); border: 1px solid var(--border-color);
        color: #fff; font-size: 13px; padding: 8px 16px; border-radius: 8px;
        cursor: pointer; outline: none; transition: border-color 0.2s;
    }
    .sort-select:hover { border-color: #555; }
    
    .profile-actions { margin-left: auto; text-align: right; display: flex; flex-direction: column; gap: 12px; align-items: flex-end; }
    .umkm-header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }

    @media (max-width: 768px) {
        .profile-header { flex-direction: column; text-align: center; gap: 20px; padding: 24px; }
        .profile-actions { margin-left: 0; align-items: center; width: 100%; }
        .btn-password { width: 100%; justify-content: center; }
        .umkm-header-row { flex-direction: column; align-items: flex-start; gap: 16px; }
        .sort-wrapper { width: 100%; }
        .sort-select { flex: 1; }
    }
</style>

<div class="profile-header">
    <div class="profile-avatar" onclick="document.getElementById('photoInput').click()">
        @if($owner->photo)
            <img src="{{ asset('storage/' . $owner->photo) }}" alt="{{ $owner->name }}">
        @else
            {{ strtoupper(substr($owner->name, 0, 1)) }}
        @endif
        <div class="avatar-overlay">
            <i class="fa-solid fa-camera"></i>
        </div>
    </div>
    
    <form id="photoForm" action="{{ route('profile.photo.post') }}" method="POST" enctype="multipart/form-data" style="display: none;">
        @csrf
        <input type="file" name="photo" id="photoInput" accept="image/*" onchange="document.getElementById('photoForm').submit()">
    </form>

    <div class="profile-info">
        <h1>{{ $owner->name }}</h1>
        <p>{{ $owner->email }}</p>
        <div class="profile-meta">
            <div class="meta-item">
                <i class="fa-solid fa-venus-mars"></i>
                {{ ucfirst($owner->gender) }}
            </div>
            <div class="meta-item">
                <i class="fa-solid fa-calendar-days"></i>
                Member sejak {{ $owner->created_at->format('M Y') }}
            </div>
        </div>
    </div>
    <div class="profile-actions">
        <a href="{{ route('profile.name') }}" class="btn-password" style="background: rgba(255,255,255,0.05);">
            <i class="fa-solid fa-user-pen"></i> Ubah Nama
        </a>
        <a href="{{ route('profile.password') }}" class="btn-password">
            <i class="fa-solid fa-key"></i> Ubah Password
        </a>
    </div>
</div>

<div class="umkm-header-row">
    <div class="section-title" style="margin-bottom: 0;">
        <i class="fa-solid fa-building-user"></i> Daftar UMKM Dikelola
    </div>
    <div class="sort-wrapper">
        <span style="font-size: 12px; color: #555; font-weight: 600; text-transform: uppercase;">Sortir:</span>
        <select class="sort-select" id="umkmSort">
            <option value="latest">Update Terbaru</option>
            <option value="name_asc">Nama (A-Z)</option>
            <option value="name_desc">Nama (Z-A)</option>
            <option value="employees_desc">Karyawan Terbanyak</option>
            <option value="employees_asc">Karyawan Tersedikit</option>
            <option value="sector">Sektor Usaha</option>
            <option value="status">Status Assessment</option>
        </select>
    </div>
</div>

<div class="umkm-card">
    <div class="table-responsive">
        <table class="umkm-table">
            <thead>
                <tr>
                    <th>Nama UMKM</th>
                    <th>Sektor Usaha</th>
                    <th>Karyawan</th>
                    <th>Status Assessment</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="umkmTableBody">
                @forelse($umkms as $umkm)
                @php
                    $latestAssessment = $umkm->assessments->first();
                    $employeeCount = $latestAssessment ? $latestAssessment->jumlah_karyawan : 0;
                    $hasAssessment = $umkm->assessments_count > 0;
                @endphp
                <tr data-name="{{ strtolower($umkm->nama_umkm) }}" 
                    data-sector="{{ strtolower($umkm->sektor_usaha ?? '') }}" 
                    data-employees="{{ $employeeCount }}"
                    data-status="{{ $hasAssessment ? 1 : 0 }}"
                    data-updated="{{ $umkm->updated_at->timestamp }}">
                    <td style="font-weight: 600;">{{ $umkm->nama_umkm }}</td>
                    <td style="color: #aaa;">{{ $umkm->sektor_usaha ?? '-' }}</td>
                    <td>
                        @if($umkm->assessments->isNotEmpty())
                            {{ $umkm->assessments->first()->jumlah_karyawan }} Orang
                        @else
                            <span style="color: #555;">N/A</span>
                        @endif
                    </td>
                    <td>
                        @if($umkm->assessments_count > 0)
                            <span class="status-badge status-done">
                                <i class="fa-solid fa-circle-check"></i> Sudah pernah membuat asessment
                            </span>
                        @else
                            <span class="status-badge status-pending">
                                <i class="fa-solid fa-circle-minus"></i> Belum pernah membuat asessment
                            </span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <form action="{{ route('umkm.destroy', $umkm->umkm_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus UMKM ini? Seluruh data assessment terkait akan ikut terhapus permanen.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete" title="Hapus UMKM">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #555;">
                        Belum ada data UMKM yang terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(session('success'))
<div style="position: fixed; bottom: 24px; right: 24px; background: #4ade80; color: #000; padding: 12px 24px; border-radius: 8px; font-size: 13px; font-weight: 600; box-shadow: 0 10px 30px rgba(0,0,0,0.3); animation: slideUp 0.3s ease;">
    {{ session('success') }}
</div>
<script>
    setTimeout(() => {
        document.querySelector('[style*="animation: slideUp"]').style.display = 'none';
    }, 3000);
</script>
@endif

@section('scripts')
<script>
    document.getElementById('umkmSort').addEventListener('change', function() {
        const criteria = this.value;
        const tbody = document.getElementById('umkmTableBody');
        const rows = Array.from(tbody.querySelectorAll('tr[data-updated]'));

        rows.sort((a, b) => {
            let valA, valB;

            switch(criteria) {
                case 'latest':
                    valA = parseInt(b.dataset.updated);
                    valB = parseInt(a.dataset.updated);
                    return valA - valB;
                case 'name_asc':
                    return a.dataset.name.localeCompare(b.dataset.name);
                case 'name_desc':
                    return b.dataset.name.localeCompare(a.dataset.name);
                case 'employees_desc':
                    return parseInt(b.dataset.employees) - parseInt(a.dataset.employees);
                case 'employees_asc':
                    return parseInt(a.dataset.employees) - parseInt(b.dataset.employees);
                case 'sector':
                    return a.dataset.sector.localeCompare(b.dataset.sector);
                case 'status':
                    return parseInt(b.dataset.status) - parseInt(a.dataset.status);
                default:
                    return 0;
            }
        });

        // Re-append rows in new order
        rows.forEach(row => tbody.appendChild(row));
    });
</script>
@endsection
@endsection
