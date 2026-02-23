<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SkateController as AdminSkateController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Booking routes
Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/available-skates', [BookingController::class, 'getAvailableSkates'])->name('booking.available-skates');

// Ticket routes - теперь используем PaymentController напрямую
Route::post('/ticket', [PaymentController::class, 'createTicketPayment'])->name('ticket.store');

// Payment routes
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/check-status', [PaymentController::class, 'checkPaymentStatus'])->name('payment.check-status');
Route::get('/payment/history', [PaymentController::class, 'index'])->name('payment.index');
Route::get('/payment/{id}', [PaymentController::class, 'show'])->name('payment.show');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    
    // Guest routes (доступны без авторизации)
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    
    // Admin routes
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/', function() {
        return redirect()->route('admin.dashboard');
    });
    
    // Skates management (CRUD)
    Route::resource('skates', AdminSkateController::class);
    
    // Bookings management
    Route::controller(AdminBookingController::class)->prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{booking}', 'show')->name('show');
        Route::post('/{booking}/status', 'updateStatus')->name('status');
        Route::post('/{booking}/cancel', 'cancel')->name('cancel');
        Route::post('/{booking}/complete', 'complete')->name('complete');
        Route::delete('/{booking}', 'destroy')->name('destroy');
        Route::post('/clear-all', 'clearAll')->name('clear-all');
    });
    
    // Tickets management
    Route::controller(AdminTicketController::class)->prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{ticket}', 'show')->name('show');
        Route::post('/{ticket}/mark-used', 'markAsUsed')->name('mark-used');
        Route::delete('/{ticket}', 'destroy')->name('destroy');
        Route::post('/clear-all', 'clearAll')->name('clear-all');
    });
});

// Fallback route for 404
Route::fallback(function () {
    return redirect()->route('home');
});