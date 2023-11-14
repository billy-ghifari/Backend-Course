<?php

namespace App\Http\Controllers;

use App\Models\category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class C_category extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required'
        ]);

        $validatordata = $validator->validated();

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category = category::create([
            'nama' => $validatordata['nama']
        ]);

        return response()->json(['message' => 'data berhasil ditambahkan', 'data' => $category], 200);
    }

    public function update(Request $request, category $categories, $id)
    {
        $category = category::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama' => 'required'
        ]);

        $validatordata = $validator->validated();

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->all()) {
            $category->update([
                'nama' => $validatordata['nama']
            ]);
        }

        return response()->json(['message' => 'data berhasil diubah', 'data' => $category], 200);
    }

    public function destroy(category $category, $id)
    {
        try {
            $deleted = category::findOrFail($id);
            //delete post
            $deleted->delete();
            //return response
            return response()->json(['message' => 'data berhasil dihapus'], 200);
        } catch (Exception $ex) {
            // Alert::warning('Error', 'Cant deleted, Barang already used !');
            return response()->json($ex, 422);
        }
    }
}
