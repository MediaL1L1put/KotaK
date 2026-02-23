<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    /**
     * Display a listing of tickets.
     */
    public function index(Request $request)
    {
        $query = Ticket::query();

        if ($request->has('status') && $request->status != '') {
            $query->where('payment_status', $request->status);
        }

        if ($request->has('is_used') && $request->is_used != '') {
            $query->where('is_used', $request->is_used);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.tickets.index', compact('tickets'));
    }

    /**
     * Display the specified ticket.
     */
    public function show(Ticket $ticket)
    {
        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * Mark ticket as used.
     */
    public function markAsUsed(Ticket $ticket)
    {
        if ($ticket->is_used) {
            return redirect()->back()
                ->with('error', 'Билет уже был использован');
        }

        $ticket->update([
            'is_used' => true,
            'used_at' => now()
        ]);
        
        return redirect()->back()
            ->with('success', 'Билет отмечен как использованный');
    }
    
    /**
     * Remove the specified ticket.
     */
    public function destroy(Ticket $ticket)
    {
        try {
            DB::beginTransaction();
            
            // Сначала обнуляем внешние ключи в payments если есть
            Payment::where('ticket_id', $ticket->id)->update(['ticket_id' => null]);
            
            $ticket->delete();
            
            DB::commit();
            
            return redirect()->route('admin.tickets.index')
                ->with('success', 'Запись о билете удалена');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting ticket: ' . $e->getMessage());
            
            return redirect()->route('admin.tickets.index')
                ->with('error', 'Ошибка при удалении билета: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear all tickets.
     */
    public function clearAll(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $count = Ticket::count();
            
            // Обнуляем внешние ключи в payments
            Payment::whereNotNull('ticket_id')->update(['ticket_id' => null]);
            
            // Удаляем все билеты
            Ticket::query()->delete();
            
            DB::commit();
            
            Log::info('All tickets cleared', ['count' => $count]);
            
            return redirect()->route('admin.tickets.index')
                ->with('success', "Все билеты ($count) были удалены.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error clearing tickets: ' . $e->getMessage());
            
            return redirect()->route('admin.tickets.index')
                ->with('error', 'Ошибка при очистке билетов: ' . $e->getMessage());
        }
    }
}