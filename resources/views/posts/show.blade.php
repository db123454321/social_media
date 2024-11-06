<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" class="w-full h-96 object-cover rounded-lg mb-4">
                    @endif
                    <h1 class="text-3xl font-bold mb-4">{{ $post->title }}</h1>
                    <p class="text-gray-700 mb-4">{{ $post->description }}</p>
                    <div class="post-author mb-4">
                        <a href="{{ route('users.show', $post->user) }}" class="text-blue-600 hover:text-blue-800">{{ $post->user->name }}</a>
                    </div>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-500">Posted {{ $post->created_at->diffForHumans() }}</span>
                        <div class="flex space-x-2">
                            <button id="like-button-{{ $post->id }}" 
                                onclick="toggleLike({{ $post->id }})" 
                                class="text-{{ $post->likes()->where('user_id', Auth::id())->exists() ? 'red-600' : 'gray-400' }} hover:text-red-600">
                                <i class="fas fa-heart"></i> 
                                <span id="likes-count-{{ $post->id }}">{{ $post->likes()->count() }}</span>
                            </button>
                            <span class="text-gray-500">
                                <i class="fas fa-comment"></i> <span id="comments-count">{{ $post->comments()->count() }}</span>
                            </span>
                        </div>
                    </div>
                    <div class="flex justify-center items-center space-x-4 mb-4">
                        @if(Auth::id() === $post->user_id)
                            <a href="{{ route('posts.edit', $post) }}" 
                               class="text-purple-600 hover:text-pink-600 px-4">
                                Edit Post
                            </a>
                            <form action="{{ route('posts.destroy', $post) }}" 
                                  method="POST" 
                                  class="inline" 
                                  onsubmit="return confirm('Are you sure you want to delete this post?');">
                                @csrf
                                @method('DELETE')
                                <button type="button" 
                                        onclick="deletePost({{ $post->id }})" 
                                        class="text-red-600 hover:text-red-700 px-4">
                                    Delete Post
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('home') }}" 
                           class="text-purple-600 hover:text-pink-600 px-4">
                            Back to Feed
                        </a>
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="p-6 bg-gray-50" id="comments">
                    <h2 class="text-2xl font-bold mb-4">Comments</h2>
                    <div id="comments-list">
                        @foreach($post->comments as $comment)
                            <div class="mb-4 p-4 bg-white rounded-lg shadow" id="comment-{{ $comment->id }}">
                                <div class="comment-content">
                                    <p class="text-gray-700">{{ $comment->content }}</p>
                                    <span class="text-gray-500 text-sm">{{ $comment->user->name }} - {{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                @if(Auth::id() === $comment->user_id)
                                    <div class="flex space-x-2 mt-2">
                                        <button onclick="editComment({{ $comment->id }})" class="text-blue-500 hover:text-blue-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button onclick="deleteComment({{ $comment->id }})" class="text-red-500 hover:text-red-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="edit-form hidden mt-2">
                                        <textarea id="edit-content-{{ $comment->id }}" rows="2" class="w-full p-2 border rounded-lg">{{ $comment->content }}</textarea>
                                        <div class="flex space-x-2 mt-2">
                                            <button onclick="updateComment({{ $comment->id }})" 
                                                    class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition duration-300 ease-in-out transform hover:-translate-y-0.5">
                                                Update
                                            </button>
                                            <button onclick="cancelEdit({{ $comment->id }})" 
                                                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition duration-300 ease-in-out">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Add Comment Form -->
                    <form id="comment-form-{{ $post->id }}" onsubmit="addComment(event, {{ $post->id }})" class="mt-4">
                        @csrf
                        <textarea name="content" rows="3" class="w-full p-2 border rounded-lg" placeholder="Add a comment..."></textarea>
                        <button type="submit" class="mt-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600">Post Comment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
    .comment-content {
        transition: opacity 0.3s ease-in-out;
    }

    .edit-form {
        transition: opacity 0.3s ease-in-out;
    }

    .hidden {
        display: none;
    }

    button svg {
        transition: transform 0.2s ease-in-out;
    }

    button:hover svg {
        transform: scale(1.1);
    }
    </style>

    <script>
    function toggleLike(postId) {
        fetch(`/posts/${postId}/like`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            // Update the likes count in the view post
            document.getElementById(`likes-count-${postId}`).innerText = data.likes_count;

            // Update the likes count in the home page (if applicable)
            const homeLikesCountElement = document.getElementById(`home-likes-count-${postId}`);
            if (homeLikesCountElement) {
                homeLikesCountElement.innerText = data.likes_count;
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function addComment(event, postId) {
        event.preventDefault();
        const form = document.getElementById(`comment-form-${postId}`);
        const formData = new FormData(form);
        
        fetch(`/posts/${postId}/comments`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            const commentElement = document.createElement('div');
            commentElement.classList.add('mb-4', 'p-4', 'bg-white', 'rounded-lg', 'shadow');
            commentElement.id = `comment-${data.id}`;
            
            const html = `
                <div class="comment-content">
                    <p class="text-gray-700">${data.content}</p>
                    <span class="text-gray-500 text-sm">You - just now</span>
                </div>
                <div class="flex space-x-2 mt-2">
                    <button onclick="editComment(${data.id})" class="text-blue-500 hover:text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button onclick="deleteComment(${data.id})" class="text-red-500 hover:text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>`;
                
            commentElement.innerHTML = html;
            document.getElementById('comments-list').appendChild(commentElement);
            form.reset();
        })
        .catch(error => console.error('Error:', error));
    }

    function editComment(commentId) {
        const commentDiv = document.getElementById(`comment-${commentId}`);
        const contentDiv = commentDiv.querySelector('.comment-content');
        const editForm = commentDiv.querySelector('.edit-form');
        
        contentDiv.classList.add('hidden');
        editForm.classList.remove('hidden');
    }

    function updateComment(commentId) {
        const content = document.getElementById(`edit-content-${commentId}`).value;
        
        fetch(`/comments/${commentId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ content })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const commentDiv = document.getElementById(`comment-${commentId}`);
                const contentDiv = commentDiv.querySelector('.comment-content');
                const editForm = commentDiv.querySelector('.edit-form');
                
                contentDiv.querySelector('p').textContent = data.content;
                contentDiv.classList.remove('hidden');
                editForm.classList.add('hidden');
                
                // Update timestamp if provided
                const timestampSpan = contentDiv.querySelector('span');
                if (data.updated_at) {
                    timestampSpan.textContent = `${data.user_name} - ${data.updated_at}`;
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update comment');
        });
    }

    function cancelEdit(commentId) {
        const commentDiv = document.getElementById(`comment-${commentId}`);
        const contentDiv = commentDiv.querySelector('.comment-content');
        const editForm = commentDiv.querySelector('.edit-form');
        
        contentDiv.classList.remove('hidden');
        editForm.classList.add('hidden');
    }

    function deleteComment(commentId) {
        if (!confirm('Are you sure you want to delete this comment?')) {
            return;
        }

        fetch(`/comments/${commentId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const commentElement = document.getElementById(`comment-${commentId}`);
            commentElement.style.transition = 'opacity 0.3s ease-out';
            commentElement.style.opacity = '0';
            
            setTimeout(() => {
                commentElement.remove();
                const commentsCount = document.getElementById('comments-count');
                commentsCount.textContent = parseInt(commentsCount.textContent) - 1;
            }, 300);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete comment');
        });
    }

    function deletePost(postId) {
        if (confirm('Are you sure you want to delete this post? This action cannot be undone.')) {
            // Create a form dynamically
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/posts/${postId}`;
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            
            // Add method spoofing for DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            // Append inputs to form
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            
            // Append form to document and submit
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
    <script>
function toggleLike(postId) {
    fetch(`/posts/${postId}/like`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        // Update the likes count in the view post
        document.getElementById(`likes-count-${postId}`).innerText = data.likes_count;

        // Update the likes count in the home page (if applicable)
        const homeLikesCountElement = document.getElementById(`home-likes-count-${postId}`);
        if (homeLikesCountElement) {
            homeLikesCountElement.innerText = data.likes_count;
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
</x-app-layout>
