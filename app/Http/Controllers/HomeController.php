<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request){
        $posts = Post::published()->with('category')->orderByDesc('created_at')->take(6)->get();

        // Post içeriğini temizleme ve kısaltma işlemleri
        $posts->each(function ($post) {
            $post->content = strip_tags($post->content);
            $post->content = Str::limit($post->content, 100);
        });

        return Inertia::render('Welcome',[
            'posts' => $posts,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'appName' => env('APP_NAME'),
            'phpVersion' => PHP_VERSION,
        ]);
    }
}
