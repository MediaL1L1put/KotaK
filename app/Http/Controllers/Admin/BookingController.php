<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Skate;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with('skate.model');

        if ($request->has('status') && $request->status != '') {
            $query->where('payment_status', $request->status);
        }

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load('skate.model');
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,completed,cancelled'
        ]);

        $oldStatus = $booking->payment_status;
        $newStatus = $request->payment_status;

        // Если бронирование было оплачено и мы его отменяем - возвращаем коньки
        if ($oldStatus == 'paid' && $newStatus == 'cancelled') {
            $this->returnSkatesToStock($booking);
        }

        $booking->update([
            'payment_status' => $newStatus
        ]);

        $message = $newStatus == 'cancelled' 
            ? 'Бронирование отменено, коньки возвращены в базу' 
            : 'Статус бронирования обновлен';

        return redirect()->back()->with('success', $message);
    }

    public function cancel(Booking $booking)
    {
        if ($booking->payment_status == 'cancelled') {
            return redirect()->back()->with('error', 'Бронирование уже отменено');
        }

        if ($booking->payment_status == 'completed') {
            return redirect()->back()->with('error', 'Завершенное бронирование нельзя отменить');
        }

        // Возвращаем коньки в базу если они были арендованы
        $this->returnSkatesToStock($booking);

        $booking->update(['payment_status' => 'cancelled']);

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Бронирование отменено, коньки возвращены в базу');
    }

    public function complete(Booking $booking)
    {
        if ($booking->payment_status != 'paid') {
            return redirect()->back()->with('error', 'Можно завершить только оплаченные бронирования');
        }

        // Возвращаем коньки в базу
        $this->returnSkatesToStock($booking);

        $booking->update(['payment_status' => 'completed']);

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Бронирование завершено, коньки возвращены в базу');
    }

    public function destroy(Booking $booking)
    {
        // Сначала удаляем связанные платежи или обнуляем внешний ключ
        Payment::where('booking_id', $booking->id)->update(['booking_id' => null]);
        
        $booking->delete();
        
        return redirect()->route('admin.bookings.index')
            ->with('success', 'Запись о бронировании удалена');
    }

    public function clearAll(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // Возвращаем все коньки в базу перед удалением
            $activeBookings = Booking::where('payment_status', 'paid')
                ->where('has_own_skates', false)
                ->whereNotNull('skate_id')
                ->get();
                
            foreach ($activeBookings as $booking) {
                $this->returnSkatesToStock($booking);
            }
            
            // Получаем количество записей для статистики
            $count = Booking::count();
            
            // Сначала обнуляем внешние ключи в таблице payments
            Payment::whereNotNull('booking_id')->update(['booking_id' => null]);
            
            // Теперь можно удалить все бронирования
            Booking::query()->delete(); // Используем delete вместо truncate
            
            DB::commit();
            
            Log::info('All bookings cleared', ['count' => $count]);
            
            return redirect()->route('admin.bookings.index')
                ->with('success', "Все бронирования ($count) были удалены. Коньки возвращены в базу.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error clearing bookings: ' . $e->getMessage());
            
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Ошибка при очистке бронирований: ' . $e->getMessage());
        }
    }

    private function returnSkatesToStock(Booking $booking)
    {
        if (!$booking->has_own_skates && $booking->skate_id) {
            $skate = Skate::find($booking->skate_id);
            if ($skate) {
                for ($i = 0; $i < $booking->hours; $i++) {
                    $skate->increaseQuantity();
                }
                Log::info('Skates returned to stock', [
                    'booking_id' => $booking->id,
                    'skate_id' => $skate->id,
                    'returned' => $booking->hours
                ]);
            }
        }
    }
}