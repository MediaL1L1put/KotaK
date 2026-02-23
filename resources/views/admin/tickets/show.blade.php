@extends('admin.layouts.admin')

@section('title', 'Просмотр билета #' . $ticket->id)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Билет #{{ $ticket->id }}</h1>
            <a href="{{ route('admin.tickets.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Назад
            </a>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">ФИО</h3>
                <p class="text-lg font-semibold">{{ $ticket->full_name }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Телефон</h3>
                <p class="text-lg">{{ $ticket->phone }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Email</h3>
                <p class="text-lg">{{ $ticket->email ?: '—' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Сумма</h3>
                <p class="text-2xl font-bold text-blue-600">{{ number_format($ticket->amount, 0, ',', ' ') }}₽</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Статус оплаты</h3>
                @if($ticket->payment_status == 'paid')
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Оплачено</span>
                @else
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">Ожидание</span>
                @endif
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Использован</h3>
                @if($ticket->is_used)
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                        {{ $ticket->used_at->format('d.m.Y H:i') }}
                    </span>
                @else
                    <span class="text-gray-400">Нет</span>
                @endif
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">ID платежа</h3>
                <p class="text-sm font-mono">{{ $ticket->payment_id ?: '—' }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Дата создания</h3>
                <p class="text-sm">{{ $ticket->created_at->format('d.m.Y H:i:s') }}</p>
            </div>
        </div>

        @if(!$ticket->is_used && $ticket->payment_status == 'paid')
        <div class="border-t pt-6">
            <form action="{{ route('admin.tickets.mark-used', $ticket) }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-green-700 transition" onclick="return confirm('Отметить билет как использованный?')">
                    Отметить вход посетителя
                </button>
            </form>
        </div>
        @endif
        
        @if($ticket->is_used || $ticket->payment_status == 'cancelled')
        <div class="border-t pt-6 mt-4">
            <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST"
                  onsubmit="return confirm('Удалить этот билет из базы данных? Это действие необратимо.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-gray-600 text-white px-6 py-2 rounded-xl hover:bg-gray-700 transition">
                    Удалить запись о билете
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection