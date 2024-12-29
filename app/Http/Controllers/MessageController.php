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

    public function store(Request $request, $receiverId)
    {
        try {
            $request->validate([
                'message' => 'nullable|string|max:500',
                'image' => 'nullable|string', // Changed from 'image' to 'string' to accept URLs
            ]);

            // Initialize message text
            $messageText = $request->message;
            $imagePath = null;

            // Handle image URL from Firebase
            if ($request->image && str_contains($request->image, 'firebasestorage.googleapis.com')) {
                $imagePath = $request->image; // Store the full Firebase URL
                
                // If no text message, set a default message for image
                if (empty($messageText)) {
                    $messageText = '[Image]';
                }
            }

            // Ensure there's either a message or an image
            if (!$messageText && !$imagePath) {
                throw new \Exception('Please provide a message or image.');
            }

            $message = Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $receiverId,
                'message' => $messageText,
                'image' => $imagePath,
                'created_at' => now('Asia/Kuala_Lumpur'),
            ]);

            // Load relationships for the response
            $message->load('sender', 'receiver');

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            \Log::error('Error sending message:', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
} 