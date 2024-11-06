<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Storage;
use App\Notifications\PostLiked;
use App\Notifications\PostCommented;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        // Use paginate instead of get to enable pagination
        $posts = Post::paginate(10); // Adjust the number as needed
        return view('home', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $post = new Post;
        $post->title = $request->title;
        $post->description = $request->description;
        $post->user_id = auth()->id();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
            $post->image = $imagePath;
        }

        $post->save();

        return redirect()->route('home')->with('success', 'Post created successfully!');
    }

    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        // Authorize the request
        if ($post->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
        ]);

        try {
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($post->image) {
                    Storage::delete('public/' . $post->image);
                }
                $imagePath = $request->file('image')->store('posts', 'public');
                $post->image = $imagePath;
            }

            $post->title = $validated['title'];
            $post->description = $validated['description'];
            $post->save();

            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully',
                'post' => $post
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update post'
            ], 500);
        }
    }

    public function destroy(Post $post)
    {
        // Check if user is authorized to delete the post
        if ($post->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Delete the image if exists
        if ($post->image) {
            Storage::delete('public/' . $post->image);
        }

        $post->delete();

        return redirect()->route('home')->with('success', 'Post deleted successfully!');
    }

    public function like(Post $post)
    {
        $user = auth()->user();
        
        // Check if user has already liked the post
        if ($post->likes()->where('user_id', $user->id)->exists()) {
            // Unlike the post
            $post->likes()->detach($user->id);
            $action = 'unliked';
        } else {
            // Like the post
            $post->likes()->attach($user->id);
            
            // Send notification to post owner if it's not their own post
            if ($post->user_id !== $user->id) {
                $post->user->notify(new PostLiked($user, $post));
            }
            $action = 'liked';
        }
        
        return response()->json([
            'likes_count' => $post->likes()->count(),
            'action' => $action
        ]);
    }

    // public function toggleLike($id)
    // {
    //     $post = Post::findOrFail($id);
    //     $post->likes()->toggle(auth()->id());
    //     return response()->json(['likes_count' => $post->likes()->count()]);
    // }

    public function addComment(Request $request, $id)
    {
        $request->validate(['content' => 'required|string']);
        $post = Post::findOrFail($id);
        
        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        // Send notification to post owner if it's not their own comment
        if ($post->user_id !== auth()->id()) {
            $post->user->notify(new PostCommented(auth()->user(), $post, $comment));
        }

        return response()->json($comment);
    }

    public function editComment(Request $request, $id)
    {
        $request->validate(['content' => 'required|string']);
        $comment = Comment::findOrFail($id);
        $comment->content = $request->content;
        $comment->save();

        return response()->json($comment);
    }

    public function deleteComment($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully']);
    }

    public function storeComment(Request $request, Post $post)
    {
        $comment = $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content
        ]);
        
        // Send notification to post owner if it's not their own comment
        if ($post->user_id !== auth()->id()) {
            $post->user->notify(new PostCommented(auth()->user(), $post, $comment));
        }
        
        return back();
    }
}
