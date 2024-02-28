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

    public function getall()
    {
        // Mengambil data kelas dengan paginasi menggunakan Kelashelper::paginateall()
        $paginateall = Bloghelper::paginateall();

        // Mengembalikan data paginasi sebagai respons
        return $paginateall;
    }


    public function allblog()
    {
        try {
            $blogs = Bloghelper::allblog(); // Panggil helper untuk mendapatkan semua data blog

            return response()->json([
                'status' => true,
                'data' => $blogs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch blogs.'
            ], 500);
        }
    }

    public function getallblog()
    {
        try {
            // Memanggil method dari Adminhelper untuk mendapatkan semua siswa
            $allblog = Bloghelper::getallblog();

            return $allblog; // Mengembalikan daftar semua siswa
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getblog($id)
    {
        try {
            $blog = Bloghelper::getblog($id); // Memanggil helper untuk mengambil data kelas berdasarkan ID

            return response()->json(['classData' => $blog], 200);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['message' => 'Kelas tidak ditemukan'], 404);
        } catch (\Exception $ex) {
            return response()->json(['message' => $ex->getMessage()], 422);
        }
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
                'foto_thumbnail' => 'required|image',
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

    public function get_blog($id)
    {
        $blog = Bloghelper::get_blog($id);
        return $blog;
    }

    //-------------------- Create Blog --------------------//



    //-------------------- Update Blog --------------------//

    public function update(Request $request, $id)
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
            'judul'          => 'required',
            'r_id_category'  => 'required',
            'content'        => 'required',
            'foto_thumbnail' => 'required|image',
            'r_id_non_siswa' => 'required'
        ]);

        // Jika validasi gagal, kembalikan pesan error 422
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Mendapatkan data yang telah divalidasi
        $validatordata = $validator->validated();

        // Menyiapkan nama unik untuk file foto
        $imageName = time() . '.' . $validatordata['foto_thumbnail']->extension();

        // Memindahkan file foto ke direktori public/profile dengan nama unik
        $request->file('foto_thumbnail')->move(public_path('blog'), $imageName);

        // Menyimpan nama file foto ke dalam data yang akan disimpan
        $validatordata['foto_thumbnail'] = $imageName;


        // Memanggil fungsi updateblog dari Bloghelper untuk memperbarui blog
        $updateblog = Bloghelper::updateblog($validatordata, $id);

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

    //---------------;----- Delete Blog --------------------//
}
