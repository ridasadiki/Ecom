<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use App\Events\NewChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index()
    {
        $messages = ChatMessage::with('user')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chat.index', compact('messages'));
    }

    public function adminIndex()
    {
        return view('admin.chat.index');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:500'
            ]);

            $message = ChatMessage::create([
                'user_id' => Auth::id(),
                'message' => $request->message,
                'type' => 'user'
            ]);

            $message->load('user');

            Log::info('Broadcasting message', ['message' => $message->toArray()]);

            broadcast(new NewChatMessage($message))->toOthers();

            return response()->json($message);
        } catch (\Exception $e) {
            Log::error('Error in chat store:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to send message',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function adminStore(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:500',
                'user_id' => 'required|exists:users,id'
            ]);

            $message = ChatMessage::create([
                'user_id' => $request->user_id,
                'message' => $request->message,
                'type' => 'admin'
            ]);

            $message->load('user');

            Log::info('Broadcasting admin message', ['message' => $message->toArray()]);

            broadcast(new NewChatMessage($message))->toOthers();

            return response()->json($message);
        } catch (\Exception $e) {
            Log::error('Error in admin chat store:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to send message',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function getMessages($userId = null)
    {
        $query = ChatMessage::with('user')->orderBy('created_at', 'asc');
        
        if ($userId) {
            $query->where('user_id', $userId);
        } elseif (Auth::user()->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }

        $messages = $query->get();
        return response()->json($messages);
    }

    public function getUsers()
    {
        $users = User::whereHas('chatMessages')
            ->where('role', '!=', 'admin')
            ->orderByDesc(function ($query) {
                $query->select('created_at')
                    ->from('chat_messages')
                    ->whereColumn('user_id', 'users.id')
                    ->latest()
                    ->limit(1);
            })
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }
}
