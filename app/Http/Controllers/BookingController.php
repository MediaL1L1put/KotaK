<?php

namespace App\Http\Controllers;

use App\Models\Skate;
use App\Models\SkateModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    protected $paymentController;

    public function __construct(PaymentController $paymentController)
    {
        $this->paymentController = $paymentController;
    }

    public function create()
    {
        $skateModels = SkateModel::with('skates')->get();
        return view('booking', compact('skateModels'));
    }

    public function store(Request $request)
    {
        // Перенаправляем создание платежа в PaymentController
        return $this->paymentController->createBookingPayment($request);
    }

    public function getAvailableSkates(Request $request)
    {
        $skates = Skate::with('model')
            ->where('available_quantity', '>', 0)
            ->where('size', $request->size)
            ->get();

        return response()->json($skates);
    }
}