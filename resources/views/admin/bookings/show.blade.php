@extends('admin.layouts.admin')

@section('title', 'Просмотр бронирования #' . $booking->id)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Бронирование #{{ $booking->id }}</h1>
            <a href="{{ route('admin.bookings.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Назад
            </a>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">ФИО</h3>
                <p class="text-lg font-semibold">{{ $booking->full_name }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Телефон</h3>
                <p class="text-lg">{{ $booking->phone }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Количество часов</h3>
                <p class="text-lg">{{ $booking->hours }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Коньки</h3>
                @if($booking->has_own_skates)
                    <p class="text-lg">Свои коньки</p>
                @elseif($booking->skate)
                    <p class="text-lg">{{ $booking->skate->model->name ?? 'Коньки' }}</p>
                    <p class="text-sm text-gray-600">Размер: {{ $booking->skate_size }}</p>
                @else
                    <p class="text-lg">-</p>
                @endif
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Сумма</h3>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($booking->total_amount, 0, ',', ' ') }}₽</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Статус</h3>
                @if($booking->payment_status == 'paid')
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Оплачено</span>
                @elseif($booking->payment_status == 'completed')
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">Завершено</span>
                @elseif($booking->payment_status == 'cancelled')
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">Отменено</span>
                @else
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">Ожидание</span>
                @endif
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">ID платежа</h3>
                <p class="text-sm font-mono">{{ $booking->payment_id ?: '—' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Дата создания</h3>
                <p class="text-sm">{{ $booking->created_at->format('d.m.Y H:i:s') }}</p>
            </div>
            @if($booking->payment_status == 'completed')
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Дата завершения</h3>
                <p class="text-sm">{{ $booking->updated_at->format('d.m.Y H:i:s') }}</p>
            </div>
            @endif
        </div>

        @if($booking->payment_status == 'paid')
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold mb-4">Управление бронированием</h3>
            <div class="flex gap-4">
                <form action="{{ route('admin.bookings.complete', $booking) }}" method="POST"
                      onsubmit="return confirm('Завершить бронирование? Коньки будут возвращены в базу.')">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition">
                        Завершить бронирование
                    </button>
                </form>
                
                <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" 
                      onsubmit="return confirm('Вы уверены, что хотите отменить бронирование? Коньки будут возвращены в базу.')">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-xl hover:bg-red-700 transition">
                        Отменить бронирование
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($booking->payment_status == 'pending')
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold mb-4">Изменить статус</h3>
            <div class="flex gap-4">
                <form action="{{ route('admin.bookings.status', $booking) }}" method="POST">
                    @csrf
                    <input type="hidden" name="payment_status" value="paid">
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-xl hover:bg-green-700 transition" onclick="return confirm('Отметить как оплаченное?')">
                        Отметить как оплаченное
                    </button>
                </form>
                <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-xl hover:bg-red-700 transition" onclick="return confirm('Отменить бронирование?')">
                        Отменить
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($booking->payment_status == 'completed')
        <div class="border-t pt-6">
            <div class="bg-blue-50 p-4 rounded-xl">
                <p class="text-blue-800">
                    <span class="font-bold">✓</span> Бронирование успешно завершено
                </p>
                @if(!$booking->has_own_skates && $booking->skate_id)
                    <p class="text-sm text-blue-600 mt-1">Коньки возвращены в базу</p>
                @endif
            </div>
        </div>
        @endif

        @if($booking->payment_status == 'cancelled')
        <div class="border-t pt-6">
            <div class="bg-red-50 p-4 rounded-xl">
                <p class="text-red-800">
                    <span class="font-bold">✗</span> Бронирование отменено
                </p>
                @if(!$booking->has_own_skates && $booking->skate_id)
                    <p class="text-sm text-red-600 mt-1">Коньки возвращены в базу</p>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Кнопка удаления конкретного бронирования -->
        @if($booking->payment_status == 'completed' || $booking->payment_status == 'cancelled')
        <div class="border-t pt-6 mt-4">
            <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST"
                  onsubmit="return confirm('Удалить это бронирование из базы данных? Это действие необратимо.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-gray-600 text-white px-6 py-2 rounded-xl hover:bg-gray-700 transition">
                    Удалить запись о бронировании
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection