<?php

namespace App\Helpers\Authhelper;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use PHPMailer\PHPMailer\Exception;

class AuthHelper
{
    public static function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $request->user()->createToken('myAppToken')->plainTextToken;

            return [
                'status'      => true,
                'data'        => [
                    'status'  => true,
                    'user'    => $user,
                    'token'   => Crypt::encrypt($token)
                ]
            ];
        } else {
            return [
                'status'      => false,
                'data'        => [
                    'status'  => false,
                    'message' => "Email atau password anda salah"
                ],
            ];
        }
    }

    public static function register(Request $request)
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

    public static function forgetPassword(Request $request)
    {
        try {
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = 'mail.dinta.co.id';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'dtc@dinta.co.id';
            $mail->Password   = '@dtcdinta800';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            $validator = Validator::make($request->all(), [
                'email'     => 'required',
            ]);

            $validatordata = $validator->validated();

            $mail->setFrom('dtc@dinta.co.id', 'Dinta Training Camp');
            $mail->addAddress($validatordata['email']);

            $checkemail = DB::table('password_reset_tokens')
                ->where('email', $validatordata['email'])
                ->first();

            if ($checkemail) {
                DB::table('password_reset_tokens')
                    ->where('email', $validatordata['email'])
                    ->delete();
            }

            $token = Str::random(60);

            $resetLinkpass = "http://127.0.0.1:1234/api/reset-password/$token";

            DB::table('password_reset_tokens')->insert([
                'email' => $validatordata['email'],
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);

            $mail->isHTML(true);
            $mail->Subject = 'halo';
            $mail->Body = "Hello,\n\n";
            $mail->Body .= "To reset your password, please click on the following link:\n";
            $mail->Body .= '
    <div>
        <a href="$resetLinkpass">Reset Link</a>
    </div>';

            $mail->send();
            return response()->json($resetLinkpass);
        } catch (Exception $e) {
            return response()->json("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    public static function resetPassword(Request $request, $token)
    {
        $request->validate([
            'password' => 'required',
        ]);
        $passwordReset = DB::table('password_reset_tokens')->where('token', $token)->first();
        if (!$passwordReset) {
            return response()->json(['message' => 'Invalid token'], 404);
        }
        $user = User::where('email', $passwordReset->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        DB::table('password_reset_tokens')->where('email', $passwordReset->email)->delete();
        return response()->json(['message' => 'Password has been reset successfully']);
    }
}
