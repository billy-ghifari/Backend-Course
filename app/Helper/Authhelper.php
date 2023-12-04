<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use PHPMailer\PHPMailer\Exception;

class AuthHelper
{
    //-------------------- Login --------------------//

    public static function login(Request $request)
    {
        // Melakukan percobaan otentikasi dengan menggunakan email dan password yang diterima dari request
        if (Auth::attempt($request->only('email', 'password'))) {
            // Jika otentikasi berhasil, mengambil informasi pengguna yang terotentikasi
            $user = Auth::user();

            // Membuat token untuk otentikasi menggunakan Sanctum dan mendapatkan token dalam bentuk plaintext
            $token = $request->user()->createToken('myAppToken')->plainTextToken;

            // Mengembalikan respons JSON yang berisi status login berhasil, informasi pengguna, dan token yang dienkripsi
            return [
                'status' => true,
                'data' => [
                    'status' => true,
                    'user' => $user,
                    'token' => Crypt::encrypt($token)
                ]
            ];
        } else {
            // Jika otentikasi gagal, mengembalikan respons JSON yang berisi status login gagal dan pesan kesalahan
            return [
                'status' => false,
                'data' => [
                    'status' => false,
                    'message' => "Email atau password anda salah"
                ],
            ];
        }
    }

    //-------------------- Login --------------------//



    //-------------------- Register --------------------//

    public static function register($validatordata)
    {
        // Membuat entri baru dalam tabel User dengan menggunakan data yang telah divalidasi
        $user = User::create([
            'name'     => $validatordata['name'],
            'email'    => $validatordata['email'],
            'password' => bcrypt($validatordata['password']),
            'photo'    => $validatordata['photo']
        ]);

        // Memeriksa apakah proses pembuatan pengguna baru berhasil
        if ($user) {
            // Jika berhasil, mengembalikan respons JSON dengan status sukses dan data pengguna yang baru dibuat, serta status 201 (Created)
            return response()->json([
                'success' => true,
                'user'   => $user,
            ], 201);
        }

        // Jika gagal membuat pengguna baru, mengembalikan respons JSON dengan status gagal
        return response()->json([
            'success' => false,
        ]);
    }

    //-------------------- Register --------------------//



    //-------------------- Forgot Password --------------------//

    public static function forgetPassword(Request $request)
    {
        try {
            // Konfigurasi PHPMailer untuk pengiriman email
            $mail = new PHPMailer(true);
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = 'mail.dinta.co.id';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'dtc@dinta.co.id';
            $mail->Password   = '@dtcdinta800';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

            // Validasi input email dari request
            $validator = Validator::make($request->all(), [
                'email' => 'required',
            ]);

            // Mengambil data yang telah divalidasi
            $validatordata = $validator->validated();

            // Pengaturan pengirim email dan penerima email
            $mail->setFrom('dtc@dinta.co.id', 'Dinta Training Camp');
            $mail->addAddress($validatordata['email']);

            // Memeriksa apakah ada email yang sudah meminta reset sebelumnya
            $checkemail = DB::table('password_reset_tokens')
                ->where('email', $validatordata['email'])
                ->first();

            // Jika sudah ada, hapus token reset sebelumnya
            if ($checkemail) {
                DB::table('password_reset_tokens')
                    ->where('email', $validatordata['email'])
                    ->delete();
            }

            // Membuat token acak untuk reset password
            $token = Str::random(60);

            // URL untuk reset password
            $resetLinkpass = "http://127.0.0.1:1234/api/reset-password/$token";

            // Menyimpan token reset password ke dalam database
            DB::table('password_reset_tokens')->insert([
                'email' => $validatordata['email'],
                'token' => $token,
                'created_at' => Carbon::now()
            ]);

            // Konfigurasi email yang akan dikirim
            $mail->isHTML(true);
            $mail->Subject = 'halo';
            $mail->Body = "Hello,\n\n";
            $mail->Body .= "To reset your password, please click on the following link:\n";
            $mail->Body .= '
    <div>
        <a href="$resetLinkpass">Reset Link</a>
    </div>';

            // Mengirim email
            $mail->send();

            // Mengembalikan respons berisi link reset password
            return response()->json($resetLinkpass);
        } catch (Exception $e) {
            // Menangani jika ada kesalahan dalam pengiriman email
            return response()->json("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    //-------------------- Forgot Password --------------------//



    //-------------------- Reset Password --------------------//

    public static function resetPassword(Request $request, $token)
    {
        // Validasi request untuk memastikan bahwa password yang baru diatur tidak kosong
        $request->validate([
            'password' => 'required',
        ]);

        // Mengambil informasi token reset password dari database berdasarkan token yang diberikan
        $passwordReset = DB::table('password_reset_tokens')->where('token', $token)->first();

        // Memeriksa apakah token reset password ada dalam database, jika tidak ada, kembalikan respons dengan pesan kesalahan 404 (Not Found)
        if (!$passwordReset) {
            return response()->json(['message' => 'Invalid token'], 404);
        }

        // Mengambil pengguna berdasarkan alamat email yang terkait dengan token reset password
        $user = User::where('email', $passwordReset->email)->first();

        // Jika pengguna tidak ditemukan berdasarkan alamat email, kembalikan respons dengan pesan kesalahan 404 (Not Found)
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Mengubah password pengguna dengan password baru yang telah di-hash menggunakan bcrypt
        $user->password = Hash::make($request->password);

        // Menyimpan perubahan password ke dalam database
        $user->save();

        // Menghapus token reset password dari database setelah password diubah
        DB::table('password_reset_tokens')->where('email', $passwordReset->email)->delete();

        // Mengembalikan respons JSON yang menyatakan bahwa password telah diubah dengan sukses
        return response()->json(['message' => 'Password has been reset successfully']);
    }

    //-------------------- Reset Password --------------------//
}
