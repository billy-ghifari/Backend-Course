<?php

namespace App\Helper;

use App\Models\kelas;
use Illuminate\Support\Facades\Storage;

class Kelashelper
{

    //-------------------- Read Kelas --------------------//

    public static function paginate()
    {
        // Mengambil data kelas dengan urutan terbaru dan menggunakan metode paginate() untuk membagi data menjadi halaman-halaman dengan dua kelas per halaman
        $kelas = Kelas::latest()->paginate(2);

        // Mengembalikan respons JSON yang berisi pesan 'List data review' dan data kelas yang telah dipaginasi
        return response()->json(['message' => 'List data review', 'data' => $kelas]);
    }

    //-------------------- Read Kelas --------------------//



    //-------------------- Create Kelas --------------------//

    public static function makekelas($validatordata)
    {
        // Membuat entri baru dalam tabel kelas berdasarkan data yang telah divalidasi
        $kelas = Kelas::create([
            'nama' => $validatordata['nama'],
            'deskripsi' => $validatordata['deskripsi'],
            'foto_thumbnail' => $validatordata['foto_thumbnail'],
            'r_id_non_siswa' => $validatordata['r_id_non_siswa'],
        ]);

        // Mengembalikan respons JSON yang menyatakan bahwa data berhasil ditambahkan, serta data kelas yang baru saja ditambahkan ke database
        return response()->json(['message' => 'data berhasil ditambahkan', 'data' => $kelas], 200);
    }


    //-------------------- Create Kelas --------------------//



    //-------------------- Update Kelas --------------------//

    public static function updatekelas($kelas, $validatordata)
    {
        // Memeriksa apakah $validatordata tidak kosong atau terdefinisi
        if ($validatordata) {
            // Jika $validatordata memiliki nilai, memperbarui atribut 'nama' dan 'deskripsi' dari kelas ($kelas) yang telah dipilih
            $kelas->update([
                'nama' => $validatordata['nama'],
                'deskripsi' => $validatordata['deskripsi'],
            ]);
        }

        // Mengembalikan respons JSON yang menyatakan bahwa data berhasil diubah, serta data kelas yang telah diperbarui
        return response()->json(['message' => 'data berhasil diubah', 'data' => $kelas], 200);
    }


    //-------------------- Update Kelas --------------------//



    //-------------------- Delete Kelas --------------------//

    public static function deletekelas($kelas)
    {
        // Menghapus file foto thumbnail yang terkait dengan kelas dari penyimpanan (storage)
        Storage::delete('public/profile/' . $kelas->foto_thumbnail);

        // Memeriksa apakah penghapusan kelas berhasil
        if ($kelas->delete()) {
            // Jika penghapusan berhasil, mengembalikan respons yang berisi pesan 'Berhasil Menghapus Data'
            return response([
                'Berhasil Menghapus Data'
            ]);
        } else {
            // Jika penghapusan gagal, mengembalikan respons yang berisi pesan 'Tidak Berhasil Menghapus Data'
            return response([
                'Tidak Berhasil Menghapus Data'
            ]);
        }

        // Mengembalikan respons JSON yang menyatakan bahwa data berhasil dihapus dengan status kode HTTP 200 (OK)
        return response()->json(['message' => 'data berhasil dihapus'], 200);
    }

    //-------------------- Delete Kelas --------------------//
}
