<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function categories(Request $request){
        $categories = Category::all();
        return response()->json($categories);
    }

    public function view(Request $request, $id){
        $category = Category::findOrFail($id);
        $posts = Post::where('category_id',$category->id)->with('category')->orderByDesc('created_at')->published()->get();

        // Post içeriğini temizleme ve kısaltma işlemleri
        $posts->each(function ($post) {
            $post->content = strip_tags($post->content);
            $post->content = Str::limit($post->content, 100);
        });

        return Inertia::render('App/Category/View',[
            'category' => $category,
            'posts' => $posts
        ]);
    }

    public function posts(Request $request, $id){
        $posts = Post::where('category_id', $id)->with('category')->orderByDesc('created_at')->published()->paginate(6);

        $posts->each(function ($post) {
            $post->content = strip_tags($post->content);
            $post->content = Str::limit($post->content, 100);
        });

        return $posts;
    }
}
