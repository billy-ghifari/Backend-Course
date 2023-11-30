<?php

namespace App\Helper;

use App\Models\materi;

class Materihelper
{
    public static function makemateri($validatordata)
    {
        $post = materi::create([
            'r_id_kelas' => $validatordata['r_id_kelas'],
            'judul_materi' => $validatordata['judul_materi'],
            'link_materi' => $validatordata['link_materi'],
            'deskripsi_materi' => $validatordata['deskripsi_materi'],
            'durasi' => $validatordata['durasi'],
        ]);

        return response()->json(['message' => 'data berhasil ditambahkan', 'data' => $post], 200);
    }

    public static function updatemateri($materi, $validatordata)
    {

        if ($validatordata) {
            $materi->update([
                'r_id_kelas' => $validatordata['r_id_kelas'],
                'judul_materi' => $validatordata['judul_materi'],
                'link_materi' => $validatordata['link_materi'],
                'deskripsi_materi' => $validatordata['deskripsi_materi'],
                'durasi' => $validatordata['durasi'],
            ]);
        }

        return response()->json(['message' => 'data berhasil diubah', 'data' => $materi], 200);
    }

    public static function deletemateri($materi)
    {
        if ($materi->delete()) {
            return response([
                'Berhasil Menghapus Data'
            ]);
        } else {
            //response jika gagal menghapus
            return response([
                'Tidak Berhasil Menghapus Data'
            ]);
        }
        return response()->json(['message' => 'data berhasil dihapus'], 200);
    }
}
