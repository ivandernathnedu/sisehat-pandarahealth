<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UmkmController extends Controller
{
    /**
     * Tampilkan daftar UMKM milik Owner tertentu
     */
    public function index(Request $request)
    {
        // Misalkan owner_id didapat dari session/token, untuk API ini kita terima dari query
        $ownerId = $request->query('owner_id'); 

        if (!$ownerId) {
            return response()->json(['error' => 'owner_id diperlukan'], 400);
        }

        $umkms = Umkm::where('owner_id', $ownerId)->get();

        return response()->json([
            'message' => 'Data UMKM berhasil diambil',
            'data' => $umkms
        ]);
    }

    /**
     * Tambahkan UMKM baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'owner_id' => 'required|exists:owners,owner_id',
            'nama_umkm' => 'required|string|max:150',
            'sektor_usaha' => 'nullable|string|max:100',
            'umur_usaha' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $umkm = Umkm::create($request->all());

        return response()->json([
            'message' => 'UMKM berhasil ditambahkan',
            'data' => $umkm
        ], 201);
    }

    public function webStore(Request $request)
    {
        $owner = Auth::guard('owner')->user();
        if (!$owner) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $request->validate([
            'nama_umkm' => 'required|string|max:150',
            'sektor_usaha' => 'nullable|string|max:100',
            'umur_usaha' => 'nullable|string|max:50',
        ]);

        Umkm::create([
            'owner_id' => $owner->owner_id,
            'nama_umkm' => $request->nama_umkm,
            'sektor_usaha' => $request->sektor_usaha,
            'umur_usaha' => $request->umur_usaha,
        ]);

        return redirect()->route('profile')->with('success', 'UMKM berhasil ditambahkan');
    }

    /**
     * Menghapus UMKM via Web
     */
    public function webDestroy($id)
    {
        $owner = Auth::guard('owner')->user();
        $umkm = Umkm::where('umkm_id', $id)
                    ->where('owner_id', $owner->owner_id)
                    ->firstOrFail();

        $umkm->delete();

        return redirect()->route('profile')->with('success', 'UMKM berhasil dihapus.');
    }

    /**
     * Lihat detail UMKM beserta histori assessment-nya
     */
    public function show($id)
    {
        $umkm = Umkm::with(['assessments' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->find($id);

        if (!$umkm) {
            return response()->json(['error' => 'UMKM tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Detail UMKM berhasil diambil',
            'data' => $umkm
        ]);
    }

    /**
     * Update data UMKM
     */
    public function update(Request $request, $id)
    {
        $umkm = Umkm::find($id);

        if (!$umkm) {
            return response()->json(['error' => 'UMKM tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'umkm_name' => 'sometimes|required|string|max:150',
            'industry' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $umkm->update($request->all());

        return response()->json([
            'message' => 'UMKM berhasil diupdate',
            'data' => $umkm
        ]);
    }
}
