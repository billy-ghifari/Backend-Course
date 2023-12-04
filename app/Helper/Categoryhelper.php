<?php

namespace App\Helper;

use App\Models\category;

class CategoryHelper
{
    //-------------------- Create Category --------------------//

    public static function create($validatedData)
    {
        // Membuat entri baru dalam tabel Category berdasarkan data yang telah divalidasi
        $category = Category::create([
            'nama' => $validatedData['nama']
        ]);

        // Mengembalikan objek $category yang merupakan instance dari model Category yang baru saja dibuat
        return $category;
    }

    //-------------------- Create Category --------------------//



    //-------------------- Update Category --------------------//

    public static function update(Category $category, $requestData)
    {
        // Memperbarui atribut 'nama' dari kategori ($category) yang telah dipilih
        $category->update([
            'nama' => $requestData['nama']
        ]);

        // Mengembalikan respons JSON yang menyatakan bahwa data berhasil diubah dan mencakup data kategori yang telah diperbarui
        return response()->json(['message' => 'data berhasil diubah', 'data' => $category], 200);
    }

    //-------------------- Update Category --------------------//



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
