<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                background: linear-gradient(150deg, #FFF0F5 6%, #E6E6FA 55%, #FFFFFF 100%);
                min-height: 100vh;
            }

            .instagram-gradient {
                background: -webkit-linear-gradient(45deg, #405DE6, #5851DB, #833AB4, #C13584, #E1306C, #FD1D1D);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                display: inline-block;
            }

            /* Hide scrollbar but keep functionality */
            .scrollbar-thin::-webkit-scrollbar {
                width: 6px;
            }
            
            .scrollbar-thin::-webkit-scrollbar-track {
                background: transparent;
            }
            
            .scrollbar-thin::-webkit-scrollbar-thumb {
                background-color: rgba(156, 163, 175, 0.5);
                border-radius: 3px;
            }
            
            .scrollbar-thin::-webkit-scrollbar-thumb:hover {
                background-color: rgba(156, 163, 175, 0.8);
            }
        </style>

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

        <!-- Custom CSS -->
        <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen flex">
            @include('layouts.sidebar')

            <div class="flex-1 ml-20 lg:ml-64"> <!-- Adjust margin to account for sidebar width -->
                <!-- Main Content -->
                <main class="py-6 px-4 sm:px-6 lg:px-8">
                    <div class="max-w-3xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link btn btn-link">
                    <i class="fas fa-sign-out-alt"></i> {{ __('Log Out') }}
                </button>
            </form>
        @endauth
        <script>
            import Echo from 'laravel-echo';
            import Pusher from 'pusher-js';

            window.Pusher = Pusher;
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: process.env.MIX_PUSHER_APP_KEY,
                cluster: process.env.MIX_PUSHER_APP_CLUSTER,
                forceTLS: true
            });

            // Listen for new messages
            Echo.private(`messages.${recipientId}`)
                .listen('NewMessage', (e) => {
                    appendMessage(
                        e.message.content,
                        e.message.sender,
                        e.message.created_at,
                        false
                    );
                    scrollToBottom();
                });
        </script>
    </body>
</html>
