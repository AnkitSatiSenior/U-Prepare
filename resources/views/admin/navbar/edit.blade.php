<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.navbar-items.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Navbar Item
            </h2>
        </div>
    </x-slot>

    <div class="py-6 container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
                <h3 class="text-lg font-medium text-white flex items-center">
                    <i class="fas fa-edit mr-2"></i> Edit Navigation Item
                </h3>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.navbar-items.update', $navbarItem) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title Field -->
                        <div class="space-y-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="text-indigo-600 dark:text-indigo-400">*</span> Title
                            </label>
                            <input type="text" name="title" id="title" required
                                class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition duration-150"
                                value="{{ $navbarItem->title }}"
                                placeholder="Home">
                        </div>

                        <!-- Slug Field -->
                        <div class="space-y-2">
                            <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                <span class="text-indigo-600 dark:text-indigo-400">*</span> Slug
                            </label>
                            <div class="relative">
                                <input type="text" name="slug" id="slug" required
                                    class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition duration-150"
                                    value="{{ $navbarItem->slug }}"
                                    placeholder="home">
                                <button type="button" onclick="generateSlug()"
                                    class="absolute right-2 top-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Parent Item -->
                        <div class="space-y-2">
                            <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Parent Item
                            </label>
                            <select name="parent_id" id="parent_id"
                                class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition duration-150">
                                <option value="">None (Top Level)</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}" {{ $navbarItem->parent_id == $parent->id ? 'selected' : '' }}>{{ $parent->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Is Dropdown -->
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="hidden" name="is_dropdown" value="0">
                                <input type="checkbox" name="is_dropdown" id="is_dropdown" value="1"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded transition duration-150"
                                    {{ $navbarItem->is_dropdown ? 'checked' : '' }}>
                                <label for="is_dropdown" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                    Is Dropdown
                                </label>
                            </div>
                        </div>

                        <!-- Order -->
                        <div class="space-y-2">
                            <label for="order" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Order
                            </label>
                            <input type="number" name="order" id="order"
                                class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition duration-150"
                                value="{{ $navbarItem->order }}">
                        </div>

                        <!-- Is Active -->
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded transition duration-150"
                                    {{ $navbarItem->is_active ? 'checked' : '' }}>
                                <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                    Is Active
                                </label>
                            </div>
                        </div>

                        <!-- Route Name -->
                        <div class="space-y-2">
                            <label for="route" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Route Name
                            </label>
                            <input type="text" name="route" id="route"
                                class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition duration-150"
                                value="{{ $navbarItem->route }}"
                                placeholder="home.index">
                        </div>

                        <!-- Custom URL -->
                        <div class="space-y-2">
                            <label for="url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Custom URL
                            </label>
                            <input type="text" name="url" id="url"
                                class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition duration-150"
                                value="{{ $navbarItem->url }}"
                                placeholder="/home">
                        </div>

                        <!-- Icon Class -->
                        <div class="space-y-2">
                            <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Icon Class
                            </label>
                            <div class="relative">
                                <input type="text" name="icon" id="icon"
                                    class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition duration-150"
                                    value="{{ $navbarItem->icon }}"
                                    placeholder="fas fa-home">
                                <div class="absolute right-2 top-2 text-gray-400">
                                    <i class="fas fa-icons"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-6 space-x-4">
                        <a href="{{ route('admin.navbar-items.index') }}" 
                            class="inline-flex items-center px-6 py-3 bg-gray-500 border border-transparent rounded-lg font-semibold text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 border border-transparent rounded-lg font-semibold text-white uppercase tracking-widest hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <i class="fas fa-save mr-2"></i> Update Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function generateSlug() {
            const title = document.getElementById('title').value;
            if (title) {
                const slug = title.toLowerCase()
                    .replace(/[^\w\s-]/g, '') // Remove special chars
                    .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
                    .replace(/^-+|-+$/g, ''); // Trim hyphens from start/end
                document.getElementById('slug').value = slug;
            }
        }

        // Auto-generate slug when title changes
        document.getElementById('title').addEventListener('input', function() {
            if (!document.getElementById('slug').value) {
                generateSlug();
            }
        });
    </script>
</x-app-layout>