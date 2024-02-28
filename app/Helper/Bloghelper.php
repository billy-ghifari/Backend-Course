<?php

namespace App\Helper;

use App\Models\blog;
use Illuminate\Support\Facades\Storage;

class Bloghelper
{
    //-------------------- Read Blog --------------------//

    public static function pagination()
    {
        // Mengambil daftar blog terbaru dengan paginasi 4 entri per halaman.
        $blogs = Blog::select('blog.id', 'judul', 'category_blog.nama', 'content', 'foto_thumbnail', 'users.name', 'users.photo', 'blog.created_at')
            ->join('users', 'users.id', '=', 'blog.r_id_non_siswa')
            ->join('category_blog', 'category_blog.id', '=', 'blog.r_id_category')
            ->latest()
            ->paginate(4);

        // Mengembalikan respons JSON dengan pesan 'List data review' dan data blog dalam format paginasi.
        return response()->json([
            'message' => 'List data review', 'data' => $blogs
        ]);
    }

    public static function paginateall()
    {
        // Mengambil daftar blog secara acak dengan paginasi 4 entri per halaman.
        $blog = Blog::select('blog.id', 'judul', 'category_blog.nama', 'content', 'foto_thumbnail', 'users.name', 'users.photo', 'blog.created_at')
            ->join('users', 'users.id', '=', 'blog.r_id_non_siswa')
            ->join('category_blog', 'category_blog.id', '=', 'blog.r_id_category')
            ->inRandomOrder()
            ->paginate(4);

        // Mengembalikan respons JSON dengan pesan 'List data review' dan data blog dalam format paginasi.
        return response()->json(['message' => 'List data review', 'data' => $blog]);
    }

    public static function allblog()
    {
        // Mengambil semua blog dengan seleksi kolom tertentu, termasuk informasi terkait pengguna dan kategori blog
        $blogs = Blog::select('blog.id', 'judul', 'category_blog.nama', 'content', 'foto_thumbnail', 'users.name', 'users.photo', 'blog.created_at')
            ->join('users', 'users.id', '=', 'blog.r_id_non_siswa') // Menghubungkan blog dengan pengguna non-siswa
            ->join('category_blog', 'category_blog.id', '=', 'blog.r_id_category') // Menghubungkan blog dengan kategori
            ->paginate(10); // Melakukan paginasi dengan 10 entri per halaman

        // Mengembalikan daftar blog dalam format paginasi
        return $blogs;
    }

    public static function getallblog()
    {
        // Menghitung jumlah total blog yang ada
        $blog = Blog::count();

        // Mengembalikan jumlah blog dalam format JSON
        return response()->json($blog);
    }

    public static function getblog($id)
    {
        try {
            // Mengambil detail blog berdasarkan ID yang diberikan
            $blog = Blog::select('blog.id', 'judul', 'category_blog.nama', 'content', 'foto_thumbnail', 'users.name', 'users.photo', 'blog.created_at')
                ->join('users', 'users.id', '=', 'blog.r_id_non_siswa') // Menghubungkan blog dengan pengguna non-siswa
                ->join('category_blog', 'category_blog.id', '=', 'blog.r_id_category') // Menghubungkan blog dengan kategori
                ->findOrFail($id); // Mengambil blog dengan ID yang cocok

            // Mengembalikan detail blog
            return $blog;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Jika blog tidak ditemukan, lemparkan pengecualian dengan pesan 'blog tidak ditemukan'
            throw new \Exception('blog tidak ditemukan');
        }
    }

    public static function get_blog($id)
    {
        // Mengambil profil blog berdasarkan ID yang diberikan
        $profile = Blog::findOrFail($id)->value();

        // Jika profil tidak ditemukan, mengembalikan respons JSON dengan pesan 'tidak ada siswa'
        if (!$profile) {
            return response()->json(['message' => 'tidak ada siswa'], 401);
        }

        // Mengembalikan profil blog dalam format JSON
        return response()->json($profile);
    }

    //-------------------- Read Blog --------------------//



    //-------------------- Create Blog --------------------//

    public static function makeblog($validatordata)
    {
        // Membuat entri baru dalam tabel Blog dengan menggunakan data dari $validatordata
        $post = Blog::create([
            'judul' => $validatordata['judul'], // Mengambil judul dari data yang divalidasi sebelumnya
            'r_id_category' => $validatordata['r_id_category'], // Mengambil ID kategori dari data yang divalidasi
            'content' => $validatordata['content'], // Mengambil konten dari data yang divalidasi
            'foto_thumbnail' => $validatordata['foto_thumbnail'], // Mengambil foto thumbnail dari data yang divalidasi
            'r_id_non_siswa' => $validatordata['r_id_non_siswa'] // Mengambil ID pengguna non-siswa dari data yang divalidasi
        ]);

        // Mengembalikan respons JSON yang memberikan informasi bahwa data berhasil ditambahkan beserta data yang baru saja dibuat
        return response()->json(['message' => 'data berhasil ditambahkan', 'data' => $post], 200);
    }

    //-------------------- Create Blog --------------------//



    //-------------------- Update Blog --------------------//

    public static function updateblog($validatordata, $id)
    {
        if ($validatordata) {
            // Melakukan pembaruan pada entri blog dengan ID yang sesuai
            $blogupdate = Blog::where('id', $id)
                ->update([
                    'judul'           => $validatordata['judul'], // Memperbarui judul dengan data yang divalidasi
                    'r_id_category'   => $validatordata['r_id_category'], // Memperbarui ID kategori dengan data yang divalidasi
                    'content'         => $validatordata['content'], // Memperbarui konten dengan data yang divalidasi
                    'foto_thumbnail'  => $validatordata['foto_thumbnail'], // Memperbarui foto thumbnail dengan data yang divalidasi
                    'r_id_non_siswa'  => $validatordata['r_id_non_siswa'] // Memperbarui ID pengguna non-siswa dengan data yang divalidasi
                ]);
        }

        // Mengembalikan respons JSON yang menyatakan bahwa data telah berhasil diubah beserta detail data yang telah diperbarui
        return response()->json([
            'message' => 'data berhasil diubah', 'data' => $blogupdate
        ], 200);
    }

    //-------------------- Update Blog --------------------//



    //-------------------- Delete Blog --------------------//

    public static function deleteblog($post)
    {
        // Menghapus foto thumbnail terkait dari penyimpanan
        Storage::delete('public/profile/' . $post->foto_thumbnail);

        // Menghapus entri blog dari basis data
        if ($post->delete()) {
            // Jika penghapusan entri berhasil, mengembalikan respons dengan pesan 'Berhasil Menghapus Data'
            return response([
                'Berhasil Menghapus Data'
            ]);
        } else {
            // Jika penghapusan entri gagal, mengembalikan respons dengan pesan 'Tidak Berhasil Menghapus Data'
            return response([
                'Tidak Berhasil Menghapus Data'
            ]);
        }

        // Respons ini tidak akan pernah tercapai karena blok kode di atasnya sudah mengembalikan respons sebelumnya
        return response()->json(['message' => 'data berhasil dihapus'], 200);
    }

    //-------------------- Delete Blog --------------------//
}
