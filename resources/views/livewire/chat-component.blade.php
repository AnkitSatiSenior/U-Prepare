<div>
    <!-- Chat Container -->
    <div class="d-flex h-100 border rounded shadow-sm" style="max-height: 600px;">

        <!-- Sidebar -->
        <div class="d-none d-md-block col-3 border-end bg-white overflow-auto">
            <div class="p-3 border-bottom">
                <strong>Chats</strong>
            </div>

            @foreach($conversations as $conv)
                @php
                    $other = $conv->users->where('id', '!=', auth()->id())->first();
                    $initial = $other ? strtoupper(substr($other->name, 0, 1)) : '?';
                @endphp
                <div wire:click="openConversation({{ $conv->id }})"
                     class="d-flex align-items-center gap-2 p-2 contact-item {{ ($conversation && $conversation->id === $conv->id) ? 'bg-light' : '' }}"
                     style="cursor:pointer;">
                    <div class="avatar bg-primary text-white">{{ $initial }}</div>
                    <div class="flex-grow-1">
                        <div class="fw-bold small">{{ $other?->name ?? 'Unknown' }}</div>
                        <div class="text-muted small text-truncate">
                            {{ $conv->lastMessage?->message ?? 'No messages yet' }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Chat Area -->
        <div class="flex-grow-1 d-flex flex-column">
            @if($conversation)
                <!-- Header -->
                <div class="border-bottom p-3 d-flex align-items-center gap-2 bg-light">
                    @php
                        $other = $conversation->users->where('id', '!=', auth()->id())->first();
                        $initial = $other ? strtoupper(substr($other->name, 0, 1)) : '?';
                    @endphp
                    <div class="avatar bg-secondary text-white">{{ $initial }}</div>
                    <div>
                        <strong class="text-dark">
                            {{ $conversation->name ?? $other?->name }}
                        </strong>
                        <div class="text-muted small">
                            {{ $conversation->is_group ? 'Group Chat' : 'Private Chat' }}
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="flex-grow-1 overflow-auto p-3 chat-messages" style="background:#f8f9fa;">
                    @forelse($messages as $msg)
                        @php
                            $isMine = $msg->sender_id === auth()->id();
                            $initial = strtoupper(substr($msg->sender->name, 0, 1));
                        @endphp

                        <div class="d-flex mb-2 {{ $isMine ? 'justify-content-end' : 'justify-content-start' }}">
                            @unless($isMine)
                                <div class="avatar bg-secondary text-white me-2"
                                     style="width:32px;height:32px;font-size:0.8rem;">
                                    {{ $initial }}
                                </div>
                            @endunless

                            <div class="p-2 bubble {{ $isMine ? 'me bg-primary text-white' : 'them bg-white' }}"
                                 style="max-width:75%;">
                                <span style="white-space: pre-wrap;">{{ $msg->message }}</span>
                                <div class="msg-meta text-end small mt-1">
                                    {{ $msg->created_at->format('H:i') }}

                                    @if($isMine)
                                        <small class="ms-1">
                                            @if($msg->is_read)
                                                <i class="fas fa-check-double text-success"></i> Read
                                            @else
                                                <i class="fas fa-check text-light"></i> Sent
                                            @endif
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted mt-4">
                            No messages yet. Start chatting!
                        </div>
                    @endforelse
                </div>

                <!-- Input -->
                <div class="border-top p-3 bg-white d-flex gap-2">
                    <input wire:model.defer="message"
                           wire:keydown.enter="sendMessage"
                           class="form-control"
                           placeholder="Type a message...">
                    <button wire:click="sendMessage" class="btn btn-primary px-4">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            @else
                <!-- No conversation selected -->
                <div class="flex-grow-1 d-flex align-items-center justify-content-center text-muted">
                    Select a chat to start messaging
                </div>
            @endif
        </div>
    </div>

    <!-- Style tweaks -->
    <style>
        .avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold;
        }
        .bubble {
            border-radius: 14px;
            padding: .6rem .75rem;
            font-size: .9rem;
        }
        .bubble.me {
            border-bottom-right-radius: 6px;
        }
        .bubble.them {
            border: 1px solid #eee;
            border-bottom-left-radius: 6px;
        }
        .msg-meta {
            font-size: .7rem;
            opacity: 0.7;
        }
        .contact-item:hover {
            background: #f5f5f5;
        }
    </style>

    <!-- Scroll helper -->
   <!-- Scroll helper -->
<script>
    function scrollToBottom(force = false) {
        const chatList = document.querySelector('.chat-messages');
        if (!chatList) return;

        // Auto-scroll if force = true OR user is already near bottom
        const threshold = 80; // px tolerance
        const isNearBottom = chatList.scrollHeight - chatList.scrollTop - chatList.clientHeight < threshold;

        if (force || isNearBottom) {
            chatList.scrollTop = chatList.scrollHeight;
        }
    }

    // Run once on load
    document.addEventListener('DOMContentLoaded', () => scrollToBottom(true));

    // After every Livewire update
    Livewire.hook('message.processed', () => {
        setTimeout(() => scrollToBottom(), 50);
    });

    // Extra: scroll down when switching back to this tab or window
    window.addEventListener('focus', () => scrollToBottom(true));

    // Extra: scroll down on mousemove (if you want very aggressive auto-scroll)
    document.addEventListener('mousemove', () => scrollToBottom());
</script>

</div>
