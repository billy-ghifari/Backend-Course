<?php

namespace App\Http\Controllers;

use App\Models\kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class C_admin extends Controller
{
    //siswa pengaktifan

    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $siswa = User::where('role', 'siswa')->get();
            return response()->json($siswa);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function activationsiswa($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->update([
                'status' => 'aktif'
            ]);

            return response()->json(['message' => 'akun sudah aktif', 'data' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal aktivasi ' . $e->getMessage()], 500);
        }
    }

    public function nonactivationsiswa($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->update([
                'status' => 'non'
            ]);

            return response()->json(['message' => 'akun sudah dinonaktifkan', 'data' => $user], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal aktivasi ' . $e->getMessage()], 500);
        }
    }
    //siswa pengaktifan

    //kelas pengaktifan
    public function activationkelas($id)
    {
        try {
            $kelas = kelas::findOrFail($id);

            $kelas->update([
                'status' => 'aktif'
            ]);

            return response()->json(['message' => 'akun sudah aktif', 'data' => $kelas], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal aktivasi ' . $e->getMessage()], 500);
        }
    }
    //kelas pengaktifan



}
