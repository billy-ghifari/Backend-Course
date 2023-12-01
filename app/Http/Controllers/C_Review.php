<?php

namespace App\Http\Controllers;

use App\Helper\ReviewHelper;
use App\Models\review;
use Illuminate\Http\Request;

class C_Review extends Controller
{
    public function post_review(Request $request)
    {
        $response = ReviewHelper::review($request);
        return $response;
    }

    public function destroy($id)
    {
        $response = ReviewHelper::deleteReview($id);
        return $response;
    }
}
