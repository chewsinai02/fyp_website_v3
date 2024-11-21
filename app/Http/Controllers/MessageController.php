<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function getUnreadMessages()
    {
        $unreadMessages = Message::where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->with('sender')
            ->get();

        return response()->json([
            'count' => $unreadMessages->count(),
            'messages' => $unreadMessages->count() > 0 ? $unreadMessages->map(function($message) {
                return [
                    'id' => $message->id,
                    'sender' => $message->sender->name,
                    'content' => \Str::limit($message->message, 50),
                    'time' => $message->created_at->diffForHumans(),
                ];
            }) : []
        ]);
    }

    public function markAsRead(Request $request)
    {
        if ($request->messageIds) {
            // Mark specific messages as read
            Message::whereIn('id', $request->messageIds)
                ->update(['is_read' => true]);
        } else {
            // Mark all messages as read
            Message::where('receiver_id', auth()->id())
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }
} 