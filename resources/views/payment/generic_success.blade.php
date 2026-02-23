@extends('layouts.app')

@section('title', 'Обработка платежа')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12 text-center">
        <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
            Платеж обрабатывается
        </h1>
        
        <div class="alert alert-info bg-blue-50 p-6 rounded-lg mb-6">
            <h5 class="font-semibold text-lg mb-2">Что происходит:</h5>
            <p class="mb-2">{{ $message }}</p>
            <p class="mb-0">Статус платежа будет обновлен автоматически в течение нескольких минут.</p>
        </div>
        
        <p class="text-gray-600 mb-6">
            Обычно обработка занимает несколько минут. Вы можете проверить статус в истории платежей.
        </p>
        
        <div class="flex flex-col md:flex-row gap-4 justify-center">
            <a href="{{ route('home') }}" class="btn-primary">
                На главную
            </a>
            <a href="{{ route('payment.index') }}" class="btn-secondary">
                История платежей
            </a>
        </div>
    </div>
</div>
@endsection