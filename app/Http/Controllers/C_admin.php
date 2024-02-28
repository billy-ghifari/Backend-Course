<?php

namespace App\Http\Controllers;

use App\Helper\Adminhelper;
use App\Models\kelas;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class C_admin extends Controller
{

    //-------------------- Aktivasi Siswa --------------------//

    public function index()
    {
        try {
            // Mencoba untuk mengambil semua data siswa menggunakan Adminhelper::allsiswa()
            $allsiswa = Adminhelper::allsiswa();

            // Mengembalikan data siswa yang berhasil diambil
            return $allsiswa;
        } catch (\Exception $e) {
            // Jika terjadi kesalahan selama proses:
            // Mengembalikan respons JSON dengan pesan error dan kode status 500 (Kesalahan Server Internal)
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function siswa_on()
    {
        try {
            // Mencoba untuk mengambil data siswa yang aktif menggunakan Adminhelper::siswa_on()
            $allsiswa = Adminhelper::siswa_on();

            // Mengembalikan data siswa yang aktif yang berhasil diambil
            return $allsiswa;
        } catch (\Exception $e) {
            // Jika terjadi kesalahan selama proses:
            // Mengembalikan respons JSON dengan pesan error dan kode status 500 (Kesalahan Server Internal)
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function activationsiswa($id)
    {
        try {
            try {
                // Mencari siswa berdasarkan ID
                $siswa = User::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                // Jika siswa tidak ditemukan, mengembalikan respons JSON dengan pesan error dan kode status 422 (Unprocessable Entity)
                return response()->json("Tidak dapat menemukan siswa", 422);
            }

            // Mengaktifkan siswa menggunakan Adminhelper::activation($siswa)
            $activation = Adminhelper::activation($siswa);

            // Mengembalikan hasil aktivasi siswa
            return $activation;
        } catch (\Exception $e) {
            // Jika terjadi kesalahan selama proses aktivasi, mengembalikan respons JSON dengan pesan kesalahan dan kode status 500 (Kesalahan Server Internal)
            return response()->json(['message' => 'Gagal aktivasi '], 500);
        }
    }

    public function get_siswa()
    {
        try {
            // Mengambil daftar siswa menggunakan Adminhelper::get_siswa()
            $siswa = Adminhelper::get_siswa();

            // Mengembalikan daftar siswa dalam respons JSON dengan status berhasil (true)
            return response()->json([
                'status' => true,
                'data' => $siswa
            ]);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan saat pengambilan data siswa, mengembalikan respons JSON dengan status gagal (false) dan pesan kesalahan, dengan kode status 500 (Kesalahan Server Internal)
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data siswa.'
            ], 500);
        }
    }

    public function nonactivationsiswa($id)
    {
        try {
            try {
                // Mencari siswa berdasarkan ID
                $siswa = User::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                // Jika siswa tidak ditemukan, mengembalikan respons JSON dengan pesan error dan kode status 422 (Unprocessable Entity)
                return response()->json("Tidak dapat menemukan siswa", 422);
            }

            // Menonaktifkan siswa menggunakan Adminhelper::nonactivation($siswa)
            $nonactivation = Adminhelper::nonactivation($siswa);

            // Mengembalikan hasil non-aktivasi siswa
            return $nonactivation;
        } catch (\Exception $e) {
            // Jika terjadi kesalahan selama proses non-aktivasi, mengembalikan respons JSON dengan pesan kesalahan yang mencakup pesan dari pengecualian yang terjadi, dengan kode status 500 (Kesalahan Server Internal)
            return response()->json(['message' => 'Gagal non-aktivasi ' . $e->getMessage()], 500);
        }
    }

    //-------------------- Aktivasi Siswa --------------------//



    //-------------------- Aktivasi Kelas --------------------//

    public function activationkelas($id)
    {
        try {
            // Mencari kelas berdasarkan ID
            $kelas = Kelas::findOrFail($id);

            // Memperbarui status kelas menjadi 'aktif'
            $kelas->update([
                'status' => 'aktif'
            ]);

            // Mengembalikan respons JSON dengan pesan sukses dan data kelas yang telah diaktifkan, dengan kode status 200 (OK)
            return response()->json(['message' => 'Akun sudah aktif', 'data' => $kelas], 200);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan selama proses aktivasi, mengembalikan respons JSON dengan pesan kesalahan yang mencakup pesan dari pengecualian yang terjadi, dengan kode status 500 (Kesalahan Server Internal)
            return response()->json(['message' => 'Gagal aktivasi ' . $e->getMessage()], 500);
        }
    }

    //-------------------- Aktivasi Kelas --------------------//



    //-------------------- CRUD Admin --------------------//

    public function makeadmin(Request $request)
    {
        // Validasi data input dari request
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required',
            'photo'    => 'required|image'
        ]);

        // Jika validasi gagal, kembalikan respons JSON dengan error 422 (Unprocessable Entity)
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Dapatkan data yang telah divalidasi
        $validatordata = $validator->validated();

        // Simpan foto admin dengan nama unik di folder 'profile'
        $imageName = time() . '.' . $validatordata['photo']->extension();
        $request->photo->move(public_path('profile'), $imageName);
        $validatordata['photo'] = $imageName;

        // Buat admin baru menggunakan Adminhelper::makeadmin($validatordata)
        $user = Adminhelper::makeadmin($validatordata);

        // Jika pembuatan admin berhasil, kembalikan respons JSON dengan sukses dan data admin, kode status 201 (Created)
        if ($user) {
            return response()->json([
                'success' => true,
                'user'    => $user,
            ], 201);
        }

        // Jika gagal membuat admin, kembalikan respons JSON dengan status kegagalan (false)
        return response()->json([
            'success' => false,
        ]);
    }

    public function alladmin()
    {
        try {
            // Ambil semua data admin menggunakan Adminhelper::alladmin()
            $admin = Adminhelper::alladmin();

            // Kembalikan data admin dalam respons JSON dengan status berhasil (true)
            return response()->json([
                'status' => true,
                'data' => $admin
            ]);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan saat mengambil data admin, kembalikan respons JSON dengan status gagal (false) dan pesan kesalahan, dengan kode status 500 (Kesalahan Server Internal)
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil admin.'
            ], 500);
        }
    }

    public function updateadmin(Request $request, $id)
    {
        // Cari admin berdasarkan ID dan peran ('role') admin
        $admin = User::where('id', $id)->where('role', 'admin')->first();

        // Validasi data input dari request
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
        ]);

        // Jika validasi gagal, kembalikan respons JSON dengan error 422 (Unprocessable Entity)
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Dapatkan data yang telah divalidasi
        $validatordata = $validator->validated();

        // Edit data admin menggunakan Adminhelper::editadmin($admin, $validatordata)
        $editadmin = Adminhelper::editadmin($admin, $validatordata);

        return $editadmin; // Kembalikan respons (hasil dari editadmin)
    }

    public function destroyadmin($id)
    {
        try {
            // Cari admin berdasarkan ID dan peran ('role') admin
            $admin = User::where('id', $id)->where('role', 'admin')->first();
        } catch (ModelNotFoundException $e) {
            // Jika tidak ditemukan admin dengan ID tersebut, kembalikan respons JSON dengan pesan error dan kode status 422 (Unprocessable Entity)
            return response()->json("Bukan admin", 422);
        }

        // Hapus admin menggunakan Adminhelper::deleteadmin($admin)
        $deleteadmin = Adminhelper::deleteadmin($admin);

        return $deleteadmin; // Kembalikan respons (hasil dari deleteadmin)
    }

    public function get_profile($id)
    {
        // Ambil profil admin berdasarkan ID menggunakan Adminhelper::get_profile($id)
        $profile = Adminhelper::get_profile($id);
        return $profile; // Kembalikan respons (profil admin)
    }

    public function getiduser($uuid)
    {
        // Ambil data pengguna berdasarkan UUID menggunakan Adminhelper::getiduser($uuid)
        $blog = Adminhelper::getiduser($uuid);
        return $blog; // Kembalikan respons (data pengguna)
    }

    //-------------------- CRUD Admin --------------------//



    //-------------------- CRUD Mentor --------------------//

    public function makementor(Request $request)
    {
        // Validasi data input dari request
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required',
            'photo'    => 'required|image'
        ]);

        // Jika validasi gagal, kembalikan respons JSON dengan error 422 (Unprocessable Entity)
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Dapatkan data yang telah divalidasi
        $validatordata = $validator->validated();

        // Simpan foto mentor dengan nama unik di folder 'profile'
        $imageName = time() . '.' . $validatordata['photo']->extension();
        $request->photo->move(public_path('profile'), $imageName);
        $validatordata['photo'] = $imageName;

        // Buat mentor baru menggunakan Adminhelper::makementor($validatordata)
        $user = Adminhelper::makementor($validatordata);

        // Jika pembuatan mentor berhasil, kembalikan respons JSON dengan sukses dan data mentor, kode status 201 (Created)
        if ($user) {
            return response()->json([
                'success' => true,
                'user'    => $user,
            ], 201);
        }

        // Jika gagal membuat mentor, kembalikan respons JSON dengan status kegagalan (false)
        return response()->json([
            'success' => false,
        ]);
    }

    public function allmentor()
    {
        try {
            // Ambil semua data mentor menggunakan Adminhelper::allmentor()
            $mentor = Adminhelper::allmentor();

            // Kembalikan data mentor dalam respons JSON dengan status berhasil (true)
            return response()->json([
                'status' => true,
                'data' => $mentor
            ]);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan saat mengambil data mentor, kembalikan respons JSON dengan status gagal (false) dan pesan kesalahan, dengan kode status 500 (Kesalahan Server Internal)
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil mentor.'
            ], 500);
        }
    }

    public function updatementor(Request $request, $id)
    {
        // Cari mentor berdasarkan ID dan peran ('role') mentor
        $mentor = User::where('id', $id)->where('role', 'mentor')->first();

        // Validasi data input dari request
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
        ]);

        // Jika validasi gagal, kembalikan respons JSON dengan error 422 (Unprocessable Entity)
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Dapatkan data yang telah divalidasi
        $validatordata = $validator->validated();

        // Edit data mentor menggunakan Adminhelper::editmentor($mentor, $validatordata)
        $editmentor = Adminhelper::editmentor($mentor, $validatordata);

        return $editmentor; // Kembalikan respons (hasil dari editmentor)
    }

    public function destroymentor($id)
    {
        try {
            // Cari mentor berdasarkan ID dan peran ('role') mentor
            $mentor = User::where('id', $id)->where('role', 'mentor')->first();

            // Hapus foto mentor dan hapus data mentor
            Storage::delete('public/profile/' . $mentor->photo);
            if ($mentor->delete()) {
                return response(['Berhasil Menghapus Data']);
            } else {
                return response(['Tidak Berhasil Menghapus Data']);
            }
        } catch (Throwable $ex) {
            return response()->json($ex, 422);
        }
    }

    //-------------------- CRUD Mentor --------------------//
}
