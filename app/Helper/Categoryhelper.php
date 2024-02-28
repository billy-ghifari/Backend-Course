<?php

namespace App\Helper;

use App\Models\category;

class CategoryHelper
{
    //-------------------- Create Category --------------------//

    public static function create($validatedData)
    {
        // Membuat entri baru dalam tabel Category dengan menggunakan data yang telah divalidasi
        $category = Category::create([
            'nama_category' => $validatedData['nama_category'] // Mengambil nilai 'nama_category' dari data yang divalidasi
        ]);

        // Mengembalikan entri Category yang baru saja dibuat
        return $category;
    }

    //-------------------- Create Category --------------------//



    //-------------------- Read Category --------------------//

    public static function getallcategory()
    {
        // Mengambil semua entri kategori dari tabel category
        $categories = Category::all();

        // Mengembalikan respons dalam format JSON yang berisi semua entri kategori
        return response()->json($categories);
    }

    //-------------------- Read Category --------------------//



    //-------------------- Update Category --------------------//

    public static function update(Category $category, $requestData)
    {
        // Memperbarui entri kategori dengan data yang diberikan
        $category->update([
            'nama_category' => $requestData['nama_category'] // Memperbarui nilai 'nama_category' dengan nilai yang diberikan dari $requestData
        ]);

        // Mengembalikan respons JSON yang menyatakan bahwa data berhasil diubah bersama dengan detail data kategori yang telah diperbarui
        return response()->json(['message' => 'data berhasil diubah', 'data' => $category], 200);
    }

    //-------------------- Update Category --------------------//



    //-------------------- Delete Category --------------------//

    public static function destroy($category)
    {
        try {
            // Menghapus entri kategori
            $category->delete();

            // Mengembalikan respons JSON yang menyatakan bahwa data berhasil dihapus
            return response()->json(['message' => 'data berhasil dihapus'], 200);
        } catch (\Exception $ex) {
            // Jika terjadi exception saat penghapusan, mengembalikan respons JSON dengan pesan exception dan status kode 422
            return response()->json($ex, 422);
        }
    }

    //-------------------- Delete Category --------------------//
}
