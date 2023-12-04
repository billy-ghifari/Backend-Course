<?php

namespace App\Helper;

use App\Models\materi;

class Materihelper
{

    //-------------------- Create Materi --------------------//

    public static function makemateri($validatordata)
    {
        // Membuat entri baru dalam tabel materi berdasarkan data yang telah divalidasi
        $post = Materi::create([
            'r_id_kelas' => $validatordata['r_id_kelas'],
            'judul_materi' => $validatordata['judul_materi'],
            'link_materi' => $validatordata['link_materi'],
            'deskripsi_materi' => $validatordata['deskripsi_materi'],
            'durasi' => $validatordata['durasi'],
        ]);

        // Mengembalikan respons JSON yang menyatakan bahwa data berhasil ditambahkan, serta data materi yang baru saja ditambahkan ke database
        return response()->json(['message' => 'data berhasil ditambahkan', 'data' => $post], 200);
    }

    //-------------------- Create Materi --------------------//



    //-------------------- Update Materi --------------------//

    public static function updatemateri($materi, $validatordata)
    {
        // Memeriksa apakah $validatordata tidak kosong atau terdefinisi
        if ($validatordata) {
            // Jika $validatordata memiliki nilai, memperbarui atribut materi yang telah dipilih
            $materi->update([
                'r_id_kelas' => $validatordata['r_id_kelas'],
                'judul_materi' => $validatordata['judul_materi'],
                'link_materi' => $validatordata['link_materi'],
                'deskripsi_materi' => $validatordata['deskripsi_materi'],
                'durasi' => $validatordata['durasi'],
            ]);
        }

        // Mengembalikan respons JSON yang menyatakan bahwa data berhasil diubah, serta data materi yang telah diperbarui
        return response()->json(['message' => 'data berhasil diubah', 'data' => $materi], 200);
    }

    //-------------------- Update Materi --------------------//



    //-------------------- Delete Materi --------------------//

    public static function deletemateri($materi)
    {
        // Memeriksa apakah penghapusan materi berhasil
        if ($materi->delete()) {
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

    //-------------------- Delete Materi --------------------//
}
