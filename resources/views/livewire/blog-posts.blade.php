<!-- resources/views/livewire/blog-posts.blade.php -->

<div class="container py-8 mx-auto">
    <!-- Search Bar -->
    <div class="mb-4">
        <input wire:model.debounce.500ms="search" type="text" placeholder="Search posts..."
            class="w-full p-2 border rounded">
    </div>

    <div class="flex">
        <!-- Sidebar for Categories -->
        <aside class="w-1/4 pr-4">
            <h2 class="mb-4 text-lg font-bold">Categories</h2>
            <ul class="list-none">
                <li><a wire:click="$set('category', null)" href="#" class="text-blue-500">All</a></li>
                {{-- @foreach ($categories as $cat)
                    <li><a wire:click="$set('category', '{{ $cat->slug }}')" href="#"
                            class="text-blue-500">{{ $cat->name }}</a></li>
                @endforeach --}}
            </ul>
        </aside>

        <!-- Main Content: Posts List -->
        <main class="w-3/4">
            {{-- @if ($posts->count())
                <div class="grid grid-cols-1 gap-6">
                    @foreach ($posts as $post)
                        <article class="pb-4 mb-4 border-b">
                            <h2 class="text-xl font-bold">
                                <a href="{{ route('post.show', $post->slug) }}">{{ $post->title }}</a>
                            </h2>
                            <p>{{ Str::limit($post->content, 150) }}</p>
                            <a href="{{ route('post.show', $post->slug) }}" class="text-blue-500">Read more</a>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination Links -->
                <div class="mt-4">
                    {{ $posts->links() }}
                </div>
            @else
                <p>No posts found.</p>
            @endif --}}
        </main>
    </div>
</div>
