<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Http\Resources\PostDetailResource;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use PharIo\Manifest\Author;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('author:id,username')->get();

        // return response()->json($posts);
        return PostDetailResource::collection($posts);
    }

    public function show($id)
    {
        // $post = Post::with('author')->findOrFail($id); //all data post

        $post = Post::with('author:id,username')->findOrFail($id);
        return new PostDetailResource($post);
        // return response()->json(['data' => $post]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
        ]);

        $request['author_id'] = Auth::user()->id;
        //menyimpan data ke database
        $post = Post::create($request->all());
        return new PostDetailResource($post->loadMissing('author:id,username'));

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

    public function destroy($id)
    {
        $post = Post::findorFail($id);
        $post->delete();

        return new PostDetailResource($post->loadMissing('author:id,username'));
    }
}
