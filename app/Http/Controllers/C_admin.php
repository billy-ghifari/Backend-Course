<?php

namespace App\Http\Controllers;

use App\Helper\Adminhelper;
use App\Models\kelas;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class C_admin extends Controller
{
    //siswa pengaktifan

    public function index()
    {
        try {

            $allsiswa = Adminhelper::allsiswa();

            return $allsiswa;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function activationsiswa($id)
    {
        try {

            try {
                $siswa = user::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return response()->json("Tidak dapat menemukan siswa", 422);
            }

            $activation = Adminhelper::activation($siswa);

            return $activation;
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal aktivasi '], 500);
        }
    }

    public function nonactivationsiswa($id)
    {
        try {

            try {
                $siswa = user::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return response()->json("Tidak dapat menemukan siswa", 422);
            }

            $nonactivation = Adminhelper::nonactivation($siswa);

            return $nonactivation;
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

    //crud admin

    public function registeradmin(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required',
            'photo'    => 'required|image',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatordata = $validator->validated();

        $imageName = time() . '.' . $validatordata['photo']->extension();
        $request->photo->move(public_path('profile'), $imageName);
        $validatordata['photo'] = $imageName;

        $makeadmin = Adminhelper::makeadmin($validatordata);

        return $makeadmin;
    }


    public function updateadmin(Request $request, $id)
    {
        try {
            $admin = User::where('id', $id)
                ->where('role', 'admin')->first();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required',
                'email' => 'sometimes|required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $validatordata = $validator->validated();

            $editadmin = Adminhelper::editadmin($admin, $validatordata);

            return $editadmin;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function destroyadmin($id)
    {
        try {
            try {
                $admin = User::where('id', $id)
                    ->where('role', 'admin')->first();
            } catch (ModelNotFoundException $e) {
                return response()->json("Bukan admin", 422);
            }
            $deleteadmin = Adminhelper::deleteadmin($admin);

            return $deleteadmin;
        } catch (Throwable $ex) {
            // Alert::warning('Error', 'Cant deleted, Barang already used !');
            return response()->json($ex, 422);
        }
    }
    //crud admin

    //crud admin
    public function registermentor(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required',
            'photo'    => 'required|image',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatordata = $validator->validated();

        $imageName = time() . '.' . $validatordata['photo']->extension();
        $request->photo->move(public_path('profile'), $imageName);
        $validatordata['photo'] = $imageName;

        $makementor = Adminhelper::makementor($validatordata);

        return $makementor;
    }


    public function updatementor(Request $request, $id)
    {
        try {
            $mentor = User::where('id', $id)
                ->where('role', 'mentor')->first();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required',
                'email' => 'sometimes|required',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $validatordata = $validator->validated();

            $editmentor = Adminhelper::editadmin($mentor, $validatordata);

            return $editmentor;
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function destroymentor($id)
    {
        try {
            $mentor = User::where('id', $id)
                ->where('role', 'mentor')->first();

            $deletementor = Adminhelper::deleteadmin($mentor);

            return $deletementor;
        } catch (Throwable $ex) {
            // Alert::warning('Error', 'Cant deleted, Barang already used !');
            return response()->json($ex, 422);
        }
    }
    //crud admin
}
