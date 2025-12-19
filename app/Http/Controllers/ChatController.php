<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display all conversations of the logged-in user.
     */
   public function index($id = null)
{
    $conversations = Auth::user()
        ->conversations()
        ->with('users')
        ->latest('updated_at')
        ->get();

    $conversation = null;
    if ($id) {
        $conversation = Conversation::with(['users', 'messages.sender'])
            ->findOrFail($id);
    }

    return view('chat.index', compact('conversations', 'conversation'));
}


    /**
     * Show a specific conversation with messages.
     *
     * @param int $id
     */
    public function show(int $id)
    {
        $conversation = Conversation::with(['users', 'messages.sender'])
            ->findOrFail($id);

        return view('chat.show', compact('conversation'));
    }

    /**
     * Create or open a 1-to-1 conversation with another user.
     *
     * @param Request $request
     */
   public function create(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
    ]);

    $userId = (int) $request->user_id;
    $authId = auth()->id();

    if ($userId === $authId) {
        return redirect()->back()->with('error', 'You cannot chat with yourself.');
    }

    // Check if a 1-to-1 conversation already exists
    $conversation = \App\Models\Conversation::where('is_group', false)
        ->whereHas('users', fn($q) => $q->where('users.id', $authId))
        ->whereHas('users', fn($q) => $q->where('users.id', $userId))
        ->first();

    // Create new conversation if it doesn't exist
    if (!$conversation) {
        $conversation = \App\Models\Conversation::create([
            'is_group' => false,
            'name' => null,
        ]);
        $conversation->users()->attach([$authId, $userId]);
    }

    return redirect()->route('admin.chat.show', $conversation->id);
}

}
