@extends('layouts.app')

@section('title', 'Главная')

@section('content')
    <!-- Hero Section -->
    <section class="relative overflow-hidden rounded-3xl bg-blue-600 text-white mb-16">
        <div class="relative z-10 py-20 px-8 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 animate-pulse">
                Там где ты катался,<br>я катамараны воровал
            </h1>
            <p class="text-xl mb-8 opacity-90">Лучший ледовый каток в городе</p>
            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <a href="{{ route('booking.create' ) }}" class="bg-white text-blue-600 px-8 py-4 rounded-xl font-semibold hover:bg-gray-100 transition-all duration-300">
                    Забронировать коньки
                </a>
                <button onclick="openTicketModal()" class="bg-blue-500 text-white px-8 py-4 rounded-xl font-semibold hover:bg-blue-400 transition-all duration-300">
                    Купить билет
                </button>
            </div>
        </div>
        <!-- Decorative elements -->
        <div class="absolute top-0 left-0 w-64 h-64 bg-blue-500 rounded-full filter blur-3xl opacity-30"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-blue-400 rounded-full filter blur-3xl opacity-30"></div>
    </section>

    <!-- Quote from Shaman Section -->
    <section class="mb-16">
        <div class="max-w-5xl mx-auto bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-200">
            <div class="flex flex-col md:flex-row">
                <!-- Фото Шамана - 60% -->
                <div class="md:w-3/5 bg-blue-100 flex items-stretch">
                    <img src="./media/шаманчик.png" alt="Шаман" class="w-full h-full object-cover">
                </div>
                <!-- Текст цитаты - 40% -->
                <div class="md:w-2/5 p-8 md:p-10 text-gray-800 flex flex-col justify-center">
                    <svg class="w-10 h-10 text-blue-300 mb-3" fill="currentColor" viewBox="0 0 32 32">
                        <path d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z" />
                    </svg>
                    <p class="text-xl md:text-2xl font-light italic mb-3 text-gray-700">
                        "Ну что родные, РОССИЯ!"
                    </p>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-base">
                            Ш
                        </div>
                        <div class="ml-2">
                            <p class="font-bold text-gray-800 text-sm">ШАМАН</p>
                            <p class="text-xs text-gray-500">Заслуженный артист</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Photo Gallery Section -->
    <section class="mb-16">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Атмосфера нашего катка</h2>
        <div class="grid md:grid-cols-3 gap-6">
            <!-- Фото 1 -->
            <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500">
                <img src="./media/котак.jpg" alt="Вечернее катание" class="w-full h-64 object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div class="absolute bottom-0 left-0 right-0 p-6">
                        <p class="text-white font-bold text-xl mb-1">Вечернее катание</p>
                        <p class="text-white/80 text-sm">Каждую пятницу и субботу с 20:00 до 23:00</p>
                    </div>
                </div>
            </div>

            <!-- Фото 2 -->
            <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500">
                <img src="./media/фигурное.webp" alt="Школа фигурного катания" class="w-full h-64 object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div class="absolute bottom-0 left-0 right-0 p-6">
                        <p class="text-white font-bold text-xl mb-1">Школа фигурного катания</p>
                        <p class="text-white/80 text-sm">Занятия для детей от 4 лет и взрослых</p>
                    </div>
                </div>
            </div>

            <!-- Фото 3 -->
            <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500">
                <img src="./media/хоккей.webp" alt="Хоккейные тренировки" class="w-full h-64 object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <div class="absolute bottom-0 left-0 right-0 p-6">
                        <p class="text-white font-bold text-xl mb-1">Хоккейные тренировки</p>
                        <p class="text-white/80 text-sm">Любительская лига, открытый набор</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Prices Section -->
    <section id="prices" class="mb-16">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Наши цены</h2>
        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <!-- Ticket Card -->
            <div class="bg-white rounded-2xl p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
                <div class="text-center">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Входной билет</h3>
                    <p class="text-4xl font-bold text-blue-600 mb-4">300₽</p>
                    <p class="text-gray-600 mb-6">Оплачивается один раз, доступ на весь день</p>
                    <button onclick="openTicketModal()" class="w-full bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition-all duration-300">
                        Купить билет
                    </button>
                </div>
            </div>

            <!-- Skates Card -->
            <div class="bg-white rounded-2xl p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
                <div class="text-center">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-2">Аренда коньков</h3>
                    <p class="text-4xl font-bold text-blue-600 mb-4">150₽/час</p>
                    <p class="text-gray-600 mb-6">Профессиональные коньки, все размеры</p>
                    <a href="{{ route('booking.create') }}" class="block w-full bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition-all duration-300">
                        Забронировать
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Schedule Section -->
    <section class="mb-16" id="schedule">
        <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Расписание работы</h2>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                <!-- Будни -->
                <div class="p-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <span class="w-1 h-8 bg-blue-600 rounded-full mr-3"></span>
                        Будние дни
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Понедельник - Пятница</span>
                            <span class="font-bold text-blue-600">10:00 - 22:00</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Утреннее катание</span>
                            <span class="text-gray-700">10:00 - 15:00</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Вечернее катание</span>
                            <span class="text-gray-700">17:00 - 22:00</span>
                        </div>
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <span class="font-bold">Технический перерыв:</span> 15:00 - 17:00 (заливка льда)
                            </p>
                        </div>
                    </div>
                </div>
                <!-- Выходные -->
                <div class="p-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                        <span class="w-1 h-8 bg-purple-600 rounded-full mr-3"></span>
                        Выходные и праздники
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Суббота - Воскресенье</span>
                            <span class="font-bold text-purple-600">09:00 - 23:00</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Утреннее катание</span>
                            <span class="text-gray-700">09:00 - 16:00</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Вечернее катание с DJ</span>
                            <span class="text-gray-700">18:00 - 23:00</span>
                        </div>
                        <div class="mt-4 p-3 bg-purple-50 rounded-lg">
                            <p class="text-sm text-purple-800">
                                <span class="font-bold">Ночное катание:</span> 23:00 - 01:00 (по предварительной записи)
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Buy Ticket Modal -->
    <div id="ticketModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">Купить билет</h3>
                <button onclick="closeTicketModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('ticket.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ФИО</label>
                    <input type="text" name="full_name" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-600" placeholder="Иванов Иван Иванович">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Телефон</label>
                    <input type="tel" name="phone" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-600 phone-mask" placeholder="+7 (___) ___-__-__">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email (необязательно)</label>
                    <input type="email" name="email" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-600" placeholder="ivan@example.com">
                </div>
                
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-lg font-semibold text-blue-800">Сумма: 300₽</p>
                </div>
                
                <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition-all duration-300">
                    Перейти к оплате
                </button>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .8; }
    }
    
    .aspect-w-16 {
        position: relative;
        padding-bottom: 75%;
    }
    
    .aspect-h-12 {
        height: 0;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/imask"></script>
<script>
    // Phone mask
    document.querySelectorAll('.phone-mask').forEach(function(element) {
        IMask(element, {
            mask: '+{7} (000) 000-00-00'
        });
    });

    // Modal functions
    function openTicketModal() {
        document.getElementById('ticketModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeTicketModal() {
        document.getElementById('ticketModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('ticketModal');
        if (event.target == modal) {
            closeTicketModal();
        }
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeTicketModal();
        }
    });
</script>
@endpush