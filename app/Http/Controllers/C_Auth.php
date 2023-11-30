<?php

namespace App\Http\Controllers;

use App\Helper\Authhelper\AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class C_Auth extends Controller
{
    //login
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email'    => 'required|email',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $login = AuthHelper::login($request);
            if (!$login['status']) {
                return response()->json($login['data'], 422);
            }
            return response()->json($login['data']);
        } catch (\Exception $e) {
            return response()->json([
                'response_code' =>  404,
                'message'       =>   $e,
            ]);
        }
    }
    //login

    //register
    public function register(Request $request)
    {
        $response = AuthHelper::register($request);
        return $response;
    }
    //register

    //forgot password
    public function forgetPassword(Request $request)
    {
        $response = AuthHelper::forgetPassword($request);
        return $response;
    }

    public function resetPassword(Request $request, $token)
    {
        $response = AuthHelper::resetPassword($request, $token);
        return $response;
    }
    //forgot password

    //logout
    public function logout(Request $request)
    {
        if (Auth::user()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'success' => true,
                'message' => 'Logout Berhasil!',
            ]);
        } else {
            return response()->json(['massage' => 'Unauthorized'], 401);
        }
    }
    //logout
}
