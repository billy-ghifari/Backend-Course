<?php

namespace App\Http\Controllers;

use App\Models\kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class C_kelas extends Controller
{
    public function index()
    {
        $kelas = kelas::latest()->paginate(2);
        return response()->json(['message' => 'List data review', 'data' => $kelas]);
    }

    public function post_kelas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'deskripsi' => 'required',
            'foto_thumbnail' => 'required',
            'r_id_non_siswa' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatordata = $validator->validated();

        $imageName = time() . '.' . $validatordata['foto_thumbnail']->extension();
        $request->foto_thumbnail->move(public_path('kelas'), $imageName);
        $validatordata['foto_thumbnail'] = $imageName;

        $post = kelas::create([
            'nama' => $validatordata['nama'],
            'deskripsi' => $validatordata['deskripsi'],
            'foto_thumbnail' => $validatordata['foto_thumbnail'],
            'r_id_non_siswa' => $validatordata['r_id_non_siswa'],
        ]);

        return response()->json(['message' => 'data berhasil ditambahkan', 'data' => $post], 200);
    }

    public function update(Request $request, kelas $kelas, $id)
    {
        $post = kelas::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'deskripsi' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatordata = $validator->validated();

        if ($request->all()) {
            $post->update([
                'nama' => $validatordata['nama'],
                'deskripsi' => $validatordata['deskripsi'],
            ]);
        }

        return response()->json(['message' => 'data berhasil diubah', 'data' => $post], 200);
    }

    public function destroy($id)
    {
        try {
            $kelas = kelas::findOrFail($id);

            Storage::delete('public/profile/' . $kelas->foto_thumbnail);
            if ($kelas->delete()) {
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
}
