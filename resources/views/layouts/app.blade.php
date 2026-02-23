<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ледовый каток - @yield('title', 'Главная')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        .hover-scale {
            transition: transform 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.02);
        }
        .card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }
        .btn-primary {
            background: #2563eb;
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }
        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgb(37 99 235 / 0.3);
        }
        .btn-secondary {
            background: #e2e8f0;
            color: #1e293b;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: #cbd5e1;
            transform: translateY(-2px);
        }
        .input-field {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.3s ease;
            margin-bottom: 16px;
        }
        .input-field:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgb(37 99 235 / 0.1);
        }
        .input-field.error {
            border-color: #dc2626;
        }
        .error-message {
            color: #dc2626;
            font-size: 14px;
            margin-top: -12px;
            margin-bottom: 16px;
        }
        
        /* Плавная прокрутка для всей страницы */
        html {
            scroll-behavior: smooth;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    <span class="text-xl font-bold text-gray-800">KotaK</span>
                </a>

                <!-- Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-blue-600 transition">Главная</a>
                    <a href="#prices" class="nav-link text-gray-600 hover:text-blue-600 transition" data-target="prices">Цены</a>
                    <a href="#schedule" class="nav-link text-gray-600 hover:text-blue-600 transition" data-target="schedule">Расписание</a>
                    <a href="#contacts" class="nav-link text-gray-600 hover:text-blue-600 transition" data-target="contacts">Контакты</a>
                </div>

                <!-- Buy Ticket Button -->
                <button onclick="openTicketModal()" class="btn-primary">
                    Купить билет
                </button>

                <!-- Mobile menu button -->
                <button class="md:hidden" onclick="toggleMobileMenu()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden md:hidden mt-4">
                <div class="flex flex-col space-y-4">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-blue-600 transition py-2" onclick="closeMobileMenu()">Главная</a>
                    <a href="#prices" class="nav-link text-gray-600 hover:text-blue-600 transition py-2" data-target="prices" onclick="closeMobileMenu()">Цены</a>
                    <a href="#schedule" class="nav-link text-gray-600 hover:text-blue-600 transition py-2" data-target="schedule" onclick="closeMobileMenu()">Расписание</a>
                    <a href="#contacts" class="nav-link text-gray-600 hover:text-blue-600 transition py-2" data-target="contacts" onclick="closeMobileMenu()">Контакты</a>
                    <button onclick="openTicketModal(); closeMobileMenu();" class="btn-primary w-full mt-2">
                        Купить билет
                    </button>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer id="contacts" class="bg-white mt-16 py-8">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h3 class="font-bold text-gray-800 mb-4">KotaK</h3>
                    <p class="text-gray-600 text-sm">Лучший ледовый каток в городе. Профессиональное покрытие, комфортная атмосфера.</p>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 mb-4">Контакты</h3>
                    <p class="text-gray-600 text-sm mb-2">ул. Ледовая, 123</p>
                    <p class="text-gray-600 text-sm mb-2">+7 (999) 123-45-67</p>
                    <p class="text-gray-600 text-sm">info@kotak.ru</p>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 mb-4">Мы в соцсетях</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-blue-600 transition">Telegram</a>
                        <a href="#" class="text-gray-400 hover:text-blue-600 transition">ZapretGram</a>
                        <a href="#" class="text-gray-400 hover:text-blue-600 transition">Telega</a>
                    </div>
                </div>
            </div>
            <div class="text-center text-gray-600 pt-8 border-t border-gray-200">
                <p>&copy; 2026 KotaK. Все права защищены.</p>
                <p class="text-sm mt-2">Там где ты катался, я катамараны воровал</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        function closeMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.add('hidden');
        }

        // Функция для открытия модального окна покупки билета
        function openTicketModal() {
            const modal = document.getElementById('ticketModal');
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        }

        // Функция для закрытия модального окна
        function closeTicketModal() {
            const modal = document.getElementById('ticketModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }

        // Плавная прокрутка к якорям
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link');
            
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetId = this.getAttribute('data-target');
                    const targetElement = document.getElementById(targetId);
                    
                    if (targetElement) {
                        const headerOffset = 80; // Высота шапки с запасом
                        const elementPosition = targetElement.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Обработка якорей в URL при загрузке страницы
            if (window.location.hash) {
                const targetId = window.location.hash.substring(1);
                const targetElement = document.getElementById(targetId);
                
                if (targetElement) {
                    setTimeout(() => {
                        const headerOffset = 80;
                        const elementPosition = targetElement.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }, 100);
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
