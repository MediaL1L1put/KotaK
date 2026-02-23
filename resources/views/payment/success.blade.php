@extends('layouts.app')

@section('title', 'Оплата прошла успешно')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
        <!-- Иконка успеха -->
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 text-center">
            Спасибо за покупку!
        </h1>
        
        <p class="text-xl text-gray-600 mb-8 text-center">
            {{ $message }}
        </p>

        <div class="bg-blue-50 rounded-lg p-6 mb-8">
            <h2 class="font-semibold text-lg mb-3">Детали платежа:</h2>
            <div class="space-y-2">
                <p><span class="text-gray-600">Сумма:</span> <span class="font-bold">{{ number_format($amount, 0, ',', ' ') }}₽</span></p>
                <p><span class="text-gray-600">ID платежа:</span> <span class="font-mono text-sm">{{ $payment->payment_id }}</span></p>
                <p><span class="text-gray-600">Дата:</span> {{ now()->format('d.m.Y H:i') }}</p>
            </div>
        </div>

        <div class="bg-green-50 rounded-lg p-6 mb-8">
            <h2 class="font-semibold text-lg mb-3">Что дальше?</h2>
            <ul class="space-y-2 text-gray-700">
                <li class="flex items-start">
                    <span class="text-green-500 mr-2">✓</span>
                    @if($type === 'ticket')
                        Ваш билет действителен на весь день
                    @else
                        Ваше бронирование подтверждено
                    @endif
                </li>
                <li class="flex items-start">
                    <span class="text-green-500 mr-2">✓</span>
                    Покажите этот экран на входе
                </li>
                <li class="flex items-start">
                    <span class="text-green-500 mr-2">✓</span>
                    Приятного катания!
                </li>
            </ul>
        </div>

        <div class="flex flex-col md:flex-row gap-4 justify-center">
            <a href="{{ route('home') }}" class="btn-primary">
                Вернуться на главную
            </a>
            @if($type === 'booking')
                <a href="{{ route('booking.create') }}" class="btn-secondary">
                    Новое бронирование
                </a>
            @endif
        </div>
    </div>
</div>
@endsection