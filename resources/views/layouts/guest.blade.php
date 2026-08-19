<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FIT CLUB') }} | Authentication</title>

        <!-- Custom FIT CLUB Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ff5b00'><path d='M13 10V3L4 14h7v7l9-11h-7z'/></svg>">

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Outfit', sans-serif;
                background-color: #181a20;
                color: #ffffff;
            }
            .neon-accent {
                color: #ff5b00;
                text-shadow: 0 0 15px rgba(255, 91, 0, 0.4);
            }
            .bg-neon-gradient {
                background: linear-gradient(135deg, #ff5b00 0%, #ff2a00 100%);
            }
            .glass-card {
                background: rgba(255, 255, 255, 0.03);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }
            .neon-blur-circle {
                position: absolute;
                width: 300px;
                height: 300px;
                border-radius: 50%;
                background: rgba(255, 91, 0, 0.12);
                filter: blur(80px);
                z-index: 0;
            }
        </style>
    </head>
    <body class="antialiased text-white">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden bg-[#181a20]">
            <!-- Decorative circles -->
            <div class="neon-blur-circle top-10 -left-20"></div>
            <div class="neon-blur-circle bottom-10 -right-20"></div>

            <div class="z-10">
                <a href="/" class="flex items-center gap-2 text-3xl font-black tracking-wider uppercase">
                    <span class="neon-accent">FIT</span><span>CLUB</span>
                </a>
            </div>

            <!-- Login / Register Card -->
            <div class="w-full sm:max-w-md mt-6 px-8 py-6 glass-card shadow-2xl overflow-hidden sm:rounded-2xl z-10">
                {{ $slot }}
            </div>
        </div>

        <!-- Universal Enterprise Double-Submit Prevention & Form Debounce -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('form').forEach(function (form) {
                    form.addEventListener('submit', function (e) {
                        if (form.dataset.submitting === 'true') {
                            e.preventDefault();
                            return false;
                        }
                        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                        if (submitBtn) {
                            form.dataset.submitting = 'true';
                            submitBtn.disabled = true;
                            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                            const originalHtml = submitBtn.innerHTML;
                            submitBtn.innerHTML = 'Processing...';
                            setTimeout(function () {
                                form.dataset.submitting = 'false';
                                submitBtn.disabled = false;
                                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                                submitBtn.innerHTML = originalHtml;
                            }, 8000);
                        }
                    });
                });
            });
        </script>
    </body>
</html>
