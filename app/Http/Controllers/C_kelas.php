<?php

namespace App\Http\Controllers;

use App\Helper\Kelashelper;
use App\Models\kelas;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class C_kelas extends Controller
{
    //show kelas
    public function index()
    {
        $pagination = Kelashelper::paginate();

        return $pagination;
    }
    //show kelas

    //create kelas
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

        $makekelas = Kelashelper::makekelas($validatordata);

        return $makekelas;
    }
    //create kelas

    //update kelas
    public function update(Request $request, $id)
    {
        try {
            $kelas = Kelas::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json("Tidak dapat menemukan kelas", 422);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'deskripsi' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatordata = $validator->validated();

        $updatekelas = Kelashelper::updatekelas($kelas, $validatordata);

        return $updatekelas;
    }
    //update kelas

    //deleted kelas
    public function destroy($id)
    {
        try {

            try {
                $kelas = Kelas::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return response()->json("Tidak dapat menemukan kelas", 422);
            }

            $deletekelas = Kelashelper::deletekelas($kelas);

            return $deletekelas;
        } catch (Throwable $ex) {
            // Alert::warning('Error', 'Cant deleted, Barang already used !');
            return response()->json($ex, 422);
        }
    }
    //deleted kelas
}
