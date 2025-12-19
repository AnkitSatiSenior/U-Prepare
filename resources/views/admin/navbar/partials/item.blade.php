<li class="py-3 px-4 mb-2 rounded-lg bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700" data-id="{{ $item->id }}">
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <div class="mr-4 text-indigo-400 dark:text-indigo-500 cursor-move hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <div class="flex items-center">
                    @if($item->icon)
                        <i class="{{ $item->icon }} mr-2 text-indigo-500 dark:text-indigo-400"></i>
                    @endif
                    <span class="font-medium text-gray-800 dark:text-white">{{ $item->title }}</span>
                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ $item->slug }}</span>
                </div>
                <div class="flex items-center mt-1 space-x-2">
                    @if($item->is_dropdown)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                            <i class="fas fa-caret-down mr-1"></i> Dropdown
                        </span>
                    @endif
                    @if(!$item->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            <i class="fas fa-eye-slash mr-1"></i> Inactive
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            <i class="fas fa-check-circle mr-1"></i> Active
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.navbar-items.edit', $item) }}" 
               class="p-2 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 dark:text-indigo-400 dark:bg-indigo-900/50 dark:hover:bg-indigo-800 rounded-lg transition-colors"
               title="Edit">
                <i class="fas fa-pencil-alt"></i>
            </a>
            <form action="{{ route('admin.navbar-items.destroy', $item) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="p-2 text-red-600 bg-red-50 hover:bg-red-100 dark:text-red-400 dark:bg-red-900/50 dark:hover:bg-red-800 rounded-lg transition-colors"
                        title="Delete">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </div>

    @if($item->children->count())
        <ul class="ml-12 mt-3 space-y-2">
            @foreach($item->children as $child)
                @include('admin.navbar.partials.item', ['item' => $child])
            @endforeach
        </ul>
    @endif
</li>