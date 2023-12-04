<?php

namespace App\Helper;

use App\Models\blog;
use Illuminate\Support\Facades\Storage;

class Bloghelper
{
    //-------------------- Read Blog --------------------//

    public static function pagination()
    {
        // Mengambil data blog dengan urutan terbaru dan menggunakan metode paginate() untuk membagi data menjadi halaman-halaman dengan lima blog per halaman
        $blogs = Blog::latest()->paginate(5);

        // Mengembalikan respons JSON yang berisi pesan 'List data review' dan data blog yang telah dipaginasi
        return response()->json(['message' => 'List data review', 'data' => $blogs]);
    }

    public static function makeblog($validatordata)
    {
        // Membuat entri baru dalam tabel blog berdasarkan data yang telah divalidasi
        $post = Blog::create([
            'judul' => $validatordata['judul'],
            'r_id_category' => $validatordata['r_id_category'],
            'content' => $validatordata['content'],
            'foto_thumbnail' => $validatordata['foto_thumbnail'],
            'r_id_non_siswa' => $validatordata['r_id_non_siswa']
        ]);

        // Mengembalikan respons JSON yang berisi pesan 'data berhasil ditambahkan' dan data blog yang baru ditambahkan
        return response()->json(['message' => 'data berhasil ditambahkan', 'data' => $post], 200);
    }

    //-------------------- Read Blog --------------------//



    //-------------------- Update Blog --------------------//

    public static function updateblog($validatordata, $post)
    {
        // Memeriksa apakah $validatordata tidak kosong atau terdefinisi
        if ($validatordata) {
            // Jika $validatordata memiliki nilai, memperbarui atribut-atribut tertentu dari $post (blog) yang diidentifikasi oleh parameter $post
            $post->update([
                'judul' => $validatordata['judul'],
                'r_id_category' => $validatordata['r_id_category'],
                'content' => $validatordata['content'],
            ]);
        }

        // Mengembalikan respons JSON yang menyatakan bahwa data berhasil diubah, serta data blog yang telah diperbarui
        return response()->json([
            'message' => 'data berhasil diubah', 'data' => $post
        ], 200);
    }

    //-------------------- Update Blog --------------------//



    //-------------------- Delete Blog --------------------//

    public static function deleteblog($post)
    {
        // Menghapus file foto thumbnail terkait dengan blog dari penyimpanan (storage)
        Storage::delete('public/profile/' . $post->foto_thumbnail);

        // Memeriksa apakah penghapusan blog berhasil
        if ($post->delete()) {
            // Jika penghapusan berhasil, mengembalikan respons dengan pesan 'Berhasil Menghapus Data'
            return response([
                'Berhasil Menghapus Data'
            ]);
        } else {
            // Jika penghapusan gagal, mengembalikan respons dengan pesan 'Tidak Berhasil Menghapus Data'
            return response([
                'Tidak Berhasil Menghapus Data'
            ]);
        }

        // Mengembalikan respons JSON yang menyatakan bahwa data berhasil dihapus dengan status kode HTTP 200 (OK)
        return response()->json(['message' => 'data berhasil dihapus'], 200);
    }

    //-------------------- Delete Blog --------------------//
}
