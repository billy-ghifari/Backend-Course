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
        $blogs = Blog::select('judul', 'category.nama_category', 'content', 'foto_thumbnail', 'users.name', 'users.photo', 'blog.created_at')
            ->join('category', 'category.id', '=', 'blog.r_id_category')
            ->join('users', 'users.id', '=', 'blog.r_id_non_siswa')
            ->latest()
            ->paginate(4);


        // Mengembalikan respons JSON yang berisi pesan 'List data review' dan data blog yang telah dipaginasi
        return response()->json([
            'message' => 'List data review', 'data' => $blogs
        ]);
    }

    public static function paginateall()
    {
        $blog = blog::select('judul', 'category.nama_category', 'content', 'foto_thumbnail', 'users.name', 'users.photo', 'blog.created_at')
            ->join('users', 'users.id', '=', 'blog.r_id_non_siswa')
            ->join('category', 'category.id', '=', 'blog.r_id_category')
            ->inRandomOrder()
            ->paginate(4);

        return response()->json(['message' => 'List data review', 'data' => $blog]);
    }

    public static function allblog()
    {
        $blogs = blog::select('blog.id', 'judul', 'category_blog.nama', 'content', 'foto_thumbnail', 'users.name', 'users.photo', 'blog.created_at')
            ->join('users', 'users.id', '=', 'blog.r_id_non_siswa')
            ->join('category_blog', 'category_blog.id', '=', 'blog.r_id_category')
            ->paginate(10);

        return $blogs;
    }

    public static function getallblog()
    {
        // Mengambil semua data pengguna yang memiliki peran (status) 'siswa'
        $blog = blog::count();

        // Mengembalikan respon JSON yang berisi data siswa
        return response()->json($blog);
    }

    public static function getblog($id)
    {
        try {
            $blog = blog::select('judul', 'category_blog.nama', 'content', 'foto_thumbnail', 'users.name', 'users.photo', 'blog.created_at')
                ->join('users', 'users.id', '=', 'blog.r_id_non_siswa')
                ->join('category_blog', 'category_blog.id', '=', 'blog.r_id_category')
                ->findOrFail($id);

            return $blog;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception('blog tidak ditemukan');
        }
    }

    public static function get_blog($id)
    {
        // Mengambil semua data pengguna yang memiliki peran (status) 'siswa'
        $profile = blog::findOrFail($id)->value();

        // Memeriksa apakah pengguna terautentikasi
        if (!$profile) {
            // Jika tidak terautentikasi, kembalikan respon JSON dengan pesan 'tidak ada siswa' dan status 401 (Unauthorized)
            return response()->json(['message' => 'tidak ada siswa'], 401);
        }

        // Mengembalikan respon JSON yang berisi data siswa
        return response()->json($profile);
    }

    //-------------------- Read Blog --------------------//

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
