<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <div class="relative">
                <img class="h-12 w-12 rounded-full object-cover border-2 border-gray-200" 
                     src="{{ $user->profile_picture ? asset('storage/profile_pictures/' . $user->profile_picture) : asset('images/default-avatar.png') }}" 
                     alt="{{ $user->name }}">
                <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full {{ $user->isOnline() ? 'bg-green-400' : 'bg-gray-400' }} border-2 border-white"></span>
            </div>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $user->name }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ $user->isOnline() ? 'Online' : 'Last seen ' . $user->last_activity?->diffForHumans() }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="space-y-4 h-96 overflow-y-auto mb-4 px-4 scrollbar-thin scrollbar-thumb-gray-300 hover:scrollbar-thumb-gray-400" id="messages-container">
                        @foreach($messages->reverse() as $message)
                            <div class="message-item flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }} mb-4">
                                @include('messages.message-item', ['message' => $message])
                            </div>
                        @endforeach
                    </div>

                    <form id="messageForm" class="mt-4">
                        @csrf
                        <div class="flex space-x-2">
                            <input type="text" 
                                   name="content" 
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   placeholder="Type your message..."
                                   required>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md">
                                Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const messagesContainer = document.getElementById('messages-container');
        const messageForm = document.getElementById('messageForm');
        const recipientId = {{ $user->id }};

        // Initialize Echo and listen for new messages
        window.Echo.private(`messages.${recipientId}`)
            .listen('NewMessage', (e) => {
                const messageData = e.message;
                const messageHtml = createMessageHtml(messageData, false);
                messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                
                // Update conversations list for real-time updates
                updateConversationsList(messageData);
            });

        function createMessageHtml(message, isSelf) {
            return `
                <div class="message-item flex ${isSelf ? 'justify-end' : 'justify-start'} mb-4">
                    <div class="flex ${isSelf ? 'flex-row-reverse' : 'flex-row'} items-end space-x-2">
                        <div class="flex-shrink-0 ${isSelf ? 'ml-2' : 'mr-2'}">
                            <img class="h-8 w-8 rounded-full object-cover" 
                                 src="${message.sender.profile_picture}" 
                                 alt="${message.sender.name}">
                        </div>
                        <div class="${isSelf ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800'} rounded-lg px-4 py-2 max-w-sm break-words">
                            <p class="text-sm font-medium mb-1">${message.sender.name}</p>
                            <p>${message.content}</p>
                            <span class="text-xs ${isSelf ? 'text-blue-100' : 'text-gray-500'} block mt-1">
                                ${message.created_at}
                            </span>
                        </div>
                    </div>
                </div>
            `;
        }

        messageForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const input = messageForm.querySelector('input[name="content"]');
            const content = input.value.trim();
            
            if (!content) return;

            try {
                // Clear input immediately for better UX
                input.value = '';

                // Create and append message immediately for instant feedback
                const tempMessage = {
                    content: content,
                    sender: {
                        id: {{ auth()->id() }},
                        name: '{{ auth()->user()->name }}',
                        profile_picture: '{{ auth()->user()->profile_picture ? asset("storage/profile_pictures/" . auth()->user()->profile_picture) : asset("images/default-avatar.png") }}'
                    },
                    created_at: new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' })
                };

                const messageHtml = createMessageHtml(tempMessage, true);
                messagesContainer.insertAdjacentHTML('beforeend', messageHtml);

                const response = await fetch(`/messages/${recipientId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ content })
                });

                if (!response.ok) throw new Error('Network response was not ok');

                const data = await response.json();
                
                // Update the message in conversations list if it exists
                updateConversationsList(data.message);
                
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to send message. Please try again.');
            }
        });

        // Add this helper function to update conversations list
        function updateConversationsList(message) {
            const conversationsList = document.querySelector('.conversations-list');
            if (conversationsList) {
                const existingConversation = conversationsList.querySelector(`[data-user-id="${recipientId}"]`);
                if (existingConversation) {
                    const lastMessageElement = existingConversation.querySelector('.last-message');
                    if (lastMessageElement) {
                        lastMessageElement.textContent = message.content;
                        const timestampElement = existingConversation.querySelector('.timestamp');
                        if (timestampElement) {
                            timestampElement.textContent = message.created_at;
                        }
                    }
                }
            }
        }

        function deleteConversation(userId, event) {
            // Prevent the click from bubbling up to the parent link
            event.preventDefault();
            
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
