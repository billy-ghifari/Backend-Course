<?php

namespace App\Http\Controllers;

use App\Helper\Adminhelper;
use App\Models\kelas;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class C_admin extends Controller
{
    // Controller Admin dnajsbfkwfbwegfwebkjbek
    //-------------------- Aktivasi Siswa --------------------//

    public function index()
    {
        try {
            // Memanggil method dari Adminhelper untuk mendapatkan semua siswa
            $allsiswa = Adminhelper::allsiswa();

            return $allsiswa; // Mengembalikan daftar semua siswa
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function activationsiswa($id)
    {
        try {
            try {
                // Mencari siswa berdasarkan ID, jika tidak ditemukan, akan mengembalikan respons JSON dengan pesan error
                $siswa = User::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return response()->json("Tidak dapat menemukan siswa", 422);
            }

            // Memanggil method dari Adminhelper untuk mengaktifkan siswa
            $activation = Adminhelper::activation($siswa);

            return $activation; // Mengembalikan respons aktivasi
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal aktivasi '], 500);
        }
    }

    public function nonactivationsiswa($id)
    {
        try {
            try {
                // Mencari siswa berdasarkan ID, jika tidak ditemukan, akan mengembalikan respons JSON dengan pesan error
                $siswa = User::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return response()->json("Tidak dapat menemukan siswa", 422);
            }

            // Memanggil method dari Adminhelper untuk menonaktifkan siswa
            $nonactivation = Adminhelper::nonactivation($siswa);

            return $nonactivation; // Mengembalikan respons non-aktivasi
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal aktivasi ' . $e->getMessage()], 500);
        }
    }

    //-------------------- Aktivasi Siswa --------------------//



    //-------------------- Aktivasi Kelas --------------------//

    public function activationkelas($id)
    {
        try {
            // Mencari kelas berdasarkan ID
            $kelas = Kelas::findOrFail($id);

            // Mengupdate status kelas menjadi 'aktif'
            $kelas->update([
                'status' => 'aktif'
            ]);

            // Mengembalikan respons JSON yang menyatakan bahwa kelas sudah diaktifkan beserta data kelas yang sudah diubah
            return response()->json(['message' => 'akun sudah aktif', 'data' => $kelas], 200);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan selama proses aktivasi kelas, mengembalikan respons JSON dengan pesan kesalahan beserta informasi exception
            return response()->json(['message' => 'Gagal aktivasi ' . $e->getMessage()], 500);
        }
    }

    //-------------------- Aktivasi Kelas --------------------//



    //-------------------- CRUD Admin --------------------//

    public function registeradmin(Request $request)
    {
        // Membuat validator untuk memeriksa data masukan dari request
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required',
            'photo'    => 'required|image',
        ]);

        // Jika validasi gagal, kembalikan respons JSON yang berisi pesan kesalahan validasi dengan kode status 422
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Mendapatkan data yang telah divalidasi untuk diproses lebih lanjut
        $validatordata = $validator->validated();

        // Membuat nama unik untuk file gambar yang diunggah berdasarkan waktu saat ini dan ekstensi file gambar
        $imageName = time() . '.' . $validatordata['photo']->extension();

        // Memindahkan gambar yang diunggah ke direktori 'public/profile' dengan nama baru yang telah dibuat
        $request->photo->move(public_path('profile'), $imageName);

        // Menyimpan nama file gambar yang telah diunggah ke dalam data yang akan disimpan dalam database
        $validatordata['photo'] = $imageName;

        // Memanggil fungsi makeadmin() dari Adminhelper untuk membuat admin baru dengan data yang telah divalidasi
        $makeadmin = Adminhelper::makeadmin($validatordata);

        // Mengembalikan respons JSON berisi pesan berhasil jika admin berhasil dibuat
        return $makeadmin;
    }

    public function updateadmin(Request $request, $id)
    {
        // Mencari admin berdasarkan ID dan peran ('admin') yang diberikan
        $admin = User::where('id', $id)->where('role', 'admin')->first();

        // Membuat validator untuk memeriksa data masukan dari request
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required',
            'email' => 'sometimes|required',
        ]);

        // Jika validasi gagal, kembalikan respons JSON yang berisi pesan kesalahan validasi dengan kode status 422
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Mendapatkan data yang telah divalidasi untuk diproses lebih lanjut
        $validatordata = $validator->validated();

        // Memanggil fungsi editadmin() dari Adminhelper untuk mengedit admin yang ditemukan
        $editadmin = Adminhelper::editadmin($admin, $validatordata);

        // Mengembalikan respons JSON berisi hasil dari pengeditan admin
        return $editadmin;
    }

    public function destroyadmin($id)
    {
        try {
            // Mencari admin berdasarkan ID dan peran ('admin') yang diberikan
            $admin = User::where('id', $id)->where('role', 'admin')->first();
        } catch (ModelNotFoundException $e) {
            // Jika admin tidak ditemukan, kembalikan respons JSON dengan pesan kesalahan dan kode status 422
            return response()->json("Bukan admin", 422);
        }

        // Memanggil fungsi deleteadmin() dari Adminhelper untuk menghapus admin yang ditemukan
        $deleteadmin = Adminhelper::deleteadmin($admin);

        // Mengembalikan respons JSON berisi hasil dari penghapusan admin
        return $deleteadmin;
    }

    //-------------------- CRUD Admin --------------------//



    //-------------------- CRUD Mentor --------------------//

    public function registermentor(Request $request)
    {
        // Validasi data dari request untuk membuat mentor baru
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required',
            'photo'    => 'required|image',
        ]);

        // Jika validasi gagal, kembalikan respons JSON dengan pesan error 422
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Dapatkan data yang telah divalidasi
        $validatordata = $validator->validated();

        // Simpan foto ke dalam direktori 'profile'
        $imageName = time() . '.' . $validatordata['photo']->extension();
        $request->photo->move(public_path('profile'), $imageName);
        $validatordata['photo'] = $imageName;

        // Buat mentor baru menggunakan data yang telah divalidasi
        $user = User::create([
            'name'     => $validatordata['name'],
            'email'    => $validatordata['email'],
            'password' => bcrypt($validatordata['password']),
            'photo'    => $validatordata['photo'],
            'role'     => 'mentor',
            'status'   => 'aktif'
        ]);

        // Jika pembuatan mentor berhasil, kembalikan respons JSON dengan data user yang baru dibuat
        if ($user) {
            return response()->json([
                'success' => true,
                'user'   => $user,
            ], 201);
        }

        // Jika terjadi kegagalan dalam pembuatan mentor, kembalikan respons JSON dengan nilai 'success' false
        return response()->json([
            'success' => false,
        ]);
    }

    public function updatementor(Request $request, $id)
    {
        try {
            // Cari mentor berdasarkan ID
            $mentor = User::where('id', $id)
                ->where('role', 'mentor')->first();

            // Validasi data yang akan diubah
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required',
                'email' => 'sometimes|required',
            ]);

            // Jika validasi gagal, kembalikan respons JSON dengan pesan error 422
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Dapatkan data yang telah divalidasi
            $validatordata = $validator->validated();

            // Panggil fungsi editadmin dari Adminhelper untuk mengubah data mentor
            $editmentor = Adminhelper::editadmin($mentor, $validatordata);

            // Jika mentor tidak ditemukan, kembalikan respons JSON dengan pesan 'User not found or not an admin'
            if (!$mentor) {
                return response()->json(['message' => 'User not found or not an admin'], 404);
            }

            // Jika berhasil, kembalikan respons JSON dengan pesan 'User updated successfully' dan data mentor yang sudah diubah
            return response()->json(['message' => 'User updated successfully', 'user' => $mentor]);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan selama proses update, kembalikan respons JSON dengan pesan kesalahan dan status 500
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroymentor($id)
    {
        try {
            // Cari mentor berdasarkan ID
            $mentor = User::where('id', $id)
                ->where('role', 'mentor')->first();

            // Hapus foto dari direktori 'profile' dan hapus data mentor
            Storage::delete('public/profile/' . $mentor->photo);
            if ($mentor->delete()) {
                return response([
                    'Berhasil Menghapus Data'
                ]);
            } else {
                return response([
                    'Tidak Berhasil Menghapus Data'
                ]);
            }
        } catch (Throwable $ex) {
            // Tangkap kesalahan jika terjadi dan kembalikan respons JSON dengan informasi kesalahan
            return response()->json($ex, 422);
        }
    }

    //-------------------- CRUD Mentor --------------------//
}
