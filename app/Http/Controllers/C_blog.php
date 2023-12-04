<?php

namespace App\Http\Controllers;

use App\Helper\Bloghelper;
use App\Models\blog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class C_blog extends Controller
{
    //-------------------- Read Blog --------------------//

    public function index()
    {
        // Memanggil fungsi pagination dari Bloghelper untuk mengambil data blog dengan paginasi
        $pagination = Bloghelper::pagination();

        // Mengembalikan data yang telah dipaginasi
        return $pagination;
    }

    //-------------------- Read Blog --------------------//



    //-------------------- Create Blog --------------------//

    public function post_blog(Request $request)
    {
        try {
            // Validasi input menggunakan Validator
            $validator = Validator::make($request->all(), [
                'judul' => 'required',
                'r_id_category' => 'required',
                'content' => 'required',
                'foto_thumbnail' => 'required|image', // Memastikan foto_thumbnail adalah file gambar
                'r_id_non_siswa' => 'required'
            ]);

            // Jika validasi gagal, kembalikan pesan error 422
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Mendapatkan data yang telah divalidasi
            $validatordata = $validator->validated();

            // Mengelola gambar foto_thumbnail: memindahkan gambar ke lokasi yang ditentukan
            $imageName = time() . '.' . $validatordata['foto_thumbnail']->extension();
            $request->foto_thumbnail->move(public_path('blog'), $imageName);
            $validatordata['foto_thumbnail'] = $imageName;

            // Memanggil fungsi makeblog dari Bloghelper untuk membuat blog baru
            $makeblog = Bloghelper::makeblog($validatordata);

            // Mengembalikan respons dari fungsi makeblog
            return $makeblog;
        } catch (\Exception $e) {
            // Tangani jika terjadi error dalam proses pembuatan blog
            return response()->json([
                'response_code' =>  404,
                'message'       =>   $e,
            ]);
        }
    }

    //-------------------- Create Blog --------------------//



    //-------------------- Update Blog --------------------//

    public function update(Request $request, blog $blog, $id)
    {
        try {
            // Mencari blog dengan ID yang diberikan
            $blog = blog::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            // Menangani jika tidak dapat menemukan blog dengan ID yang diberikan
            return response()->json("Tidak dapat menemukan blog", 422);
        }

        // Melakukan validasi terhadap input yang diberikan
        $validator = Validator::make($request->all(), [
            'judul' => 'required',
            'r_id_category' => 'required',
            'content' => 'required',
        ]);

        // Jika validasi gagal, kembalikan pesan error 422
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Mendapatkan data yang telah divalidasi
        $validatordata = $validator->validated();

        // Memanggil fungsi updateblog dari Bloghelper untuk memperbarui blog
        $updateblog = Bloghelper::updateblog($validatordata, $blog);

        return $updateblog;
    }

    //-------------------- Update Blog --------------------//



    //-------------------- Delete Blog --------------------//

    public function destroy($id)
    {
        try {
            // Mencari blog dengan ID yang diberikan
            try {
                $blog = blog::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                // Menangani jika tidak dapat menemukan blog dengan ID yang diberikan
                return response()->json("Tidak dapat menemukan blog", 422);
            }

            // Memanggil fungsi deleteblog dari Bloghelper untuk menghapus blog
            $deleteblog = Bloghelper::deleteblog($blog);

            return $deleteblog;
        } catch (Throwable $ex) {
            // Menangani kesalahan jika terjadi error selama penghapusan
            return response()->json(['message' => 'tidak dapat menghapus blog']);
        }
    }

    //-------------------- Delete Blog --------------------//
}
