<?php

namespace App\Http\Controllers;

use App\Helper\Materihelper;
use App\Models\materi;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class C_materi extends Controller
{
    //create materi
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

        $makemateri = Materihelper::makemateri($validatordata);

        return $makemateri;
    }
    //create materi

    //update materi
    public function update(Request $request, $id)
    {
        try {
            $materi = materi::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json("Tidak dapat menemukan materi", 422);
        }

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

        $updatemateri = Materihelper::updatemateri($materi, $validatordata);

        return $updatemateri;
    }
    //update materi

    //deleted materi
    public function destroy($id)
    {
        try {

            try {
                $materi = materi::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return response()->json("Tidak dapat menemukan materi", 422);
            }

            $deletemateri = Materihelper::deletemateri($materi);

            return $deletemateri;
        } catch (Throwable $ex) {
            // Alert::warning('Error', 'Cant deleted, Barang already used !');
            return response()->json($ex, 422);
        }
    }
    //deleted materi
}
