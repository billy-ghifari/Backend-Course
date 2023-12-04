<?php

namespace App\Http\Controllers;

use App\Helper\ReviewHelper;
use App\Models\review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class C_Review extends Controller
{
    public function post_review(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'r_id_siswa'  => 'required',
            'review'      => 'required',
            'r_id_kelas'  => 'required',
            'rating'      => 'required|numeric|min:1|max:5'
        ]);

        // Memeriksa apakah terdapat kesalahan validasi
        if ($validator->fails()) {
            // Jika terdapat kesalahan validasi, mengembalikan respons JSON dengan status kode 422 (Unprocessable Entity) yang berisi pesan error validasi
            return response()->json($validator->errors(), 422);
        }

        // Jika validasi berhasil, mendapatkan data yang telah divalidasi
        $validatedData = $validator->validated();

        // Memanggil helper function untuk membuat review
        return ReviewHelper::review($validatedData);
    }

    public function destroy($id)
    {
        try {
            // Melakukan pencarian review berdasarkan ID yang diberikan
            $review = review::findOrFail($id);

            //Jika pencarian berhasil dan review ditemukan, baris ini memanggil fungsi
            return ReviewHelper::deleteReview($review);
        } catch (\Throwable $ex) {
            //Jika terjadi pengecualian, baris ini akan mengembalikan respons dalam bentuk JSON dengan kode status 422
            return response()->json($ex, 422);
        }
    }
}
