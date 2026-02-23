@extends('layouts.app')

@section('title', 'Бронирование коньков')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="text-center mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Бронирование коньков</h1>
        <p class="text-gray-600">Заполните форму ниже, чтобы забронировать коньки</p>
    </div>

    <div class="card">
        <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
            @csrf
            
            <!-- Personal Information -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Личные данные</h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ФИО</label>
                    <input type="text" 
                           name="full_name" 
                           value="{{ old('full_name') }}"
                           class="input-field @error('full_name') error @enderror" 
                           placeholder="Иванов Иван Иванович"
                           required>
                    @error('full_name')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Телефон</label>
                    <input type="tel" 
                           name="phone" 
                           value="{{ old('phone') }}"
                           class="input-field phone-mask @error('phone') error @enderror" 
                           placeholder="+7 (___) ___-__-__"
                           required>
                    @error('phone')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Skates Selection -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Выбор коньков</h2>
                
                <div class="mb-4">
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" 
                               name="has_own_skates" 
                               value="1" 
                               {{ old('has_own_skates') ? 'checked' : '' }}
                               class="w-5 h-5 text-blue-600"
                               onchange="toggleSkatesSelection()">
                        <span class="text-gray-700">У меня свои коньки</span>
                    </label>
                </div>

                <div id="skatesSelection" style="{{ old('has_own_skates') ? 'display: none;' : '' }}">
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Размер</label>
                            <select name="skate_size" 
                                    id="skateSize"
                                    class="input-field"
                                    onchange="loadAvailableSkates()">
                                <option value="">Выберите размер</option>
                                @foreach(range(35, 45) as $size)
                                    <option value="{{ $size }}" {{ old('skate_size') == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Модель</label>
                            <select name="skate_id" 
                                    id="skateModel"
                                    class="input-field @error('skate_id') error @enderror"
                                    {{ !old('skate_size') ? 'disabled' : '' }}>
                                <option value="">Сначала выберите размер</option>
                            </select>
                            @error('skate_id')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div id="skateInfo" class="hidden bg-blue-50 p-4 rounded-lg mb-4">
                        <p class="text-blue-800">Цена аренды: 150₽/час</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Количество часов</label>
                    <select name="hours" class="input-field @error('hours') error @enderror" onchange="calculateTotal()">
                        @foreach([1,2,3,4] as $hour)
                            <option value="{{ $hour }}" {{ old('hours') == $hour ? 'selected' : '' }}>
                                {{ $hour }} {{ trans_choice('час|часа|часов', $hour) }}
                            </option>
                        @endforeach
                    </select>
                    @error('hours')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Total -->
            <div class="bg-gray-50 p-6 rounded-lg mb-6">
                <div class="flex justify-between mb-2">
                    <span>Входной билет:</span>
                    <span class="font-semibold">300₽</span>
                </div>
                <div class="flex justify-between mb-2" id="skatesCostRow">
                    <span>Аренда коньков:</span>
                    <span class="font-semibold" id="skatesCost">0₽</span>
                </div>
                <div class="border-t pt-2 mt-2">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Итого:</span>
                        <span id="totalAmount">300₽</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-primary w-full text-lg py-4">
                Перейти к оплате
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/imask"></script>
<script>
    // Phone mask
    document.querySelectorAll('.phone-mask').forEach(function(element) {
        IMask(element, {
            mask: '+{7} (000) 000-00-00'
        });
    });

    function toggleSkatesSelection() {
        const checkbox = document.querySelector('input[name="has_own_skates"]');
        const skatesSelection = document.getElementById('skatesSelection');
        const skatesCostRow = document.getElementById('skatesCostRow');
        
        if (checkbox.checked) {
            skatesSelection.style.display = 'none';
            skatesCostRow.style.display = 'none';
        } else {
            skatesSelection.style.display = 'block';
            skatesCostRow.style.display = 'flex';
        }
        
        calculateTotal();
    }

    function loadAvailableSkates() {
        const size = document.getElementById('skateSize').value;
        const modelSelect = document.getElementById('skateModel');
        const skateInfo = document.getElementById('skateInfo');
        
        if (!size) {
            modelSelect.innerHTML = '<option value="">Сначала выберите размер</option>';
            modelSelect.disabled = true;
            skateInfo.classList.add('hidden');
            return;
        }

        fetch(`{{ route('booking.available-skates') }}?size=${size}`)
            .then(response => response.json())
            .then(data => {
                modelSelect.innerHTML = '<option value="">Выберите модель</option>';
                data.forEach(skate => {
                    modelSelect.innerHTML += `<option value="${skate.id}" data-price="${skate.price_per_hour}">${skate.model.name} (в наличии: ${skate.available_quantity})</option>`;
                });
                modelSelect.disabled = false;
                skateInfo.classList.remove('hidden');
            });
    }

    function calculateTotal() {
        const hasOwnSkates = document.querySelector('input[name="has_own_skates"]').checked;
        const hours = document.querySelector('select[name="hours"]').value || 1;
        const skatesCost = hasOwnSkates ? 0 : (hours * 150);
        
        document.getElementById('skatesCost').textContent = skatesCost + '₽';
        
        const total = 300 + skatesCost;
        document.getElementById('totalAmount').textContent = total + '₽';
    }

    // Initial calculation
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();
    });
</script>
@endpush