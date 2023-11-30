<?php

namespace App\Helpers\Authhelper;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
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

    public static function register($validatordata)
    {
        $imageName = time() . '.' . $validatordata['photo']->extension();
        $validatordata['photo']->move(public_path('profile'), $imageName);
        $validatordata['photo'] = $imageName;

        $user = User::create([
            'name'     => $validatordata['name'],
            'email'    => $validatordata['email'],
            'password' => bcrypt($validatordata['password']),
            'photo'    => $validatordata['photo']
        ]);

        return $user;
    }

    public static function forgetPassword($email)
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

            $mail->setFrom('dtc@dinta.co.id', 'Dinta Training Camp');
            $mail->addAddress($email);

            $user = Auth::user();

            $checkEmail = DB::table('password_reset_tokens')
                ->where('email', $email)
                ->first();

            if ($checkEmail) {
                DB::table('password_reset_tokens')
                    ->where('email', $email)
                    ->delete();
            }

            $token = Str::random(60);
            $resetLinkpass = "http://127.0.0.1:1234/api/reset-password/$token";

            DB::table('password_reset_tokens')->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);

            $mail->isHTML(true);
            $mail->Subject = 'halo';
            $mail->Body = "Hello,\n\n";
            $mail->Body .= "To reset your password, please click on the following link:\n";
            $mail->Body .= '<div><a href="' . $resetLinkpass . '">Reset Link</a></div>';

            $mail->send();
            return $resetLinkpass;
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public static function resetPassword($token, $password)
    {
        $passwordReset = DB::table('password_reset_tokens')->where('token', $token)->first();
        if (!$passwordReset) {
            return response()->json(['message' => 'Invalid token'], 404);
        }

        $user = User::where('email', $passwordReset->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->password = Hash::make($password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $passwordReset->email)->delete();

        return response()->json(['message' => 'Password has been reset successfully']);
    }
}
