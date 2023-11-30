<?php

namespace App\Helper;

use App\Models\blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Bloghelper
{
    public static function pagination()
    {
        $blogs = blog::latest()->paginate(5);
        return response()->json(['message' => 'List data review', 'data' => $blogs]);
    }

    public static function makeblog($validatordata)
    {
        $post = blog::create([
            'judul' => $validatordata['judul'],
            'r_id_category' => $validatordata['r_id_category'],
            'content' => $validatordata['content'],
            'foto_thumbnail' => $validatordata['foto_thumbnail'],
            'r_id_non_siswa' => $validatordata['r_id_non_siswa']
        ]);

        return response()->json(['message' => 'data berhasil ditambahkan', 'data' => $post], 200);
    }

    public static function updateblog($validatordata, $post)
    {
        if ($validatordata) {
            $post->update([
                'judul' => $validatordata['judul'],
                'r_id_category' => $validatordata['r_id_category'],
                'content' => $validatordata['content'],
            ]);
        }

        return response()->json(['message' => 'data berhasil diubah', 'data' => $post], 200);
    }

    public static function deleteblog($post)
    {
        Storage::delete('public/profile/' . $post->foto_thumbnail);
        if ($post->delete()) {
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
