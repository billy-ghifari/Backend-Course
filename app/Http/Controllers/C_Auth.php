<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class C_Auth extends Controller
{
    public function login(Request $request)
    {
        $login = Auth::attempt($request->all());
        try {
            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();
                $token = $request->user()->createToken('myAppToken')->plainTextToken;

                return response()->json([
                    'response_code' =>   200,
                    'message'       =>  'login berhasil',
                    'content'       =>  $user,
                    'token'         =>  Crypt::encrypt($token)
                ]);
            } else {
                throw new \Exception('invalid credentials');
            }
        } catch (\Exception $e) {
            return response()->json([
                'response_code' =>  404,
                'message'       =>  'gagal',
            ]);
        }
    }

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

        $imageName = time() . '.' . $validatordata['photo']->extension();
        $request->photo->move(public_path('profile'), $imageName);
        $validatordata['photo'] = $imageName;


        $user = User::create([
            'name'     => $validatordata['name'],
            'email'    => $validatordata['email'],
            'password' => bcrypt($validatordata['password']),
            'photo'    => $validatordata['photo']
        ]);

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

    public function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);


        $status = Password::sendResetLink($request->only('email'));
        if ($status == Password::RESET_LINK_SENT) {
            return response()->json(['status' => __($status)]);
        }
        return response()->json(['error' => __($status)]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed'
        ]);

        $status = Password::reset(
            $request->only('email', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->save();
            }
        );
        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['status' => __($status)]);
        }
        return response()->json(['error' => __($status)]);
    }

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
}
