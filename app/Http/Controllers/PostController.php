<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Http\Resources\PostDetailResource;
use Illuminate\Http\Request;
use App\Models\Post;
use Faker\Core\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PharIo\Manifest\Author;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->get();
        // return response()->json($posts);
        return PostDetailResource::collection($posts->loadMissing(['author:id,username', 'comments:id,post_id,user_id,comments_content']));
    }

    public function show($id)
    {
        // $post = Post::with('author')->findOrFail($id); //all data post

        $post = Post::with('author:id,username')->findOrFail($id);
        return new PostDetailResource($post->loadMissing(['author:id,username', 'comments:id,post_id,user_id,comments_content']));
        // return response()->json(['data' => $post]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
        ]);

        $image = null;
        if ($request->hasFile('file')) { // Memeriksa apakah file ada dalam request
            // upload file
            $fileName = $this->generateRandomString();
            $extension = $request->file('file')->extension();
            $image = $fileName . '.' . $extension;
            $request->file('file')->storeAs('public/images', $image); // Simpan file ke direktori yang diinginkan
        }

        $requestData = $request->except('file');

        $requestData['image'] = $image;
        $requestData['author_id'] = Auth::user()->id;

        $product = Post::create($requestData);
        return response()->json([
            'data' => $product
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
        ]);

        $post = Post::findorFail($id);
        $post->update($request->all());

        return new PostDetailResource($post->loadMissing('author:id,username'));
    }

    public function showByUser($username)
    {
        $posts = Post::latest()->get();

        $data = PostDetailResource::collection($posts->loadMissing(['author:id,username']));

        // dd($data['author']->username);
        // $data = []; 
        foreach ($posts as $post) {
            // $auth_id = $post['author_id'];

            if ($post['username'] == $username) {
                $data[] = $post;
            }
        }

        return response()->json([
            'data' => $data
        ]);
    }

    public function destroy($id)
    {
        $post = Post::findorFail($id);
        $post->delete();

        return new PostDetailResource($post->loadMissing('author:id,username'));
    }

    function generateRandomString($length = 30)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
