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
        ]);

        // Jika validasi gagal, kembalikan pesan error
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Ambil data yang telah divalidasi
        $validatordata = $validator->validated();

        // Pindahkan foto thumbnail ke lokasi yang ditentukan dan ubah nama file
        $imageName = time() . '.' . $validatordata['foto_thumbnail']->extension();
        $request->foto_thumbnail->move(public_path('kelas'), $imageName);
        $validatordata['foto_thumbnail'] = $imageName;

        // Buat kelas baru menggunakan Kelashelper::makekelas()
        $makekelas = Kelashelper::makekelas($validatordata);

        // Kembalikan hasil operasi membuat kelas
        return $makekelas;
    }

    //-------------------- Create Kelas --------------------//



    //-------------------- Update Kelas --------------------//

    public function update(Request $request, $id)
    {
        try {
            // Mengambil data kelas berdasarkan ID
            $kelas = Kelas::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            // Mengembalikan respons jika kelas tidak ditemukan
            return response()->json("Tidak dapat menemukan kelas", 422);
        }

        // Validasi data yang diterima dari request
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'deskripsi' => 'required',
        ]);

        // Jika validasi gagal, kembalikan pesan error
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Ambil data yang telah divalidasi
        $validatordata = $validator->validated();

        // Panggil Kelashelper::updatekelas() untuk melakukan update data kelas
        $updatekelas = Kelashelper::updatekelas($kelas, $validatordata);

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
