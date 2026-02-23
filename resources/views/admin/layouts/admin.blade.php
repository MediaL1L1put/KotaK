<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg">
            <div class="p-6 border-b">
                <h1 class="text-xl font-bold text-gray-800">KotaK Admin</h1>
            </div>
            <nav class="p-4">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 mb-2 rounded-lg hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-600' }}">
                    Дашборд
                </a>
                <a href="{{ route('admin.skates.index') }}" class="block px-4 py-2 mb-2 rounded-lg hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('admin.skates*') ? 'bg-blue-600 text-white' : 'text-gray-600' }}">
                    Коньки
                </a>
                <a href="{{ route('admin.bookings.index') }}" class="block px-4 py-2 mb-2 rounded-lg hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('admin.bookings*') ? 'bg-blue-600 text-white' : 'text-gray-600' }}">
                    Бронирования
                </a>
                <a href="{{ route('admin.tickets.index') }}" class="block px-4 py-2 mb-2 rounded-lg hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('admin.tickets*') ? 'bg-blue-600 text-white' : 'text-gray-600' }}">
                    Билеты
                </a>
                
                <div class="border-t mt-4 pt-4">
                    <a href="{{ route('home') }}" class="block px-4 py-2 mb-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg">
                        На сайт
                    </a>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg">
                            Выйти
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm p-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-700">@yield('title')</h2>
                    <div class="text-sm text-gray-600">
                        {{ now()->addHours(2)->format('d.m.Y H:i') }}
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-6">
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
            </div>
        </main>
    </div>
</body>
</html>