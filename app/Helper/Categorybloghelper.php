<?php

namespace App\Helper;

use App\Models\categoryblog;

class CategoryBlogHelper
{

    //-------------------- Read Categoryblog --------------------//

    public static function paginate()
    {
        // Mengambil data kelas dengan urutan terbaru dan menggunakan metode paginate() untuk membagi data menjadi halaman-halaman dengan dua kelas per halaman
        $categoryblog = categoryblog::latest()->get();

        // Mengembalikan respons JSON yang berisi pesan 'List data review' dan data kelas yang telah dipaginasi
        return response()->json(['message' => 'List data review', 'data' => $categoryblog]);
    }

    public static function getallcatblog()
    {
        // Mengambil semua data pengguna yang memiliki peran (status) 'siswa'
        $blog = categoryblog::all();

        // Mengembalikan respon JSON yang berisi data siswa
        return response()->json($blog);
    }

    //-------------------- Read Categoryblog --------------------//



    //-------------------- Create Category --------------------//

    public static function create($validatordata)
    {
        // Membuat entri baru dalam tabel kelas berdasarkan data yang telah divalidasi
        $kelas = categoryblog::create([
            'nama' => $validatordata['nama'],
            'photo' => $validatordata['photo'],
        ]);

        // Mengembalikan respons JSON yang menyatakan bahwa data berhasil ditambahkan, serta data kelas yang baru saja ditambahkan ke database
        return response()->json(['message' => 'data berhasil ditambahkan', 'data' => $kelas], 200);
    }

    //-------------------- Create Category --------------------//



    //-------------------- Delete Category --------------------//

    public static function destroy($category)
    {
        try {
            // Menghapus entri kategori yang diberikan ($category)
            $category->delete();

            // Mengembalikan respons JSON yang menyatakan bahwa data berhasil dihapus dengan status kode 200 (OK)
            return response()->json(['message' => 'data berhasil dihapus'], 200);
        } catch (\Exception $ex) {
            // Jika terjadi exception (kesalahan) selama proses penghapusan, tangkap exception tersebut
            // dan kembalikan respons JSON yang berisi informasi tentang exception dengan status kode 422 (Unprocessable Entity)
            return response()->json($ex, 422);
        }
    }

    //-------------------- Delete Category --------------------//
}
