<div class="flex {{ $message->sender_id === auth()->id() ? 'flex-row-reverse' : 'flex-row' }} items-end space-x-2">
    <div class="flex-shrink-0 {{ $message->sender_id === auth()->id() ? 'ml-2' : 'mr-2' }}">
        <img class="h-8 w-8 rounded-full object-cover" 
             src="{{ $message->sender->profile_picture ? asset('storage/profile_pictures/' . $message->sender->profile_picture) : asset('images/default-avatar.png') }}" 
             alt="{{ $message->sender->name }}">
    </div>
    <div class="{{ $message->sender_id === auth()->id() ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }} rounded-lg px-4 py-2 max-w-sm break-words">
        <p class="text-sm font-medium mb-1">{{ $message->sender->name }}</p>
        <p>{{ $message->content }}</p>
        <span class="text-xs {{ $message->sender_id === auth()->id() ? 'text-blue-100' : 'text-gray-500' }} block mt-1">
            {{ $message->created_at->format('g:i A') }}
            @if($message->read_at && $message->sender_id === auth()->id())
                · Read
            @endif
        </span>
    </div>
</div>
