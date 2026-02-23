@extends('admin.layouts.admin')

@section('title', 'Дашборд')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-200">
        <div class="text-3xl font-bold text-black">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} ₽</div>
        <div class="text-gray-600 text-sm mt-1">Общая выручка</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-200">
        <div class="text-3xl font-bold text-black">{{ $stats['total_bookings'] }}</div>
        <div class="text-gray-600 text-sm mt-1">Всего бронирований</div>
        <div class="text-xs text-gray-500 mt-1">{{ $stats['paid_bookings'] }} оплачено</div>
        <div class="text-xs text-gray-500">{{ $stats['active_bookings'] }} активных</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-200">
        <div class="text-3xl font-bold text-black">{{ $stats['total_tickets'] }}</div>
        <div class="text-gray-600 text-sm mt-1">Всего билетов</div>
        <div class="text-xs text-gray-500 mt-1">{{ $stats['used_tickets'] }} использовано</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-200">
        <div class="text-3xl font-bold text-black">{{ $stats['available_skates'] }}/{{ $stats['total_skates'] }}</div>
        <div class="text-gray-600 text-sm mt-1">Коньков в наличии</div>
        @php
            $percent = $stats['total_skates'] > 0 ? round(($stats['available_skates'] / $stats['total_skates']) * 100) : 0;
        @endphp
        <div class="w-full bg-gray-200 h-2 mt-3 rounded-full overflow-hidden">
            <div class="bg-black h-2 rounded-full" style="width: {{ $percent }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-500 mt-1">
            <span>{{ $percent }}% свободно</span>
            <span>{{ $stats['in_use_skates'] }} в аренде</span>
        </div>
    </div>
</div>

<!-- Остальная часть дашборда без изменений -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Bookings -->
    <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-200">
        <h3 class="text-lg font-bold mb-4 text-black">Последние бронирования</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Клиент</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Часы</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Сумма</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recent_bookings as $booking)
                    <tr>
                        <td class="px-4 py-2 text-gray-800">{{ $booking->full_name }}</td>
                        <td class="px-4 py-2 text-gray-800">{{ $booking->hours }}</td>
                        <td class="px-4 py-2 text-gray-800">{{ number_format($booking->total_amount, 0, ',', ' ') }} ₽</td>
                        <td class="px-4 py-2">
                            @if($booking->payment_status == 'paid')
                                <span class="px-2 py-1 bg-gray-800 text-white rounded-full text-xs">Оплачено</span>
                            @else
                                <span class="px-2 py-1 bg-gray-200 text-gray-800 rounded-full text-xs">Ожидание</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-center text-gray-500">Нет бронирований</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-right">
            <a href="{{ route('admin.bookings.index') }}" class="text-black hover:text-gray-600 text-sm border-b border-black pb-0.5">Все бронирования →</a>
        </div>
    </div>

    <!-- Recent Tickets -->
    <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-200">
        <h3 class="text-lg font-bold mb-4 text-black">Последние билеты</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Клиент</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Сумма</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Дата</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($recent_tickets as $ticket)
                    <tr>
                        <td class="px-4 py-2 text-gray-800">{{ $ticket->full_name }}</td>
                        <td class="px-4 py-2 text-gray-800">{{ number_format($ticket->amount, 0, ',', ' ') }} ₽</td>
                        <td class="px-4 py-2">
                            @if($ticket->payment_status == 'paid')
                                <span class="px-2 py-1 bg-gray-800 text-white rounded-full text-xs">Оплачено</span>
                            @else
                                <span class="px-2 py-1 bg-gray-200 text-gray-800 rounded-full text-xs">Ожидание</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-gray-800">{{ $ticket->created_at->format('d.m.Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-center text-gray-500">Нет билетов</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-right">
            <a href="{{ route('admin.tickets.index') }}" class="text-black hover:text-gray-600 text-sm border-b border-black pb-0.5">Все билеты →</a>
        </div>
    </div>
</div>
@endsection