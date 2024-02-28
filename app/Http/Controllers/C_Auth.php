<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class C_Auth extends Controller
{
    //-------------------- Login --------------------//

    public function login(Request $request)
    {
        try {
            // Validasi data masukan: email dan password
            $validator = Validator::make($request->all(), [
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            // Jika validasi gagal, kembalikan respons JSON dengan pesan error 422
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Memanggil fungsi login dari AuthHelper untuk proses otentikasi pengguna
            $login = AuthHelper::login($request);

            // Jika proses login gagal, kembalikan respons JSON dengan pesan error 422
            if (!$login['status']) {
                return response()->json($login['data'], 422);
            }

            // Jika proses login berhasil, kembalikan respons JSON dengan data pengguna yang telah diotentikasi
            return response()->json($login['data']);
        } catch (\Exception $e) {
            // Tangkap kesalahan jika terjadi selama proses login dan kembalikan respons JSON dengan informasi kesalahan
            return response()->json([
                'response_code' =>  404,
                'message'       =>   $e,
            ]);
        }
    }

    public function loginadmin(Request $request)
    {
        try {
            // Validasi data masukan: email dan password
            $validator = Validator::make($request->all(), [
                'email'    => 'required|email',
                'password' => 'required',
            ]);

            // Jika validasi gagal, kembalikan respons JSON dengan pesan error 422
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Memanggil fungsi login dari AuthHelper untuk proses otentikasi pengguna
            $login = AuthHelper::loginadmin($request);

            // Jika proses login gagal, kembalikan respons JSON dengan pesan error 422
            if (!$login['status']) {
                return response()->json($login['data'], 422);
            }

            // Mendapatkan informasi peran dari data yang dikembalikan oleh helper
            $role = $login['data']['role'];

            // Menentukan tindakan setelah login berdasarkan peran pengguna
            switch ($role) {
                case 'superadmin':
                    // Lakukan sesuatu untuk superadmin
                    return response()->json([
                        'message' => 'Halo, Superadmin! Selamat datang.',
                        'data' => $login['data']
                    ]);
                    break;
                case 'admin':
                    // Lakukan sesuatu untuk admin
                    return response()->json([
                        'message' => 'Halo, Admin! Selamat datang.',
                        'data' => $login['data']
                    ]);
                    break;
                case 'mentor':
                    // Lakukan sesuatu untuk mentor
                    return response()->json([
                        'message' => 'Halo, Mentor! Selamat datang.',
                        'data' => $login['data']
                    ]);
                    break;
            }
        } catch (\Exception $e) {
            // Tangkap kesalahan jika terjadi selama proses login dan kembalikan respons JSON dengan informasi kesalahan

            // Peran tidak valid
            return response()->json([
                'message' => 'Peran tidak valid.'
            ], 403);
        }
    }


    //-------------------- Login --------------------//



    //-------------------- Register --------------------//

    public function register(Request $request)
    {
        // Validasi input yang diterima dari request
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required',
            'photo'    => 'required|image'
        ]);

        // Jika validasi gagal, kembalikan respons JSON dengan pesan error 422 yang berisi detail kesalahan validasi
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Memperoleh data yang telah lolos validasi
        $validatordata = $validator->validated();

        // Menyiapkan nama unik untuk file foto
        $imageName = time() . '.' . $validatordata['photo']->extension();

        // Memindahkan file foto ke direktori public/profile dengan nama unik
        $request->photo->move(public_path('profile'), $imageName);

        // Menyimpan nama file foto ke dalam data yang akan disimpan
        $validatordata['photo'] = $imageName;

        // Memanggil fungsi register dari AuthHelper untuk proses registrasi pengguna
        $user = AuthHelper::register($validatordata);

        // Jika registrasi berhasil, kembalikan respons JSON yang berisi informasi pengguna yang terdaftar
        if ($user) {
            return response()->json([
                'success' => true,
                'user'   => $user,
            ], 201);
        }

        // Jika registrasi gagal, kembalikan respons JSON dengan status gagal
        return response()->json([
            'success' => false,
        ]);
    }

    //-------------------- Register --------------------//



    //-------------------- Forgot Password --------------------//

    public function forgetPassword(Request $request)
    {
        // Melakukan validasi terhadap input email yang diterima dari request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        // Jika validasi gagal, kembalikan respons JSON yang berisi pesan error 422 dengan detail kesalahan validasi
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Mengambil nilai dari input email pada request
        $email = $request->input('email');

        // Memulai proses forget password dengan memanggil fungsi forgetPassword dari AuthHelper
        $resetLink = AuthHelper::forgetPassword($request, $email);

        // Mengembalikan respons JSON dengan resetLink yang berisi informasi yang diperlukan untuk proses reset password
        return response()->json($resetLink);
    }

    //-------------------- Forgot Password --------------------//



    //-------------------- Reset Password --------------------//

    public function resetPassword(Request $request, $token)
    {
        // Melakukan validasi terhadap input password yang diterima dari request
        $request->validate([
            'password' => 'required',
        ]);

        // Mengambil nilai dari input password pada request
        $password = $request->input('password');

        // Memanggil fungsi resetPassword dari AuthHelper dengan memberikan token dan password yang baru
        $response = AuthHelper::resetPassword($request, $token, $password);

        // Mengembalikan respons dari fungsi resetPassword yang berisi hasil dari proses reset password
        return $response;
    }

    //-------------------- Reset Password --------------------//
}
