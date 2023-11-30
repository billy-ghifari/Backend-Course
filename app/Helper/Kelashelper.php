<?php

namespace App\Helper;

use App\Models\kelas;
use Illuminate\Support\Facades\Storage;

class Kelashelper
{
    public static function paginate()
    {
        $kelas = kelas::latest()->paginate(2);
        return response()->json(['message' => 'List data review', 'data' => $kelas]);
    }

    public static function makekelas($validatordata)
    {
        $kelas = kelas::create([
            'nama' => $validatordata['nama'],
            'deskripsi' => $validatordata['deskripsi'],
            'foto_thumbnail' => $validatordata['foto_thumbnail'],
            'r_id_non_siswa' => $validatordata['r_id_non_siswa'],
        ]);

        return response()->json(['message' => 'data berhasil ditambahkan', 'data' => $kelas], 200);
    }

    public static function updatekelas($kelas, $validatordata)
    {

        if ($validatordata) {
            $kelas->update([
                'nama' => $validatordata['nama'],
                'deskripsi' => $validatordata['deskripsi'],
            ]);
        }

        return response()->json(['message' => 'data berhasil diubah', 'data' => $kelas], 200);
    }

    public static function deletekelas($kelas)
    {
        Storage::delete('public/profile/' . $kelas->foto_thumbnail);
        if ($kelas->delete()) {
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
