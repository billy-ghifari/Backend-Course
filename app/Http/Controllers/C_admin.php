<?php

namespace App\Http\Controllers;

use App\Models\kelas;
use App\Models\User;
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

    public function activationsiswa(Request $request, $id)
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

    public function nonactivationsiswa(Request $request, $id)
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
    public function activationkelas(Request $request, $id)
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
    public function loginadmin(Request $request)
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

            if ($request->all()) {
                $admin->update([
                    'name' => $validatordata['name'],
                    'email' => $validatordata['email'],
                ]);
            }


            if (!$admin) {
                return response()->json(['message' => 'User not found or not an admin'], 404);
            }

            return response()->json(['message' => 'User updated successfully', 'user' => $admin]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function destroyadmin($id)
    {
        try {
            $admin = User::where('id', $id)
                ->where('role', 'admin')->first();

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

            //delete post
            //return response
            // return response()->json(['message' => 'data berhasil dihapus'], 200);
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


    public function updatementor(Request $request, $id)
    {
        try {
            $admin = User::where('id', $id)
                ->where('role', 'mentor')->first();

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required',
                'email' => 'sometimes|required',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $validatordata = $validator->validated();

            if ($request->all()) {
                $admin->update([
                    'name' => $validatordata['name'],
                    'email' => $validatordata['email'],
                ]);
            }


            if (!$admin) {
                return response()->json(['message' => 'User not found or not an admin'], 404);
            }

            return response()->json(['message' => 'User updated successfully', 'user' => $admin]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    public function destroymentor($id)
    {
        try {
            $admin = User::where('id', $id)
                ->where('role', 'mentor')->first();

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

            //delete post
            //return response
            // return response()->json(['message' => 'data berhasil dihapus'], 200);
        } catch (Throwable $ex) {
            // Alert::warning('Error', 'Cant deleted, Barang already used !');
            return response()->json($ex, 422);
        }
    }
    //crud admin
}
