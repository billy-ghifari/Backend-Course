<?php

namespace App\Http\Controllers;

use App\Helper\CategoryHelper;
use App\Models\category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class C_category extends Controller
{
    //-------------------- Create Category --------------------//

    public function create(Request $request)
    {
        // Validasi data yang diterima dari request
        $validator = Validator::make($request->all(), [
            'nama' => 'required'
        ]);

        // Jika validasi gagal, kembalikan respons dengan kode status 422 dan pesan error validasi
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Mendapatkan data yang telah divalidasi
        $validatedData = $validator->validated();

        // Membuat kategori baru dengan menggunakan CategoryHelper
        $category = CategoryHelper::create($validatedData);

        // Mengembalikan respons berhasil dengan pesan sukses dan data kategori yang telah dibuat
        return response()->json([
            'message' => 'Data berhasil ditambahkan', 'data' => $category
        ], 200);
    }

    //-------------------- Create Category --------------------//

    //-------------------- Read Category --------------------//

    public function getallcategory()
    {
        try {
            $blogs = CategoryHelper::getallcategory(); // Panggil helper untuk mendapatkan semua data blog

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

    //-------------------- Read Category --------------------//

    //-------------------- Update Category --------------------//

    public function update(Request $request, $id)
    {
        try {
            // Mencari kategori berdasarkan ID
            $category = category::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            // Jika kategori tidak ditemukan, kembalikan respons dengan pesan error
            return response()->json("Tidak dapat menemukan category", 422);
        }

        // Validasi data yang diterima dari request
        $validator = Validator::make($request->all(), [
            'nama' => 'required'
        ]);

        // Jika validasi gagal, kembalikan respons dengan pesan error validasi
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Memanggil fungsi CategoryHelper::update() untuk mengubah kategori
        $response = CategoryHelper::update($category, $request->all());

        // Mengembalikan respons dari proses update kategori
        return $response;
    }

    //-------------------- Create Category --------------------//



    //-------------------- Delete Category --------------------//

    public function destroy($id)
    {
        try {
            // Mencari kategori berdasarkan ID
            $category = category::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            // Jika kategori tidak ditemukan, kembalikan respons dengan pesan error
            return response()->json("Tidak dapat menemukan category", 422);
        }

        // Memanggil fungsi CategoryHelper::destroy() untuk menghapus kategori
        return CategoryHelper::destroy($category);
    }

    //-------------------- Delete Category --------------------//
}
