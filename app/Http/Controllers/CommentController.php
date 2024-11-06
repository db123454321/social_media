<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function update(Request $request, Comment $comment)
    {
        // Check if user is authorized to edit this comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update($validated);

        return response()->json([
            'success' => true,
            'content' => $comment->content,
            'user_name' => $comment->user->name,
            'updated_at' => $comment->updated_at->diffForHumans()
        ]);
    }

    public function destroy(Comment $comment)
    {
        // Check if user is authorized to delete this comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }
}
