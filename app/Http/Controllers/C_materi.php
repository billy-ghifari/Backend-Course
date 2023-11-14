<?php

namespace App\Http\Controllers;

use App\Models\materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class C_materi extends Controller
{
    public function post_materi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'r_id_kelas' => 'required',
            'judul_materi' => 'required',
            'link_materi' => 'required',
            'deskripsi_materi' => 'required',
            'durasi' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatordata = $validator->validated();
        $post = materi::create([
            'r_id_kelas' => $validatordata['r_id_kelas'],
            'judul_materi' => $validatordata['judul_materi'],
            'link_materi' => $validatordata['link_materi'],
            'deskripsi_materi' => $validatordata['deskripsi_materi'],
            'durasi' => $validatordata['durasi'],
        ]);

        return response()->json(['message' => 'data berhasil ditambahkan', 'data' => $post], 200);
    }

    public function update(Request $request, materi $kelas, $id)
    {
        $post = materi::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'r_id_kelas' => 'required',
            'judul_materi' => 'required',
            'link_materi' => 'required',
            'deskripsi_materi' => 'required',
            'durasi' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatordata = $validator->validated();

        if ($request->all()) {
            $post->update([
                'r_id_kelas' => $validatordata['r_id_kelas'],
                'judul_materi' => $validatordata['judul_materi'],
                'link_materi' => $validatordata['link_materi'],
                'deskripsi_materi' => $validatordata['deskripsi_materi'],
                'durasi' => $validatordata['durasi'],
            ]);
        }

        return response()->json(['message' => 'data berhasil diubah', 'data' => $post], 200);
    }

    public function destroy($id)
    {
        try {
            $kelas = materi::findOrFail($id);

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
