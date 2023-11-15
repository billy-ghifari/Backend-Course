<?php

namespace App\Http\Controllers;

use App\Models\review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class C_Review extends Controller
{
    public function post_review(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'r_id_user'  => 'required',
            'review'     => 'required',
            'r_id_kelas' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatorreview = $validator->validated();
        $post = review::create([
            'r_id_user'  => $validatorreview['r_id_user'],
            'review'     => $validatorreview['review'],
            'r_id_kelas' => $validatorreview['r_id_kelas']
        ]);
        return response()->json(['message' => 'data review berhasil ditambahkan', 'data' => $post], 200);
    }
}
