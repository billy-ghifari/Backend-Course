<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Authhelper\AuthHelper;

class C_Auth extends Controller
{
    //-------------------- Login --------------------//

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

    //-------------------- Login --------------------//



    //-------------------- Register --------------------//

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required',
            'photo'    => 'required|image'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatordata = $validator->validated();
        $user = AuthHelper::register($validatordata);

        if ($user) {
            return response()->json([
                'success' => true,
                'user'   => $user,
            ], 201);
        }

        return response()->json([
            'success' => false,
        ]);
    }

    //-------------------- Register --------------------//



    //-------------------- Forgot Password --------------------//

    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $email = $request->input('email');
        $resetLink = AuthHelper::forgetPassword($email);

        return response()->json($resetLink);
    }

    //-------------------- Forgot Password --------------------//



    //-------------------- Reset Password --------------------//

    public function resetPassword(Request $request, $token)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $password = $request->input('password');
        $response = AuthHelper::resetPassword($token, $password);

        return $response;
    }

    //-------------------- Reset Password --------------------//
}
