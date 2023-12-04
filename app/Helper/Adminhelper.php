<?php

namespace App\Helper;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Adminhelper
{

    //-------------------- Aktivasi Siswa --------------------//

    public static function allsiswa()
    {
        // Mengambil informasi user yang sedang terautentikasi (login)
        $user = Auth::user();

        // Memeriksa apakah pengguna terautentikasi
        if (!$user) {
            // Jika tidak terautentikasi, kembalikan respon JSON dengan pesan 'tidak ada siswa' dan status 401 (Unauthorized)
            return response()->json(['message' => 'tidak ada siswa'], 401);
        }

        // Mengambil semua data pengguna yang memiliki peran (role) 'siswa'
        $siswa = User::where('role', 'siswa')->get();

        // Mengembalikan respon JSON yang berisi data siswa
        return response()->json($siswa);
    }

    public static function activation($siswa)
    {
        // Memperbarui status akun siswa menjadi 'aktif'
        $siswa->update([
            'status' => 'aktif'
        ]);

        // Mengembalikan respon JSON dengan pesan 'akun sudah aktif' dan data siswa yang telah diaktifkan, serta status 200 (OK)
        return response()->json(['message' => 'akun sudah aktif', 'data' => $siswa], 200);
    }

    public static function nonactivation($siswa)
    {
        // Memperbarui status akun siswa menjadi 'non'
        $siswa->update([
            'status' => 'non'
        ]);

        // Mengembalikan respon JSON dengan pesan 'akun sudah dinonaktifkan' dan data siswa yang telah dinonaktifkan, serta status 200 (OK)
        return response()->json(['message' => 'akun sudah dinonaktifkan', 'data' => $siswa], 200);
    }

    //-------------------- Aktivasi Siswa --------------------//



    //-------------------- Aktivasi Admin --------------------//

    public static function makeadmin($validatordata)
    {
        // Membuat sebuah admin baru dengan data yang divalidasi
        $user = User::create([
            'name'     => $validatordata['name'],
            'email'    => $validatordata['email'],
            'password' => bcrypt($validatordata['password']),
            'photo'    => $validatordata['photo'],
            'role'     => 'admin',
            'status'   => 'aktif'
        ]);

        // Memeriksa apakah proses pembuatan admin berhasil
        if ($user) {
            // Jika berhasil, kembalikan respons JSON sukses dengan data admin yang baru dibuat dan status 201 (Created)
            return response()->json([
                'success' => true,
                'user'   => $user,
            ], 201);
        }

        // Jika gagal membuat admin, kembalikan respons JSON gagal
        return response()->json([
            'success' => false,
        ]);
    }

    public static function editadmin($admin, $validatordata)
    {
        // Memeriksa jika data validator ada (data validasi untuk update)
        if ($validatordata) {
            // Jika ada, lakukan pembaruan data admin
            $admin->update([
                'name' => $validatordata['name'],
                'email' => $validatordata['email'],
            ]);
        }

        // Memeriksa jika admin tidak ditemukan
        if (!$admin) {
            // Jika tidak ditemukan, kembalikan respons JSON dengan pesan 'User not found or not an admin' dan status 404 (Not Found)
            return response()->json(['message' => 'User not found or not an admin'], 404);
        }

        // Jika berhasil mengedit admin, kembalikan respons JSON sukses dengan pesan 'User updated successfully' dan data admin yang diperbarui
        return response()->json(['message' => 'User updated successfully', 'user' => $admin]);
    }

    public static function deleteadmin($admin)
    {
        // Menghapus foto dari storage yang terkait dengan admin
        Storage::delete('public/profile/' . $admin->photo);

        // Menghapus admin dari database
        if ($admin->delete()) {
            // Jika berhasil menghapus, kembalikan respons dengan pesan sukses
            return response([
                'Berhasil Menghapus Data'
            ]);
        } else {
            // Jika gagal menghapus, kembalikan respons dengan pesan gagal
            return response([
                'Tidak Berhasil Menghapus Data'
            ]);
        }

        // Kembalikan respons JSON dengan pesan 'data berhasil dihapus' dan status 200 (OK)
        return response()->json(['message' => 'data berhasil dihapus'], 200);
    }

    //-------------------- Aktivasi Admin --------------------//



    //-------------------- Aktivasi Mentor --------------------//

    public static function makementor($validatordata)
    {
        // Membuat mentor baru dengan menggunakan data yang telah divalidasi
        $user = User::create([
            'name'     => $validatordata['name'],
            'email'    => $validatordata['email'],
            'password' => bcrypt($validatordata['password']),
            'photo'    => $validatordata['photo'],
            'role'     => 'mentor',
            'status'   => 'aktif'
        ]);

        // Memeriksa apakah proses pembuatan mentor berhasil
        if ($user) {
            // Jika berhasil, kembalikan respons JSON sukses dengan data mentor yang baru dibuat dan status 201 (Created)
            return response()->json([
                'success' => true,
                'user'   => $user,
            ], 201);
        }

        // Jika gagal membuat mentor, kembalikan respons JSON gagal
        return response()->json([
            'success' => false,
        ]);
    }

    public static function editmentor($mentor, $validatordata)
    {
        // Memeriksa jika data validator ada (data validasi untuk update)
        if ($validatordata) {
            // Jika ada, lakukan pembaruan data mentor
            $mentor->update([
                'name' => $validatordata['name'],
                'email' => $validatordata['email'],
            ]);
        }

        // Memeriksa jika mentor tidak ditemukan
        if (!$mentor) {
            // Jika tidak ditemukan, kembalikan respons JSON dengan pesan 'User not found or not an admin' dan status 404 (Not Found)
            return response()->json(['message' => 'User not found or not an admin'], 404);
        }

        // Jika berhasil mengedit mentor, kembalikan respons JSON sukses dengan pesan 'User updated successfully' dan data mentor yang diperbarui
        return response()->json(['message' => 'User updated successfully', 'user' => $mentor]);
    }

    public static function deletementor($mentor)
    {
        // Menghapus foto mentor dari penyimpanan yang terkait dengan mentor tersebut
        Storage::delete('public/profile/' . $mentor->photo);

        // Menghapus mentor dari database
        if ($mentor->delete()) {
            // Jika berhasil menghapus, kembalikan respons dengan pesan sukses
            return response([
                'Berhasil Menghapus Data'
            ]);
        } else {
            // Jika tidak berhasil menghapus, kembalikan respons dengan pesan gagal
            return response([
                'Tidak Berhasil Menghapus Data'
            ]);
        }

        // Kembalikan respons JSON dengan pesan 'data berhasil dihapus' dan status 200 (OK)
        return response()->json(['message' => 'data berhasil dihapus'], 200);
    }

    //-------------------- Aktivasi Mentor --------------------//
}
