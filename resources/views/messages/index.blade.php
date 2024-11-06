<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div class="bg-gray-100 min-h-screen">
        <div class="max-w-3xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Your Conversations</h3>
                </div>

                @if($conversations->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($conversations as $user)
                            <li data-user-id="{{ $user->id }}">
                                <a href="{{ route('messages.show', $user) }}" class="block hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <div class="flex items-center px-4 py-4 sm:px-6">
                                        <div class="flex-shrink-0 relative">
                                            <img class="h-12 w-12 rounded-full object-cover" 
                                                 src="{{ $user->profile_picture ? asset('storage/profile_pictures/' . $user->profile_picture) : asset('images/default-avatar.png') }}" 
                                                 alt="{{ $user->name }}">
                                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full {{ $user->isOnline() ? 'bg-green-400' : 'bg-gray-400' }}"></span>
                                        </div>
                                        <div class="ml-4 flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-indigo-600 truncate">{{ $user->name }}</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $user->last_message_at ? \Carbon\Carbon::parse($user->last_message_at)->diffForHumans() : 'Never' }}
                                                </p>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-500 truncate">
                                                {{ $user->last_message ? Str::limit($user->last_message, 50) : 'No messages yet' }}
                                            </p>
                                        </div>
                                        <div class="ml-5 flex-shrink-0">
                                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <button onclick="deleteConversation({{ $user->id }}, event)" 
                                                class="ml-4 text-red-600 hover:text-red-800 transition duration-150 ease-in-out">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No conversations</h3>
                        <p class="mt-1 text-sm text-gray-500">Start by messaging someone new!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function deleteConversation(userId, event) {
        // Prevent the click from bubbling up to the parent link
        event.preventDefault();
        event.stopPropagation();
        
        if (!confirm('Are you sure you want to delete this entire conversation? This cannot be undone.')) {
            return;
        }

        fetch(`/messages/${userId}/conversation`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const conversationElement = document.querySelector(`[data-user-id="${userId}"]`);
                if (conversationElement) {
                    conversationElement.style.transition = 'opacity 0.3s ease-out';
                    conversationElement.style.opacity = '0';
                    setTimeout(() => {
                        conversationElement.remove();
                        // If no conversations left, reload the page to show empty state
                        if (document.querySelectorAll('.divide-y > li').length === 0) {
                            window.location.reload();
                        }
                    }, 300);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete conversation');
        });
    }
    </script>
    @endpush
</x-app-layout>
