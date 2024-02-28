<?php

namespace App\Helper;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class Adminhelper
{

    //-------------------- Aktivasi Siswa --------------------//

    public static function allsiswa()
    {
        // Mengambil pengguna yang sedang terotentikasi
        $user = Auth::user();

        // Memeriksa jika tidak ada pengguna yang terotentikasi
        if (!$user) {
            // Mengembalikan respons JSON dengan pesan kesalahan dan kode status 401 (Unauthorized)
            return response()->json(['message' => 'tidak ada siswa'], 401);
        }

        // Mengambil semua pengguna dengan peran 'siswa'
        $siswa = User::where('role', 'siswa')->get();

        // Mengembalikan respons JSON yang berisi data 'siswa' yang telah diambil
        return response()->json($siswa);
    }

    public static function siswa_on()
    {
        // Mengambil pengguna yang saat ini terotentikasi
        $user = Auth::user();

        // Memeriksa jika tidak ada pengguna yang terotentikasi
        if (!$user) {
            // Mengembalikan respons JSON dengan pesan kesalahan dan kode status 401 (Unauthorized)
            return response()->json(['message' => 'tidak ada siswa'], 401);
        }

        // Menghitung jumlah pengguna dengan status 'aktif' dan peran 'siswa'
        $siswaCount = User::where('status', 'aktif')
            ->where('role', 'siswa')
            ->count();

        // Mengembalikan respons JSON yang berisi jumlah siswa yang aktif
        return response()->json($siswaCount);
    }

    public static function get_siswa()
    {
        // Mengambil daftar siswa dari basis data dengan paginasi (10 siswa per halaman)
        $siswa = User::where('role', 'siswa')
            ->simplepaginate(10);

        // Mengembalikan daftar siswa dalam bentuk halaman
        return $siswa;
    }

    public static function activation($siswa)
    {
        // Mengubah status akun siswa menjadi 'aktif'
        $siswa->update([
            'status' => 'aktif'
        ]);

        // Mengembalikan respons JSON dengan pesan 'akun sudah aktif' dan data akun yang telah diaktifkan
        return response()->json(['message' => 'akun sudah aktif', 'data' => $siswa], 200);
    }

    public static function nonactivation($siswa)
    {
        // Mengubah status akun siswa menjadi 'non' (tidak aktif)
        $siswa->update([
            'status' => 'non'
        ]);

        // Mengembalikan respons JSON dengan pesan 'akun sudah dinonaktifkan' dan data akun yang telah dinonaktifkan
        return response()->json(['message' => 'akun sudah dinonaktifkan', 'data' => $siswa], 200);
    }

    //-------------------- Aktivasi Siswa --------------------//



    //-------------------- CRUD Admin --------------------//

    public static function makeadmin($validatordata)
    {
        // Mendapatkan ID terakhir dari pengguna
        $lastid = User::latest()->value('id');
        $nextid = $lastid + 1;

        // Mengenkripsi ID untuk UUID
        $encryptuuid = Crypt::encrypt($nextid);

        // Membuat admin baru dalam basis data
        $user = User::create([
            'name'     => $validatordata['name'],
            'email'    => $validatordata['email'],
            'password' => bcrypt($validatordata['password']),
            'photo'    => $validatordata['photo'],
            'uuid'     => $encryptuuid,
            'role'     => 'admin',
            'status'   => 'aktif'
        ]);

        // Mengembalikan respons JSON berisi informasi hasil pembuatan admin
        if ($user) {
            return response()->json([
                'success' => true,
                'last_user' => $lastid,
                'uuid' => $encryptuuid,
                'user'   => $user,
            ], 201);
        }

        return response()->json([
            'success' => false,
        ]);
    }

    public static function alladmin()
    {
        // Mengambil semua admin dari basis data
        $admin = User::where('role', 'admin')->get();

        // Jika tidak ada admin, kembalikan pesan kesalahan
        if (!$admin) {
            return response()->json(['message' => 'tidak ada admin'], 401);
        }

        return response()->json($admin);
    }

    public static function editadmin($admin, $validatordata)
    {
        // Jika data valid, lakukan pembaruan pada admin
        if ($validatordata) {
            $admin->update([
                'name' => $validatordata['name'],
                'email' => $validatordata['email'],
            ]);
        }

        // Jika admin tidak ditemukan, kembalikan pesan kesalahan
        if (!$admin) {
            return response()->json(['message' => 'User not found or not an admin'], 404);
        }

        return response()->json(['message' => 'User updated successfully', 'user' => $admin]);
    }

    public static function deleteadmin($admin)
    {
        // Menghapus foto profil dari penyimpanan
        Storage::delete('public/profile/' . $admin->photo);

        // Jika penghapusan berhasil, kembalikan respons berhasil, jika tidak, kembalikan respons gagal
        if ($admin->delete()) {
            return response([
                'Berhasil Menghapus Data'
            ]);
        } else {
            return response([
                'Tidak Berhasil Menghapus Data'
            ]);
        }

        return response()->json(['message' => 'data berhasil dihapus'], 200);
    }

    public static function get_profile($id)
    {
        // Mendapatkan profil pengguna berdasarkan ID
        $profile = User::findOrFail($id);

        // Jika profil tidak ditemukan, kembalikan pesan kesalahan
        if (!$profile) {
            return response()->json(['message' => 'tidak ada siswa'], 401);
        }

        return response()->json($profile);
    }

    public static function getiduser($uuid)
    {
        // Mendapatkan ID pengguna berdasarkan UUID
        $profile = User::where('uuid', $uuid)->value('id');

        // Jika tidak ditemukan, kembalikan pesan kesalahan
        if (!$profile) {
            return response()->json(['message' => 'tidak ada siswa'], 401);
        }

        return response()->json($profile);
    }

    //-------------------- CRUD Admin --------------------//



    //-------------------- Aktivasi Mentor --------------------//

    public static function makementor($validatordata)
    {
        // Mengambil ID terbaru dari model User
        $lastid = User::latest()->value('id');
        // Menghitung ID berikutnya dengan menambahkan 1 ke ID terbaru
        $nextid = $lastid + 1;
        // Enkripsi ID berikutnya
        $encryptuuid = Crypt::encrypt($nextid);

        // Membuat user baru dengan data yang diberikan
        $user = User::create([
            'name'     => $validatordata['name'],
            'email'    => $validatordata['email'],
            'password' => bcrypt($validatordata['password']),
            'photo'    => $validatordata['photo'],
            'uuid'     => $encryptuuid,
            'role'     => 'mentor',
            'status'   => 'aktif'
        ]);

        // Memeriksa apakah pembuatan user berhasil
        if ($user) {
            // Mengembalikan respons JSON yang menunjukkan keberhasilan dan detail user
            return response()->json([
                'success'   => true,
                'last_user' => $lastid,
                'uuid'      => $encryptuuid,
                'user'      => $user,
            ], 201);
        }

        // Mengembalikan respons JSON yang menunjukkan kegagalan
        return response()->json([
            'success' => false,
        ]);
    }

    public static function allmentor()
    {
        // Mengambil semua user dengan peran 'mentor', dipaginasi dengan 8 user per halaman
        $admin = User::where('role', 'mentor')
            ->latest()
            ->paginate(8);

        // Memeriksa apakah mentor ditemukan
        if (!$admin) {
            // Mengembalikan respons JSON yang menunjukkan tidak ada mentor ditemukan
            return response()->json(['message' => 'tidak ada admin'], 401);
        }

        // Mengembalikan respons JSON dengan daftar mentor yang dipaginasi
        return response()->json($admin);
    }

    public static function editmentor($mentor, $validatordata)
    {
        // Memeriksa apakah data yang valid diberikan
        if ($validatordata) {
            // Memperbarui nama dan email mentor dengan data yang diberikan
            $mentor->update([
                'name'  => $validatordata['name'],
                'email' => $validatordata['email'],
            ]);
        }

        // Memeriksa apakah mentor ada
        if (!$mentor) {
            // Mengembalikan respons JSON yang menunjukkan mentor tidak ditemukan atau bukan admin
            return response()->json(['message' => 'User not found or not an admin'], 404);
        }

        // Mengembalikan respons JSON yang menunjukkan berhasil memperbarui user
        return response()->json(['message' => 'User updated successfully', 'user' => $mentor]);
    }

    public static function deletementor($mentor)
    {
        // Menghapus foto mentor dari penyimpanan
        Storage::delete('public/profile/' . $mentor->photo);

        // Mencoba menghapus mentor
        if ($mentor->delete()) {
            // Mengembalikan respons yang menunjukkan penghapusan berhasil
            return response([
                'Berhasil Menghapus Data'
            ]);
        } else {
            // Mengembalikan respons yang menunjukkan gagal menghapus
            return response([
                'Tidak Berhasil Menghapus Data'
            ]);
        }

        // Mengembalikan respons JSON yang menunjukkan penghapusan data berhasil
        return response()->json(['message' => 'data berhasil dihapus'], 200);
    }

    //-------------------- Aktivasi Mentor --------------------//
}
