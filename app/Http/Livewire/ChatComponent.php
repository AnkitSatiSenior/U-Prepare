<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatComponent extends Component
{
    public $conversations = [];
    public $conversation;
    public $conversationId;
    public $messages = [];
    public $message = '';
    public $user;
    public $activeContact;
    public $search = '';

    protected $listeners = ['refreshMessages' => 'loadConversation'];

    public function mount($conversationId = null)
    {
        $this->user = Auth::user();
        $this->loadSidebar();

        if ($conversationId) {
            $this->openConversation($conversationId);
        }
    }

    /**
     * Load sidebar chat list
     */
    public function loadSidebar()
    {
        $this->conversations = Conversation::with('users', 'lastMessage')
            ->whereHas('users', fn($q) => $q->where('user_id', Auth::id()))
            ->get();
    }

    /**
     * Open a conversation and load messages
     */
    public function openConversation($id)
    {
        $this->conversationId = $id;

        $this->conversation = Conversation::with(['users', 'messages.sender'])
            ->findOrFail($id);

        $this->messages = $this->conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Identify the other user in this conversation
        $this->activeContact = $this->conversation->users
            ->where('id', '!=', Auth::id())
            ->first();

        // Mark unread messages as read
        Message::where('conversation_id', $id)
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * Reload the current conversation (useful after sending/receiving)
     */
    public function loadConversation()
    {
        if (!$this->conversationId) return;

        $this->openConversation($this->conversationId);
    }

    /**
     * Send a message
     */
    public function sendMessage()
    {
        if (empty($this->message) || !$this->conversationId) return;

        Message::create([
            'conversation_id' => $this->conversationId,
            'sender_id'       => Auth::id(),
            'message'         => $this->message,
            'is_read'         => false,
        ]);

        $this->message = '';
        $this->loadConversation();
        $this->dispatch('refreshMessages');
    }

    public function render()
    {
        return view('livewire.chat-component');
    }
}
