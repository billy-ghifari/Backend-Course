<?php

namespace App\Helper;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Adminhelper
{
    public static function allsiswa()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'tidak ada siswa'], 401);
        }

        $siswa = User::where('role', 'siswa')->get();
        return response()->json($siswa);
    }

    public static function activation($siswa)
    {
        $siswa->update([
            'status' => 'aktif'
        ]);

        return response()->json(['message' => 'akun sudah aktif', 'data' => $siswa], 200);
    }

    public static function nonactivation($siswa)
    {

        $siswa->update([
            'status' => 'non'
        ]);

        return response()->json(['message' => 'akun sudah dinonaktifkan', 'data' => $siswa], 200);
    }

    public static function makeadmin($validatordata)
    {

        $user = User::create([
            'name'     => $validatordata['name'],
            'email'    => $validatordata['email'],
            'password' => bcrypt($validatordata['password']),
            'photo'    => $validatordata['photo'],
            'role'     => 'admin',
            'status'   => 'aktif'
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

    public static function editadmin($admin, $validatordata)
    {

        if ($validatordata) {
            $admin->update([
                'name' => $validatordata['name'],
                'email' => $validatordata['email'],
            ]);
        }


        if (!$admin) {
            return response()->json(['message' => 'User not found or not an admin'], 404);
        }

        return response()->json(['message' => 'User updated successfully', 'user' => $admin]);
    }

    public static function deleteadmin($admin)
    {
        Storage::delete('public/profile/' . $admin->photo);
        if ($admin->delete()) {
            return response([
                'Berhasil Menghapus Data'
            ]);
        } else {
            //response jika gagal menghapus
            return response([
                'Tidak Berhasil Menghapus Data'
            ]);
        }

        return response()->json(['message' => 'data berhasil dihapus'], 200);
    }

    public static function makementor($validatordata)
    {

        $user = User::create([
            'name'     => $validatordata['name'],
            'email'    => $validatordata['email'],
            'password' => bcrypt($validatordata['password']),
            'photo'    => $validatordata['photo'],
            'role'     => 'mentor',
            'status'   => 'aktif'
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

    public static function editmentor($mentor, $validatordata)
    {

        if ($validatordata) {
            $mentor->update([
                'name' => $validatordata['name'],
                'email' => $validatordata['email'],
            ]);
        }


        if (!$mentor) {
            return response()->json(['message' => 'User not found or not an admin'], 404);
        }

        return response()->json(['message' => 'User updated successfully', 'user' => $mentor]);
    }

    public static function deletementor($mentor)
    {

        Storage::delete('public/profile/' . $mentor->photo);
        if ($mentor->delete()) {
            return response([
                'Berhasil Menghapus Data'
            ]);
        } else {
            //response jika gagal menghapus
            return response([
                'Tidak Berhasil Menghapus Data'
            ]);
        }

        return response()->json(['message' => 'data berhasil dihapus'], 200);
    }
}
