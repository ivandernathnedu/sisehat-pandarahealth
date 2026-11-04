<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Umkm;
use App\Models\Owner;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil
     */
    public function index()
    {
        $owner = Auth::guard('owner')->user();
        
        // Ambil UMKM milik owner beserta assessment terbaru untuk data karyawan
        $umkms = Umkm::where('owner_id', $owner->owner_id)
            ->withCount('assessments')
            ->with(['assessments' => function($q) {
                $q->latest();
            }])
            ->get();

        return view('pages.profile', compact('owner', 'umkms'));
    }

    /**
     * Tampilkan form ubah password
     */
    public function showChangePassword()
    {
        return view('pages.change-password');
    }

    /**
     * Proses ubah password
     */
    public function updatePassword(Request $request)
    {
        $isGuest = !Auth::guard('owner')->check();

        $rules = [
            'password' => 'required|string|min:6|confirmed',
        ];
        
        $messages = [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'email.required' => 'Email wajib diisi.',
            'email.exists' => 'Email tidak terdaftar di sistem kami.'
        ];

        if ($isGuest) {
            $rules['email'] = 'required|email|exists:owners,email';
        }

        $request->validate($rules, $messages);

        if ($isGuest) {
            // Lupa password flow
            Owner::where('email', $request->email)->update([
                'password' => Hash::make($request->password)
            ]);
            return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login.');
        } else {
            // Ganti password profil
            $owner = Auth::guard('owner')->user();
            Owner::where('owner_id', $owner->owner_id)->update([
                'password' => Hash::make($request->password)
            ]);
            return redirect()->route('profile')->with('success', 'Password berhasil diperbarui.');
        }
    }

    /**
     * Tampilkan form ubah nama profil (username)
     */
    public function showChangeName()
    {
        $owner = Auth::guard('owner')->user();
        return view('pages.change-name', compact('owner'));
    }

    /**
     * Proses ubah nama profil (username)
     */
    public function updateName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.'
        ]);

        $owner = Auth::guard('owner')->user();
        $ownerModel = Owner::find($owner->owner_id);
        
        $ownerModel->update([
            'name' => $request->name
        ]);

        return redirect()->route('profile')->with('success', 'Nama profil berhasil diperbarui.');
    }

    /**
     * Proses unggah foto profil
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'photo.required' => 'Silakan pilih file gambar.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
            'photo.max' => 'Ukuran gambar maksimal 2MB.'
        ]);

        $owner = Auth::guard('owner')->user();
        $ownerModel = Owner::find($owner->owner_id);

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($ownerModel->photo) {
                Storage::disk('public')->delete($ownerModel->photo);
            }

            // Simpan foto baru
            $path = $request->file('photo')->store('profile_photos', 'public');
            
            // Update database
            $ownerModel->update(['photo' => $path]);

            return redirect()->route('profile')->with('success', 'Foto profil berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah foto.');
    }
}
