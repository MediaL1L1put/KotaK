@extends('admin.layouts.admin')

@section('title', 'Добавление коньков')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Добавить новые коньки</h2>

        <form action="{{ route('admin.skates.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Модель</label>
                <select name="skate_model_id" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-600" required>
                    <option value="">Выберите модель</option>
                    @foreach($models as $model)
                        <option value="{{ $model->id }}" {{ old('skate_model_id') == $model->id ? 'selected' : '' }}>
                            {{ $model->name }} ({{ $model->brand }})
                        </option>
                    @endforeach
                </select>
                @error('skate_model_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Размер</label>
                <select name="size" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-600" required>
                    <option value="">Выберите размер</option>
                    @foreach(range(35, 48) as $size)
                        <option value="{{ $size }}" {{ old('size') == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
                @error('size')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Количество</label>
                <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" 
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-600" required>
                @error('quantity')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Цена за час (₽)</label>
                <input type="number" name="price_per_hour" value="{{ old('price_per_hour', 150) }}" min="0" step="1"
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-600" required>
                @error('price_per_hour')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition">
                    Сохранить
                </button>
                <a href="{{ route('admin.skates.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>
@endsection