<?php

namespace App\Helper;

use App\Models\category;
use Illuminate\Support\Facades\Validator;

class CategoryHelper
{
    public static function create($validatedData)
    {
        $category = Category::create([
            'nama' => $validatedData['nama']
        ]);

        return $category;
    }

    public static function update(Category $category, $requestData)
    {
        $category->update([
            'nama' => $requestData['nama']
        ]);

        return response()->json(['message' => 'data berhasil diubah', 'data' => $category], 200);
    }

    public static function destroy($category)
    {
        try {
            $category->delete();
            return response()->json(['message' => 'data berhasil dihapus'], 200);
        } catch (\Exception $ex) {
            return response()->json($ex, 422);
        }
    }
}
