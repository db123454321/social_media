<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\NewMessage;

class MessageController extends Controller
{
    public function index()
    {
        // Disable strict mode for this query
        \DB::statement("SET SQL_MODE=''");

        $conversations = auth()->user()->conversations()
            ->join('messages as last_message', function ($join) {
                $join->on(function ($query) {
                    $query->where(function ($q) {
                        $q->where('messages.sender_id', auth()->id())
                            ->whereRaw('last_message.recipient_id = users.id');
                    })->orWhere(function ($q) {
                        $q->where('messages.recipient_id', auth()->id())
                            ->whereRaw('last_message.sender_id = users.id');
                    });
                })->whereRaw('last_message.created_at = (
                    SELECT MAX(created_at)
                    FROM messages
                    WHERE (sender_id = ? AND recipient_id = users.id)
                       OR (sender_id = users.id AND recipient_id = ?)
                )', [auth()->id(), auth()->id()]);
            })
            ->select([
                'users.*',
                'last_message.content as last_message',
                'last_message.created_at as last_message_at'
            ])
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('messages.index', compact('conversations'));
    }

    public function show(User $user)
    {
        $messages = Message::between(auth()->user(), $user)
                          ->latest()
                          ->get();
        
        // Mark messages as read
        $messages->where('recipient_id', auth()->id())
                ->where('read_at', null)
                ->each(function ($message) {
                    $message->markAsRead();
                });
        
        return view('messages.show', compact('user', 'messages'));
    }

    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message = auth()->user()->sentMessages()->create([
            'recipient_id' => $user->id,
            'content' => $validated['content'],
            'read_at' => null,
        ]);

        // Load the sender relationship
        $message->load('sender');

        // Broadcast the new message event
        broadcast(new NewMessage($message))->toOthers();

        return response()->json([
            'message' => $message,
            'sender' => [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'profile_picture' => auth()->user()->profile_picture
                    ? asset('storage/profile_pictures/' . auth()->user()->profile_picture)
                    : asset('images/default-avatar.png')
            ],
            'created_at' => $message->created_at->format('g:i A')
        ]);
    }

    public function destroy(Message $message)
    {
        $this->authorize('delete', $message);
        $message->delete();
        return back()->with('success', 'Message deleted successfully.');
    }

    public function deleteConversation(User $user)
    {
        // Delete all messages between the authenticated user and the specified user
        Message::between(auth()->user(), $user)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Conversation deleted successfully'
        ]);
    }
}
