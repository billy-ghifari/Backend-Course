<?php

namespace App\Http\Controllers;

use App\Models\materi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class C_materi extends Controller
{
    //-------------------- Create Materi --------------------//

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

    //--------------------Create Materi --------------------//



    //-------------------- Update Materi --------------------//

    public function update(Request $request, $id)
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

    //-------------------- Update Materi --------------------//



    //-------------------- Delete Materi --------------------//

    public function destroy($id)
    {
        try {
            $kelas = materi::findOrFail($id);

            if ($kelas->delete()) {
                return response([
                    'Berhasil Menghapus Data'
                ]);
            } else {
                return response([
                    'Tidak Berhasil Menghapus Data'
                ]);
            }
        } catch (Throwable $ex) {
            return response()->json($ex, 422);
        }
    }

    //-------------------- Delete Materi --------------------//
}
