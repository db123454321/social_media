<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $user->name }}'s Profile
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center mb-6">
                        @if($user->profile_picture)
                            <img src="{{ asset('storage/profile_pictures/' . $user->profile_picture) }}" 
                                 alt="{{ $user->name }}" 
                                 class="w-32 h-32 rounded-full object-cover">
                        @endif
                        <div class="ml-6">
                            <h3 class="text-2xl font-bold">{{ $user->name }}</h3>
                            <p class="text-gray-600">{{ $user->email }}</p>
                            @if($user->bio)
                                <p class="mt-2 text-gray-700">{{ $user->bio }}</p>
                            @endif
                        </div>
                        @if($user->id !== auth()->id())
                            <a href="{{ route('messages.show', $user) }}" 
                               class="ml-4 inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                </svg>
                                Message
                            </a>
                        @endif
                    </div>

                    <div class="mt-8">
                        <h4 class="text-xl font-semibold mb-4">Posts</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($posts as $post)
                                <div class="bg-white border rounded-lg overflow-hidden">
                                    @if($post->image)
                                        <img src="{{ asset('storage/' . $post->image) }}" 
                                             alt="{{ $post->title }}" 
                                             class="w-full h-48 object-cover">
                                    @endif
                                    <div class="p-4">
                                        <h5 class="font-semibold">{{ $post->title }}</h5>
                                        <p class="text-gray-600 text-sm mt-2">
                                            {{ Str::limit($post->description, 100) }}
                                        </p>
                                        <div class="mt-4">
                                            <a href="{{ route('posts.show', $post) }}" 
                                               class="text-blue-500 hover:underline">
                                                View Post
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6">
                            {{ $posts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
