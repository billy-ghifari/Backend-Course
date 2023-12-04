<?php

namespace App\Http\Controllers;

use App\Helper\Materihelper;
use App\Models\materi;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class C_materi extends Controller
{
    //-------------------- Create Materi --------------------//

    public function post_materi(Request $request)
    {
        // Validasi data yang diterima dari request menggunakan Validator
        $validator = Validator::make($request->all(), [
            'r_id_kelas' => 'required',
            'judul_materi' => 'required',
            'link_materi' => 'required',
            'deskripsi_materi' => 'required',
            'durasi' => 'required',
        ]);

        // Memeriksa apakah validasi gagal
        if ($validator->fails()) {
            // Jika validasi gagal, kembalikan respons berupa pesan error yang menjelaskan detail kesalahan validasi
            return response()->json($validator->errors(), 422);
        }

        // Jika data yang diterima lolos dari proses validasi, data tersebut akan disimpan dalam variabel $validatordata
        $validatordata = $validator->validated();

        // Menggunakan Materihelper::makemateri() untuk membuat entri baru dalam database yang berisi informasi tentang materi yang ditambahkan
        $makemateri = Materihelper::makemateri($validatordata);

        // Respons dari proses ini akan mengembalikan hasil dari fungsi Materihelper::makemateri(), yang kemudian akan digunakan sebagai respons dari fungsi post_materi()
        return $makemateri;
    }


    //--------------------Create Materi --------------------//



    //-------------------- Update Materi --------------------//

    public function update(Request $request, $id)
    {
        try {
            // Mencari materi berdasarkan ID yang diberikan
            $materi = materi::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            // Jika materi tidak ditemukan, kembalikan respons berupa pesan error
            return response()->json("Tidak dapat menemukan materi", 422);
        }

        // Validasi data yang diterima dari request menggunakan Validator
        $validator = Validator::make($request->all(), [
            'r_id_kelas' => 'required',
            'judul_materi' => 'required',
            'link_materi' => 'required',
            'deskripsi_materi' => 'required',
            'durasi' => 'required',
        ]);

        // Memeriksa apakah validasi gagal
        if ($validator->fails()) {
            // Jika validasi gagal, kembalikan respons berupa pesan error yang menjelaskan detail kesalahan validasi
            return response()->json($validator->errors(), 422);
        }

        // Jika data yang diterima lolos dari proses validasi, data tersebut akan disimpan dalam variabel $validatordata
        $validatordata = $validator->validated();

        // Menggunakan Materihelper::updatemateri() untuk memperbarui informasi materi yang ada berdasarkan data yang diterima dari request
        $updatemateri = Materihelper::updatemateri($materi, $validatordata);

        // Respons dari proses ini akan mengembalikan hasil dari fungsi Materihelper::updatemateri(), yang kemudian akan digunakan sebagai respons dari fungsi update()
        return $updatemateri;
    }

    //-------------------- Update Materi --------------------//



    //-------------------- Delete Materi --------------------//

    public function destroy($id)
    {
        try {
            try {
                // Mencari materi berdasarkan ID yang diberikan
                $materi = materi::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                // Jika materi tidak ditemukan, kembalikan respons berupa pesan error
                return response()->json("Tidak dapat menemukan materi", 422);
            }

            // Menggunakan Materihelper::deletemateri() untuk menghapus materi yang ditemukan
            $deletemateri = Materihelper::deletemateri($materi);

            // Respons dari proses ini akan mengembalikan hasil dari fungsi Materihelper::deletemateri()
            return $deletemateri;
        } catch (Throwable $ex) {
            // Jika terjadi kesalahan yang tidak terduga, tangkap dan kembalikan respons berupa pesan error
            return response()->json($ex, 422);
        }
    }

    //-------------------- Delete Materi --------------------//
}
