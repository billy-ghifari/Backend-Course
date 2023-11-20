<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
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
    //login

    //register
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
    //register

    //forgot password
    public function forgetPassword(Request $request)
    {

        try {


            $mail = new PHPMailer(true);

            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'mail.dinta.co.id';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'dtc@dinta.co.id';                     //SMTP username
            $mail->Password   = '@dtcdinta800';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            $validator = Validator::make($request->all(), [
                'email'     => 'required',
            ]);

            $validatordata = $validator->validated();

            $token = Str::random(60);

            $resetLinkpass = "http://127.0.0.1:1234/api/reset-password/$token";

            //Recipients
            $mail->setFrom('dtc@dinta.co.id', 'Dinta Training Camp');
            $mail->addAddress($validatordata['email']);     //Add a recipient

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
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

    public function resetPassword(Request $request, $token)
    {
        $request->validate([
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
            return redirect()->route('reset-password'); // Ubah 'reset_password_success' sesuai dengan nama rute atau URL yang sesuai
        }
        return response()->json(['error' => __($status)]);
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
