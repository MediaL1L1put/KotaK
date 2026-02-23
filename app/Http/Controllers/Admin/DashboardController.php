<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Ticket;
use App\Models\Skate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Проверка админа
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Пожалуйста, войдите в систему');
        }

        if (!Auth::user()->is_admin) {
            Auth::logout();
            return redirect()->route('admin.login')->with('error', 'У вас нет прав доступа');
        }

        // Статистика по конькам с правильным подсчетом
        $totalSkates = Skate::sum('quantity');
        $availableSkates = Skate::sum('available_quantity');
        
        // Активные бронирования (оплаченные, которые еще не завершились)
        $activeBookings = Booking::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subHours(4))
            ->count();

        $stats = [
            'total_revenue' => Booking::where('payment_status', 'paid')->sum('total_amount') + 
                               Ticket::where('payment_status', 'paid')->sum('amount'),
            'total_bookings' => Booking::count(),
            'paid_bookings' => Booking::where('payment_status', 'paid')->count(),
            'total_tickets' => Ticket::count(),
            'used_tickets' => Ticket::where('is_used', true)->count(),
            'total_skates' => $totalSkates,
            'available_skates' => $availableSkates,
            'in_use_skates' => $totalSkates - $availableSkates,
            'active_bookings' => $activeBookings,
            'total_users' => User::count(),
        ];

        $recent_bookings = Booking::with('skate.model')
            ->latest()
            ->take(5)
            ->get();

        $recent_tickets = Ticket::latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_bookings', 'recent_tickets'));
    }
}