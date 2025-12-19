<x-app-layout>
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <x-admin.breadcrumb-header
            icon="fas fa-comments text-primary"
            title="Chat Room"
            :breadcrumbs="[
                ['route' => 'dashboard', 'label' => '<i class=\'fas fa-home\'></i>'],
                ['label' => 'Admin'],
                ['label' => 'Chat']
            ]"
        />

        <!-- Alerts -->
        @if (session('success'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="success" :message="session('success')" dismissible />
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="row mb-3">
                <div class="col-md-12">
                    <x-alert type="danger" :message="session('error')" dismissible />
                </div>
            </div>
        @endif

        <div class="row">
            <!-- Conversation List -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary"><i class="fas fa-list me-2"></i> Chats</h5>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newChatModal">
                            <i class="fas fa-plus me-1"></i> New
                        </button>
                    </div>
                   
                </div>
            </div>

            <!-- Chat Window -->
            
        </div>
 @livewire('chat-component')
        <!-- New Chat Modal -->
        <div class="modal fade" id="newChatModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.chat.create') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Start New Chat</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label for="user_id" class="form-label">Select User:</label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                @foreach(\App\Models\User::where('id', '!=', auth()->id())->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Start Chat</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
