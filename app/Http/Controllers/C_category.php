<?php

namespace App\Http\Controllers;

use App\Helper\CategoryHelper;
use App\Models\category;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class C_category extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatedData = $validator->validated();

        $category = CategoryHelper::create($validatedData);

        return response()->json(['message' => 'Data berhasil ditambahkan', 'data' => $category], 200);
    }

    public function update(Request $request, $id)
    {
        try {
            $category = category::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json("Tidak dapat menemukan category", 422);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $response = CategoryHelper::update($category, $request->all());
        return $response;
    }

    public function destroy($id)
    {
        try {
            $category = category::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json("Tidak dapat menemukan category", 422);
        }
        return CategoryHelper::destroy($category);
    }
}
