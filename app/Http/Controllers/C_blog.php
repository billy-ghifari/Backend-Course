<?php

namespace App\Http\Controllers;

use App\Helper\Bloghelper;
use App\Models\blog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class C_blog extends Controller
{
    //read blog
    public function index()
    {
        $pagination = Bloghelper::pagination();

        return $pagination;
    }
    //read blog

    //create blog
    public function post_blog(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'judul' => 'required',
                'r_id_category' => 'required',
                'content' => 'required',
                'foto_thumbnail' => 'required',
                'r_id_non_siswa' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $validatordata = $validator->validated();

            $imageName = time() . '.' . $validatordata['foto_thumbnail']->extension();
            $request->foto_thumbnail->move(public_path('blog'), $imageName);
            $validatordata['foto_thumbnail'] = $imageName;

            $makeblog = Bloghelper::makeblog($validatordata);

            return $makeblog;
        } catch (\Exception $e) {
            return response()->json([
                'response_code' =>  404,
                'message'       =>   $e,
            ]);
        }
    }
    //create blog

    //update blog
    public function update(Request $request, $id)
    {
        try {
            $blog = blog::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json("Tidak dapat menemukan blog", 422);
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'required',
            'r_id_category' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatordata = $validator->validated();

        $updateblog = Bloghelper::updateblog($validatordata, $blog);

        return $updateblog;
    }
    //update blog

    //deleted blog
    public function destroy($id)
    {
        try {

            try {
                $blog = blog::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return response()->json("Tidak dapat menemukan blog", 422);
            }

            $deleteblog = Bloghelper::deleteblog($blog);

            return $deleteblog;
        } catch (Throwable $ex) {
            // Alert::warning('Error', 'Cant deleted, Barang already used !');
            return response()->json(['message' => 'tidak dapat menghapus blog']);
        }
    }
    //deleted blog
}
