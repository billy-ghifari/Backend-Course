<?php

namespace App\Helper;

use App\Models\review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewHelper
{
    public static function review(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'r_id_siswa'  => 'required',
            'review'     => 'required',
            'r_id_kelas' => 'required',
            'rating'     => 'required|numeric|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatorreview = $validator->validated();
        $post = review::create([
            'r_id_siswa'  => $validatorreview['r_id_siswa'],
            'review'     => $validatorreview['review'],
            'r_id_kelas' => $validatorreview['r_id_kelas'],
            'rating'     => $validatorreview['rating']
        ]);
        return response()->json(['message' => 'data review berhasil ditambahkan', 'data' => $post], 200);
    }

    public static function deleteReview($id)
    {
        try {
            $deleted = Review::findOrFail($id);
            $deleted->delete();
            return response()->json(['message' => 'Review Berhasil Dihapus!'], 200);
        } catch (\Throwable $ex) {
            return response()->json($ex, 200);
        }
    }
}
