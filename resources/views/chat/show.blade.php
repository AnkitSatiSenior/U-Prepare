<x-app-layout>
    <div class="container mt-4" style="height: 500px;">
        <h4>{{ $conversation->name ?? 'Chat Room' }}</h4>

        <!-- Correct Livewire inclusion -->
        @livewire('chat-component', ['conversationId' => $conversation->id])
    </div>
</x-app-layout>
