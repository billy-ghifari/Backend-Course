<?php

namespace App\Http\Controllers;

use App\Models\blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class C_blog extends Controller
{
    //read blog
    public function index()
    {
        $blogs = blog::latest()->paginate(5);
        return response()->json(['message' => 'List data review', 'data' => $blogs]);
    }
    //read blog

    //create blog
    public function post_blog(Request $request)
    {
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

        $post = blog::create([
            'judul' => $validatordata['judul'],
            'r_id_category' => $validatordata['r_id_category'],
            'content' => $validatordata['content'],
            'foto_thumbnail' => $validatordata['foto_thumbnail'],
            'r_id_non_siswa' => $validatordata['r_id_non_siswa']
        ]);

        return response()->json(['message' => 'data berhasil ditambahkan', 'data' => $post], 200);
    }
    //create blog

    //update blog
    public function update(Request $request, blog $blog, $id)
    {
        $post = blog::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'judul' => 'required',
            'r_id_category' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $validatordata = $validator->validated();

        if ($request->all()) {
            $post->update([
                'judul' => $validatordata['judul'],
                'r_id_category' => $validatordata['r_id_category'],
                'content' => $validatordata['content'],
            ]);
        }

        return response()->json(['message' => 'data berhasil diubah', 'data' => $post], 200);
    }
    //update blog

    //deleted blog
    public function destroy($id)
    {
        try {
            $blog = blog::findOrFail($id);

            Storage::delete('public/profile/' . $blog->foto_thumbnail);
            if ($blog->delete()) {
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
    //deleted blog
}
