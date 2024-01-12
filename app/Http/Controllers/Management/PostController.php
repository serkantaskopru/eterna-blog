<?php

namespace App\Http\Controllers\Management;

use App\Mail\NewPostMail;
use App\Models\Category;
use App\Models\Newsletter;
use App\Models\Post;
use App\Notifications\NewPostNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PostController extends Controller
{
    public function view(Request $request)
    {
        $posts = Post::with('category')->orderByDesc('created_at')->get();
        $categories = Category::orderBy('name')->get();

        // Post içeriğini temizleme ve kısaltma işlemleri
        $posts->each(function ($post) {
            $post->content = strip_tags($post->content);
            $post->content = Str::limit($post->content, 100);
        });

        return Inertia::render('Posts', [
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }

    public function edit($id)
    {
        $post = Post::where('id',$id)
            ->with('category')
            ->first(['id', 'name', 'content', 'category_id']);

        return response()->json($post);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string|min:5',
            'category_id' => [
                'required',
                'exists:categories,id',
            ],
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
        ]);

        try {
            $newPost = Post::create([
                'name' => $request->input('name'),
                'image' => '',
                'content' => $request->input('content'),
                'category_id' => $request->input('category_id'),
                'status' => 1,
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads'), $imageName);
                $newPost->image = 'uploads/' . $imageName;
                $newPost->save();
            }

            if ($request->input('newsletter_send')) {
                // Bültene kayıtlı email adreslerine bildirim gönder
                $emails = Newsletter::pluck('email');

                foreach ($emails as $email) {
                    Mail::to($email)->send(new NewPostMail($newPost));
                }
            }

            return Redirect::back()->with([
                'data' => 'Post created successfully',
            ]);
        } catch (\Exception $exception) {
            \Log::error($exception);
            return response()->json(['message' => 'Error creating post', 'error' => $exception->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string|min:5',
            'post_id' => [
                'required',
                'exists:posts,id',
            ],
            'category_id' => [
                'required',
                'exists:categories,id',
            ]
        ]);
        try {
            $post = Post::find($request->input('post_id'));
            $post->name = $request->input('name');
            $post->content = $request->input('content');
            $post->category_id = $request->input('category_id');
            if ($request->hasFile('image')) {
                $request->validate([
                    'image' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
                ]);
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads'), $imageName);
                $post->image = 'uploads/' . $imageName;
            }
            $post->save();
            return Redirect::back()->with([
                'data' => 'Post updated successfully',
            ]);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Error creating post', 'error' => $exception->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        $request->validate([
            'post_id' => [
                'required',
                'exists:posts,id',
            ]
        ]);
        try {
            Post::where('id',$request->input('post_id'))->delete();
            return Redirect::back()->with([
                'data' => 'Post updated successfully',
            ]);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Error creating post', 'error' => $exception->getMessage()], 500);
        }
    }
}
