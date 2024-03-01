<?php

namespace App\Helper;

use App\Models\kelas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Kelashelper
{

    //-------------------- Read Kelas --------------------//

    public static function paginate()
    {
        // Memilih kolom-kolom tertentu dari tabel Kelas beserta informasi terkait pengguna dan kategori
        $kelas = Kelas::select('nama', 'deskripsi', 'foto_thumbnail', 'users.name', 'users.photo', 'category_kelas.nama', 'kelas.created_at')
            ->join('users', 'users.id', '=', 'kelas.r_id_non_siswa') // Melakukan JOIN dengan tabel users berdasarkan kolom id
            ->join('category_kelas', 'kelas.id', '=', 'kelas.r_id_category') // Melakukan JOIN dengan tabel category berdasarkan kolom id
            ->latest() // Mengurutkan hasil dari yang terbaru berdasarkan kolom created_at
            ->paginate(8); // Melakukan paginasi dengan menampilkan 8 entri per halaman

        // Mengembalikan respons dalam format JSON yang berisi pesan 'List data review' dan data kelas yang telah dipaginasi
        return response()->json(['message' => 'List data review', 'data' => $kelas]);
    }

    public static function paginateall()
    {
        // Memilih kolom-kolom tertentu dari tabel Kelas beserta informasi terkait pengguna dan kategori
        $kelas = Kelas::select('nama', 'deskripsi', 'foto_thumbnail', 'users.name', 'users.photo', 'category_kelas.nama', 'kelas.created_at')
            ->join('users', 'users.id', '=', 'kelas.r_id_non_siswa') // Melakukan JOIN dengan tabel users berdasarkan kolom id
            ->join('category_kelas', 'kelas.id', '=', 'kelas.r_id_category') // Melakukan JOIN dengan tabel category berdasarkan kolom id
            ->inRandomOrder() // Mengambil hasil secara acak
            ->paginate(4); // Melakukan paginasi dengan menampilkan 4 entri per halaman

        // Mengembalikan respons dalam format JSON yang berisi pesan 'List data review' dan data kelas yang telah dipaginasi secara acak
        return response()->json(['message' => 'List data review', 'data' => $kelas]);
    }

    public static function paginatekelas()
    {
        // Mengambil satu entri kelas secara acak dari tabel Kelas menggunakan inRandomOrder() dan first()
        $kelas = Kelas::inRandomOrder()->first();

        // Mengembalikan respons dalam format JSON yang berisi pesan 'List data review' dan satu entri kelas yang dipilih secara acak
        return response()->json(['message' => 'List data review', 'data' => $kelas]);
    }

    public static function get_kelas()
    {
        // Memilih kolom-kolom tertentu dari tabel Kelas beserta informasi terkait pengguna dan kategori
        $kelas = Kelas::select('kelas.id', 'nama', 'deskripsi', 'foto_thumbnail', 'users.name', 'users.photo', 'category_kelas.nama', 'kelas.created_at')
            ->join('users', 'users.id', '=', 'kelas.r_id_non_siswa') // Melakukan JOIN dengan tabel users berdasarkan kolom id
            ->join('category_kelas', 'kelas.id', '=', 'kelas.r_id_category') // Melakukan JOIN dengan tabel category berdasarkan kolom id
            ->latest() // Mengurutkan hasil dari yang terbaru berdasarkan kolom created_at
            ->paginate(10); // Melakukan paginasi dengan menampilkan 10 entri per halaman

        return $kelas; // Mengembalikan data kelas yang telah dipaginasi
    }

    public static function getallkelas()
    {
        $user = Auth::user(); // Memeriksa apakah terdapat pengguna yang terautentikasi

        if (!$user) {
            return response()->json(['message' => 'tidak ada siswa'], 401); // Jika tidak ada pengguna yang terautentikasi, kembalikan respons JSON dengan pesan 'tidak ada siswa' dan status kode 401
        }

        // Menghitung jumlah entri kelas yang ada di basis data
        $kelas = Kelas::select('nama', 'deskripsi', 'foto_thumbnail', 'users.name', 'users.photo', 'kelas.created_at')
            ->join('users', 'users.id', '=', 'kelas.r_id_non_siswa') // Melakukan JOIN dengan tabel users berdasarkan kolom id
            ->count(); // Menghitung jumlah entri

        return response()->json($kelas); // Mengembalikan respons JSON dengan jumlah entri kelas
    }

    public static function getClassById($id)
    {
        try {
            // Mengambil detail kelas berdasarkan ID yang diberikan
            $class = Kelas::select('nama', 'deskripsi', 'foto_thumbnail', 'users.name', 'users.photo', 'kelas.created_at')
                ->join('users', 'users.id', '=', 'kelas.r_id_non_siswa') // Melakukan JOIN dengan tabel users berdasarkan kolom id
                ->findOrFail($id); // Mengambil entri kelas dengan ID yang sesuai

            return $class; // Mengembalikan detail kelas
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception('Kelas tidak ditemukan'); // Jika kelas dengan ID tersebut tidak ditemukan, lemparkan pengecualian dengan pesan 'Kelas tidak ditemukan'
        }
    }

    //-------------------- Read Kelas --------------------//



    //-------------------- Create Kelas --------------------//

    public static function makekelas($validatordata)
    {
        // Membuat entri baru dalam tabel Kelas dengan menggunakan data yang diterima
        $kelas = Kelas::create([
            'nama' => $validatordata['nama'],
            'deskripsi' => $validatordata['deskripsi'],
            'foto_thumbnail' => $validatordata['foto_thumbnail'],
            'r_id_non_siswa' => $validatordata['r_id_non_siswa'],
            'r_id_category'  => $validatordata['r_id_category'],
        ]);

        // Mengembalikan respons dalam format JSON yang berisi pesan 'data berhasil ditambahkan' dan data kelas yang baru dibuat
        return response()->json(['message' => 'data berhasil ditambahkan', 'data' => $kelas], 200);
    }

    //-------------------- Create Kelas --------------------//



    //-------------------- Update Kelas --------------------//

    public static function updatekelas($id, $validatordata)
    {
        if ($validatordata) {
            // Memperbarui entri kelas yang memiliki ID sesuai dengan yang diberikan
            $kelasupdate = Kelas::where('id', $id)
                ->update([
                    'nama'              => $validatordata['nama'],
                    'deskripsi'         => $validatordata['deskripsi'],
                    'foto_thumbnail'    => $validatordata['foto_thumbnail'],
                    'r_id_non_siswa'    => $validatordata['r_id_non_siswa'],
                    'r_id_category'     => $validatordata['r_id_category'],
                ]);
        }

        // Mengembalikan respons dalam format JSON yang berisi pesan 'data berhasil diubah' dan data entri kelas yang telah diperbarui
        return response()->json(['message' => 'data berhasil diubah', 'data' => $kelasupdate], 200);
    }

    //-------------------- Update Kelas --------------------//



    //-------------------- Delete Kelas --------------------//

    public static function deletekelas($kelas)
    {
        // Menghapus file thumbnail dari penyimpanan
        Storage::delete('public/profile/' . $kelas->foto_thumbnail);

        // Menghapus entri kelas dari database
        if ($kelas->delete()) {
            return response([
                'Berhasil Menghapus Data'
            ]); // Jika penghapusan entri kelas berhasil, kembalikan respons bahwa data berhasil dihapus
        } else {
            return response([
                'Tidak Berhasil Menghapus Data'
            ]); // Jika penghapusan entri kelas gagal, kembalikan respons bahwa data tidak berhasil dihapus
        }

        return response()->json(['message' => 'data berhasil dihapus'], 200); // Respons default jika tidak ada kondisi terpenuhi
    }

    //-------------------- Delete Kelas --------------------//
}
