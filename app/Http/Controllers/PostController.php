<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    public function view(Request $request, $id){
        $post = Post::with('category')->published()->findOrFail($id);
        return Inertia::render('App/Post/View',[
            'post' => $post
        ]);
    }
}
