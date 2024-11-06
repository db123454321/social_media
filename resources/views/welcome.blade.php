<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Welcome to Pinstagram</title>
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400,600,700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
        
        <style>
            body {
                font-family: 'Nunito', sans-serif;
                margin: 0;
                padding: 0;
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                background: linear-gradient(135deg, #f687b3, #818cf8);
                animation: gradientAnimation 15s ease infinite;
                background-size: 400% 400%;
            }

            @keyframes gradientAnimation {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            .container {
                text-align: center;
                background: rgba(255, 255, 255, 0.95);
                padding: 3rem;
                border-radius: 1rem;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                max-width: 400px;
                width: 90%;
                backdrop-filter: blur(10px);
            }

            .logo {
                font-family: 'Dancing Script', cursive;
                font-size: 4rem;
                font-weight: 700;
                margin-bottom: 1rem;
                background: linear-gradient(45deg, #ec4899, #8b5cf6);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .tagline {
                font-size: 1.2rem;
                color: #666;
                margin-bottom: 2rem;
                line-height: 1.5;
            }

            .button-group {
                display: flex;
                flex-direction: column;
                gap: 1rem;
                margin-top: 2rem;
            }

            .button {
                padding: 0.75rem 2rem;
                border-radius: 0.5rem;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
                position: relative;
                overflow: hidden;
            }

            .primary-button {
                background: linear-gradient(45deg, #ec4899, #8b5cf6);
                color: white;
            }

            .secondary-button {
                background: white;
                color: #ec4899;
                border: 2px solid #ec4899;
            }

            .button:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .primary-button:hover {
                background: linear-gradient(45deg, #8b5cf6, #ec4899);
            }

            .secondary-button:hover {
                background: linear-gradient(45deg, #ec4899, #8b5cf6);
                color: white;
                border-color: transparent;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1 class="logo">Pinstagram</h1>
            <p class="tagline">Share your moments, connect with friends, and discover amazing stories through the perfect blend of photos and inspiration.</p>
            <div class="button-group">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="button primary-button">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="button primary-button">Welcome to Pinstagram</a>
                    @endauth
                @endif
            </div>
        </div>
    </body>
</html>
