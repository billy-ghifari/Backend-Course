<?php

namespace App\Http\Controllers;

use App\Helper\CategoryBlogHelper;
use App\Models\category;
use App\Models\categoryblog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class C_categoryblog extends Controller
{

    //-------------------- Read Kelas --------------------//

    public function index()
    {
        // Mengambil data kelas dengan paginasi menggunakan Kelashelper::paginate()
        $pagination = CategoryBlogHelper::paginate();

        // Mengembalikan data paginasi sebagai respons
        return $pagination;
    }

    public function getallcatblog()
    {
        try {
            $blogs = CategoryBlogHelper::getallcatblog(); // Panggil helper untuk mendapatkan semua data blog

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

    //-------------------- Read Kelas --------------------//



    //-------------------- Create Category --------------------//

    public function create(Request $request)
    {
        // Validasi data yang diterima dari request
        $validator = Validator::make($request->all(), [
            'nama'  => 'required',
            'photo' => 'required',
        ]);

        // Jika validasi gagal, kembalikan pesan error
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Ambil data yang telah divalidasi
        $validatordata = $validator->validated();

        // Pindahkan foto thumbnail ke lokasi yang ditentukan dan ubah nama file
        $imageName = time() . '.' . $validatordata['photo']->extension();
        $request->photo->move(
            public_path('categoryblog'),
            $imageName
        );
        $validatordata['photo'] = $imageName;

        // Buat kelas baru menggunakan Kelashelper::makekelas()
        $makecategoryblog = CategoryBlogHelper::create($validatordata);

        // Kembalikan hasil operasi membuat kelas
        return $makecategoryblog;
    }

    //-------------------- Delete Category --------------------//

    public function destroy($id)
    {
        try {
            // Mencari kategori berdasarkan ID
            $category = categoryblog::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            // Jika kategori tidak ditemukan, kembalikan respons dengan pesan error
            return response()->json("Tidak dapat menemukan category", 422);
        }

        // Memanggil fungsi CategoryHelper::destroy() untuk menghapus kategori
        return CategoryBlogHelper::destroy($category);
    }

    //-------------------- Delete Category --------------------//
}
