@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Live Chat Support</h2>
            </div>
            
            <div id="chat-messages" class="h-96 overflow-y-auto p-4 space-y-4">
                @foreach($messages as $message)
                    <div class="flex {{ $message->type === 'user' ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-sm {{ $message->type === 'user' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800' }} rounded-lg px-4 py-2 shadow">
                            <p class="text-sm">{{ $message->message }}</p>
                            <p class="text-xs mt-1 {{ $message->type === 'user' ? 'text-indigo-200' : 'text-gray-500' }}">
                                {{ $message->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-4 border-t border-gray-200">
                <form id="chat-form" class="flex space-x-4">
                    <input type="text" id="message-input" class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Type your message...">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing chat...');
    
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        encrypted: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-Token': '{{ csrf_token() }}'
            }
        }
    });

    // Debug Pusher connection states
    pusher.connection.bind('connecting', () => {
        console.log('Connecting to Pusher...');
    });
    
    pusher.connection.bind('connected', () => {
        console.log('Successfully connected to Pusher');
        console.log('Subscribing to channel: private-chat.{{ auth()->id() }}');
    });
    
    pusher.connection.bind('error', (err) => {
        console.error('Pusher connection error:', err);
    });

    pusher.connection.bind('disconnected', () => {
        console.log('Disconnected from Pusher');
    });

    const channelName = 'private-chat.{{ auth()->id() }}';
    console.log('Attempting to subscribe to:', channelName);
    
    const channel = pusher.subscribe(channelName);

    channel.bind('pusher:subscription_succeeded', () => {
        console.log('Successfully subscribed to channel:', channelName);
    });

    channel.bind('pusher:subscription_error', (error) => {
        console.error('Subscription error:', error);
    });

    channel.bind('App\\Events\\NewChatMessage', function(data) {
        console.log('Received message event:', data);
        const message = data.message;
        const isUser = message.type === 'user';
        
        const messageHtml = `
            <div class="flex ${isUser ? 'justify-end' : 'justify-start'}">
                <div class="max-w-sm ${isUser ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800'} rounded-lg px-4 py-2 shadow">
                    <p class="text-sm">${message.message}</p>
                    <p class="text-xs mt-1 ${isUser ? 'text-indigo-200' : 'text-gray-500'}">Just now</p>
                </div>
            </div>
        `;
        
        chatMessages.insertAdjacentHTML('beforeend', messageHtml);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    });

    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');

    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        try {
            console.log('Sending message:', message);
            const response = await fetch('{{ route('chat.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message: message
                })
            });

            if (!response.ok) {
                const error = await response.json();
                console.error('Error sending message:', error);
                return;
            }

            const data = await response.json();
            console.log('Message sent successfully:', data);
            messageInput.value = '';
            
        } catch (error) {
            console.error('Error:', error);
        }
    });
});
</script>
@endpush
@endsection
