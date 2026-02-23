@extends('admin.layouts.admin')

@section('title', 'Управление коньками')

@section('content')
<div class="bg-white rounded-2xl shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Управление коньками</h1>
        <a href="{{ route('admin.skates.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Добавить коньки
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Модель</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Размер</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Бренд</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Всего</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Доступно</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Цена/час</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($skates as $skate)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $skate->model->name }}</td>
                    <td class="px-6 py-4">{{ $skate->size }}</td>
                    <td class="px-6 py-4">{{ $skate->model->brand }}</td>
                    <td class="px-6 py-4">{{ $skate->quantity }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $skate->available_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $skate->available_quantity }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $skate->price_per_hour }}₽</td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.skates.edit', $skate) }}" class="text-blue-600 hover:text-blue-800 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('admin.skates.destroy', $skate) }}" method="POST" onsubmit="return confirm('Вы уверены?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $skates->links() }}
    </div>
</div>
@endsection