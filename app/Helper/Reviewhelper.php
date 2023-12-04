<?php

namespace App\Helper;

use App\Models\review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewHelper
{

    //-------------------- Create Review --------------------//

    public static function review($validatedData)
    {
        // Membuat entri baru dalam tabel review berdasarkan data yang telah divalidasi
        $post = Review::create([
            'r_id_siswa'  => $validatedData['r_id_siswa'],
            'review'      => $validatedData['review'],
            'r_id_kelas'  => $validatedData['r_id_kelas'],
            'rating'      => $validatedData['rating']
        ]);

        // Mengembalikan respons JSON yang menyatakan bahwa data review berhasil ditambahkan, serta data review yang baru saja ditambahkan ke database
        return response()->json(['message' => 'data review berhasil ditambahkan', 'data' => $post], 200);
    }

    //-------------------- Create Review --------------------//



    //-------------------- Delete Review --------------------//

    public static function deleteReview(Review $review)
    {
        try {
            //Proses menghapus data yang direpresentasikan oleh variabel $review
            $review->delete();

            //Mengembalikan respons dalam bentuk JSON dengan pesan 'Review Berhasil Dihapus!' dan status kode 200 jika penghapusan berhasil dilakukan
            return response()->json(['message' => 'Review Berhasil Dihapus!'], 200);
        } catch (\Throwable $ex) {
            //Respons yang dikirim jika terjadi kesalahan. Ini akan mengembalikan informasi kesalahan dalam bentuk JSON
            return response()->json($ex, 200);
        }
    }

    //-------------------- Delete Review --------------------//
}
