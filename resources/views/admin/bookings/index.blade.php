@extends('admin.layouts.admin')

@section('title', 'Бронирования')

@section('content')
<div class="bg-white rounded-2xl shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Список бронирований</h1>
        
        <!-- Фильтры и действия -->
        <div class="flex gap-2">
            <form method="GET" class="flex gap-2">
                <select name="status" class="px-4 py-2 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-600" onchange="this.form.submit()">
                    <option value="">Все статусы</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Ожидают оплаты</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Оплачены</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Завершены</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Отменены</option>
                </select>
                
                <input type="date" name="date" value="{{ request('date') }}" class="px-4 py-2 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-600" onchange="this.form.submit()">
                
                @if(request('status') || request('date'))
                    <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                        Сбросить
                    </a>
                @endif
            </form>
            
            <!-- Кнопка очистки всех бронирований -->
            <form action="{{ route('admin.bookings.clear-all') }}" method="POST" 
                  onsubmit="return confirm('ВНИМАНИЕ! Вы уверены, что хотите удалить ВСЕ бронирования? Это действие необратимо. Коньки будут возвращены в базу.')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition">
                    Очистить все бронирования
                </button>
            </form>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ФИО</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Телефон</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Часы</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Коньки</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Сумма</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Дата</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">#{{ $booking->id }}</td>
                    <td class="px-6 py-4">{{ $booking->full_name }}</td>
                    <td class="px-6 py-4">{{ $booking->phone }}</td>
                    <td class="px-6 py-4">{{ $booking->hours }}</td>
                    <td class="px-6 py-4">
                        @if($booking->has_own_skates)
                            <span class="text-gray-500">Свои коньки</span>
                        @elseif($booking->skate)
                            {{ $booking->skate->model->name ?? 'Коньки' }} ({{ $booking->skate_size }})
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 font-semibold">{{ number_format($booking->total_amount, 0, ',', ' ') }}₽</td>
                    <td class="px-6 py-4">
                        @if($booking->payment_status == 'paid')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Оплачено</span>
                        @elseif($booking->payment_status == 'completed')
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">Завершено</span>
                        @elseif($booking->payment_status == 'cancelled')
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Отменено</span>
                        @else
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Ожидание</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $booking->created_at->format('d.m.Y H:i') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-600 hover:text-blue-800" title="Просмотр">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            
                            @if($booking->payment_status == 'paid')
                            <form action="{{ route('admin.bookings.complete', $booking) }}" method="POST" class="inline" 
                                  onsubmit="return confirm('Завершить бронирование? Коньки будут возвращены в базу.')">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800" title="Завершить">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                            </form>
                            
                            <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Отменить бронирование? Коньки будут возвращены в базу.')">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Отменить">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </form>
                            @endif
                            
                            @if($booking->payment_status == 'completed' || $booking->payment_status == 'cancelled')
                            <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Удалить запись о бронировании?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-600 hover:text-gray-800" title="Удалить">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-lg font-medium">Нет бронирований</p>
                            <p class="text-sm">Здесь будут отображаться все бронирования</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex justify-between items-center">
        <div class="text-sm text-gray-600">
            Показано {{ $bookings->firstItem() ?? 0 }} - {{ $bookings->lastItem() ?? 0 }} из {{ $bookings->total() }} бронирований
        </div>
        <div>
            {{ $bookings->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Модальное окно подтверждения массового удаления (на всякий случай) -->
<div id="clearAllModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-red-600">Подтверждение</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="mb-6">
            <p class="text-gray-700 mb-4">Вы действительно хотите удалить <span class="font-bold">ВСЕ</span> бронирования?</p>
            <p class="text-sm text-gray-500">Это действие необратимо. Все данные будут безвозвратно удалены.</p>
            <p class="text-sm text-green-600 mt-2">✓ Коньки будут возвращены в базу</p>
        </div>
        
        <div class="flex gap-4">
            <button onclick="closeModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                Отмена
            </button>
            <form action="{{ route('admin.bookings.clear-all') }}" method="POST" class="flex-1">
                @csrf
                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition">
                    Удалить всё
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openModal() {
    document.getElementById('clearAllModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('clearAllModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('clearAllModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});
</script>
@endpush