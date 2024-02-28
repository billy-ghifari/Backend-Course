<?php

namespace App\Http\Controllers;

use App\Helper\Kelashelper;
use App\Models\kelas;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class C_kelas extends Controller
{
    //-------------------- Read Kelas --------------------//

    public function index()
    {
        // Mengambil data kelas dengan paginasi menggunakan Kelashelper::paginate()
        $pagination = Kelashelper::paginate();

        // Mengembalikan data paginasi sebagai respons
        return $pagination;
    }

    public function getall_course()
    {
        // Mengambil data kelas dengan paginasi menggunakan Kelashelper::paginateall()
        $paginateall = Kelashelper::paginateall();

        // Mengembalikan data paginasi sebagai respons
        return $paginateall;
    }

    public function getone_kelas()
    {
        // Mengambil data kelas dengan paginasi menggunakan Kelashelper::paginatekelas()
        $paginatekelas = KelasHelper::paginatekelas();

        // Mengembalikan data paginasi sebagai respons
        return $paginatekelas;
    }

    public function get_kelas()
    {
        try {
            $kelas = Kelashelper::get_kelas(); // Panggil helper untuk mendapatkan semua data blog

            return response()->json([
                'status' => true,
                'data' => $kelas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch blogs.'
            ], 500);
        }
    }

    public function getallkelas()
    {
        try {
            // Memanggil method dari Adminhelper untuk mendapatkan semua siswa
            $allkelas = Kelashelper::getallkelas();

            return $allkelas; // Mengembalikan daftar semua siswa
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getClassById($id)
    {
        try {
            $class = Kelashelper::getClassById($id); // Memanggil helper untuk mengambil data kelas berdasarkan ID

            return response()->json(['classData' => $class], 200);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['message' => 'Kelas tidak ditemukan'], 404);
        } catch (\Exception $ex) {
            return response()->json(['message' => $ex->getMessage()], 422);
        }
    }

    //-------------------- Read Kelas --------------------//



    //-------------------- Create Kelas --------------------//

    public function post_kelas(Request $request)
    {
        // Validasi data yang diterima dari request
        $validator = Validator::make($request->all(), [
            'nama'           => 'required',
            'deskripsi'      => 'required',
            'foto_thumbnail' => 'required',
            'r_id_non_siswa' => 'required',
            'r_id_category'  => 'required'
        ]);

        // Jika validasi gagal, kembalikan pesan error
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Ambil data yang telah divalidasi
        $validatordata = $validator->validated();

        // Pindahkan foto thumbnail ke lokasi yang ditentukan dan ubah nama file
        $imageName = time() . '.' . $validatordata['foto_thumbnail']->extension();
        $request->foto_thumbnail->move(
            public_path('kelas'),
            $imageName
        );
        $validatordata['foto_thumbnail'] = $imageName;

        // Buat kelas baru menggunakan Kelashelper::makekelas()
        $makekelas = Kelashelper::makekelas($validatordata);

        // Kembalikan hasil operasi membuat kelas
        return $makekelas;
    }

    //-------------------- Create Kelas --------------------//



    //-------------------- Update Kelas --------------------//

    public function update($id, Request $request)
    {
        // return response()->json(['messages' => $request->all()]);
        try {
            // Mengambil data kelas berdasarkan ID
            $kelas = Kelas::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            // Mengembalikan respons jika kelas tidak ditemukan
            return response()->json("Tidak dapat menemukan kelas", 422);
        }

        // Validasi data yang diterima dari request
        $validator = Validator::make($request->all(), [
            'nama'           => 'required',
            'deskripsi'      => 'required',
            'foto_thumbnail' => 'required',
            'r_id_non_siswa' => 'required',
            'r_id_category'  => 'required'
        ]);


        // Jika validasi gagal, kembalikan pesan error
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Ambil data yang telah divalidasi
        $validatordata = $validator->validated();

        // Menyiapkan nama unik untuk file foto
        $imageName = time() . '.' . $validatordata['foto_thumbnail']->extension();

        // Memindahkan file foto ke direktori public/profile dengan nama unik
        $request->file('foto_thumbnail')->move(public_path('profile'), $imageName);

        // Menyimpan nama file foto ke dalam data yang akan disimpan
        $validatordata['foto_thumbnail'] = $imageName;

        // Panggil Kelashelper::updatekelas() untuk melakukan update data kelas
        $updatekelas = Kelashelper::updatekelas($id, $validatordata);

        // Kembalikan hasil operasi update kelas
        return $updatekelas;
    }

    //-------------------- Update Kelas --------------------//



    //-------------------- Delete Kelas --------------------//

    public function destroy($id)
    {
        try {
            try {
                // Mencoba mencari data kelas berdasarkan ID
                $kelas = Kelas::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                // Jika kelas tidak ditemukan, respons dengan pesan kesalahan
                return response()->json("Tidak dapat menemukan kelas", 422);
            }

            // Memanggil Kelashelper::deletekelas() untuk menghapus kelas
            $deletekelas = Kelashelper::deletekelas($kelas);

            // Mengembalikan respons dari hasil operasi penghapusan kelas
            return $deletekelas;
        } catch (Throwable $ex) {
            // Menangani exception yang mungkin terjadi selama proses penghapusan
            return response()->json($ex, 422);
        }
    }

    //-------------------- Delete Kelas --------------------//
}
