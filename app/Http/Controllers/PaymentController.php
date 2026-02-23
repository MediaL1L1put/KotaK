<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use App\Models\Ticket;
use App\Models\Skate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use YooKassa\Client;
use YooKassa\Common\Exceptions\ApiException;

class PaymentController extends Controller
{
    private $client;
    
    public function __construct()
    {
        try {
            $this->client = new Client();
            $shopId = env('YOOKASSA_SHOP_ID', '1224152');
            $secretKey = env('YOOKASSA_SECRET_KEY', 'test_TQmJq6_24Ty0Z46F7Y6kejxPCZ7VnhHrJ8l8i0VO508');
            
            Log::info('YooKassa client initializing', [
                'shop_id' => $shopId,
                'secret_key_exists' => !empty($secretKey)
            ]);
            
            $this->client->setAuth($shopId, $secretKey);
            $this->client->setLogger(Log::channel('stack'));
            
        } catch (\Exception $e) {
            Log::error('Failed to initialize YooKassa client: ' . $e->getMessage());
        }
    }
    
    /**
     * Создание платежа для билета
     */
    public function createTicketPayment(Request $request)
    {
        Log::info('=== TICKET PAYMENT CREATE ===', $request->all());
        
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            // 1. Сохраняем платеж в БД
            $payment = $this->createPaymentRecord('ticket', [
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'amount' => 300,
            ]);
            
            // 2. Создаем платеж в ЮKassa
            $idempotenceKey = uniqid('ticket_', true);
            
            $paymentData = [
                'amount' => [
                    'value' => '300.00',
                    'currency' => 'RUB',
                ],
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => route('payment.success') . '?payment_id=' . $payment->payment_id,
                ],
                'capture' => true,
                'description' => 'Входной билет на каток',
                'metadata' => [
                    'db_payment_id' => $payment->id,
                    'type' => 'ticket',
                ],
            ];
            
            // Пытаемся создать платеж
            $response = $this->createPaymentWithRetry($paymentData, $idempotenceKey);
            
            if (!$response) {
                throw new \Exception('Не удалось создать платеж в ЮKassa после всех попыток');
            }
            
            // 3. Обновляем запись с данными из ЮKassa
            $payment->payment_id = $response->getId();
            $payment->status = $response->getStatus();
            $payment->paid = $response->getPaid();
            $payment->save();
            
            // 4. Получаем URL для редиректа
            $confirmation = $response->getConfirmation();
            if (!$confirmation) {
                throw new \Exception('No confirmation data from YooKassa');
            }
            
            $redirectUrl = $confirmation->getConfirmationUrl();
            
            if (empty($redirectUrl)) {
                throw new \Exception('Empty redirect URL from YooKassa');
            }
            
            Log::info('Ticket payment created successfully', [
                'db_id' => $payment->id,
                'yookassa_id' => $response->getId(),
                'redirect_url' => $redirectUrl,
            ]);
            
            session(['last_payment_id' => $response->getId()]);
            
            return redirect()->away($redirectUrl);
            
        } catch (ApiException $e) {
            Log::error('YooKassa API Exception: ' . $e->getMessage());
            Log::error('API Response: ' . json_encode($e->getResponseBody()));
            
            if (isset($payment) && $payment->exists) {
                $payment->delete();
            }
            
            return redirect()->back()
                ->with('error', 'Ошибка платежной системы: ' . $this->getUserFriendlyError($e->getMessage()))
                ->withInput();
            
        } catch (\Exception $e) {
            Log::error('Payment create error: ' . $e->getMessage());
            
            if (isset($payment) && $payment->exists) {
                $payment->delete();
            }
            
            return redirect()->back()
                ->with('error', 'Ошибка при создании платежа: ' . $this->getUserFriendlyError($e->getMessage()))
                ->withInput();
        }
    }
    
    /**
     * Создание платежа для бронирования коньков
     */
    public function createBookingPayment(Request $request)
    {
        Log::info('=== BOOKING PAYMENT CREATE ===', $request->all());
        
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'hours' => 'required|in:1,2,3,4',
            'has_own_skates' => 'sometimes|boolean',
            'skate_id' => 'required_if:has_own_skates,false|exists:skates,id|nullable',
            'skate_size' => 'required_if:has_own_skates,false|integer|nullable'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Проверяем доступность коньков
        $skatesCost = 0;
        $skateModel = null;
        
        if (!$request->has_own_skates && $request->skate_id) {
            $skate = Skate::with('model')->find($request->skate_id);
            
            if (!$skate || !$skate->isAvailable()) {
                return redirect()->back()
                    ->with('error', 'Выбранные коньки недоступны')
                    ->withInput();
            }
            
            $skatesCost = $skate->price_per_hour * $request->hours;
            $skateModel = $skate->model->name ?? 'Коньки';
        }
        
        $entranceCost = 300;
        $totalAmount = $entranceCost + $skatesCost;
        
        try {
            // 1. Сохраняем платеж в БД
            $payment = $this->createPaymentRecord('booking', [
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'amount' => $totalAmount,
                'metadata' => [
                    'hours' => $request->hours,
                    'has_own_skates' => $request->has_own_skates ?? false,
                    'skate_id' => $request->skate_id ?? null,
                    'skate_size' => $request->skate_size ?? null,
                    'skates_cost' => $skatesCost,
                    'entrance_cost' => $entranceCost,
                ]
            ]);
            
            // 2. Создаем описание
            $description = "Вход на каток";
            if (!$request->has_own_skates && $request->skate_id) {
                $description .= " + аренда {$skateModel} ({$request->hours}ч)";
            }
            
            // 3. Создаем платеж в ЮKassa
            $idempotenceKey = uniqid('booking_', true);
            
            $paymentData = [
                'amount' => [
                    'value' => number_format($totalAmount, 2, '.', ''),
                    'currency' => 'RUB',
                ],
                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => route('payment.success') . '?payment_id=' . $payment->payment_id,
                ],
                'capture' => true,
                'description' => $description,
                'metadata' => [
                    'db_payment_id' => $payment->id,
                    'type' => 'booking',
                ],
            ];
            
            // Пытаемся создать платеж
            $response = $this->createPaymentWithRetry($paymentData, $idempotenceKey);
            
            if (!$response) {
                throw new \Exception('Не удалось создать платеж в ЮKassa после всех попыток');
            }
            
            // 4. Обновляем запись с данными из ЮKassa
            $payment->payment_id = $response->getId();
            $payment->status = $response->getStatus();
            $payment->paid = $response->getPaid();
            $payment->save();
            
            // 5. Получаем URL для редиректа
            $confirmation = $response->getConfirmation();
            if (!$confirmation) {
                throw new \Exception('No confirmation data from YooKassa');
            }
            
            $redirectUrl = $confirmation->getConfirmationUrl();
            
            if (empty($redirectUrl)) {
                throw new \Exception('Empty redirect URL from YooKassa');
            }
            
            Log::info('Booking payment created successfully', [
                'db_id' => $payment->id,
                'yookassa_id' => $response->getId(),
                'redirect_url' => $redirectUrl,
            ]);
            
            session(['last_payment_id' => $response->getId()]);
            
            return redirect()->away($redirectUrl);
            
        } catch (ApiException $e) {
            Log::error('YooKassa API Exception: ' . $e->getMessage());
            Log::error('API Response: ' . json_encode($e->getResponseBody()));
            
            if (isset($payment) && $payment->exists) {
                $payment->delete();
            }
            
            return redirect()->back()
                ->with('error', 'Ошибка платежной системы: ' . $this->getUserFriendlyError($e->getMessage()))
                ->withInput();
            
        } catch (\Exception $e) {
            Log::error('Payment create error: ' . $e->getMessage());
            
            if (isset($payment) && $payment->exists) {
                $payment->delete();
            }
            
            return redirect()->back()
                ->with('error', 'Ошибка при создании платежа: ' . $this->getUserFriendlyError($e->getMessage()))
                ->withInput();
        }
    }
    
    /**
     * Создание записи платежа в БД
     */
    private function createPaymentRecord(string $type, array $data): Payment
    {
        $payment = new Payment();
        $payment->full_name = $data['full_name'];
        $payment->phone = $data['phone'];
        $payment->email = $data['email'] ?? null;
        $payment->amount = $data['amount'];
        $payment->status = 'pending';
        $payment->paid = false;
        $payment->test = true;
        $payment->type = $type;
        $payment->metadata = $data['metadata'] ?? [
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'created_at' => now()->toDateTimeString(),
        ];
        
        if (!$payment->save()) {
            throw new \Exception('Failed to save payment to database');
        }
        
        Log::info('Payment DB record created', ['payment_id' => $payment->id]);
        
        return $payment;
    }
    
    /**
     * Создание платежа в ЮKassa с несколькими попытками
     */
    private function createPaymentWithRetry(array $paymentData, string $idempotenceKey)
    {
        $attempts = [
            'without_receipt' => function() use ($paymentData, $idempotenceKey) {
                return $this->client->createPayment($paymentData, $idempotenceKey);
            },
            'with_receipt' => function() use ($paymentData, $idempotenceKey) {
                $dataWithReceipt = $paymentData;
                $dataWithReceipt['receipt'] = [
                    'customer' => [
                        'phone' => preg_replace('/[^0-9]/', '', $paymentData['metadata']['phone'] ?? '79991234567'),
                    ],
                    'items' => [
                        [
                            'description' => $paymentData['description'],
                            'quantity' => '1.00',
                            'amount' => [
                                'value' => $paymentData['amount']['value'],
                                'currency' => 'RUB',
                            ],
                            'vat_code' => 1,
                        ]
                    ],
                ];
                return $this->client->createPayment($dataWithReceipt, $idempotenceKey . '_receipt');
            },
        ];
        
        foreach ($attempts as $attemptName => $attemptFunction) {
            try {
                Log::info('Trying payment creation: ' . $attemptName);
                return $attemptFunction();
            } catch (\Exception $e) {
                Log::warning('Failed with ' . $attemptName . ': ' . $e->getMessage());
                continue;
            }
        }
        
        return null;
    }
    
    /**
     * Получение понятного пользователю сообщения об ошибке
     */
    private function getUserFriendlyError(string $error): string
    {
        if (strpos($error, 'receipt') !== false) {
            return 'Временная проблема с платежной системой. Пожалуйста, попробуйте позже или обратитесь к администратору.';
        }
        
        if (strpos($error, 'timeout') !== false) {
            return 'Превышено время ожидания ответа от платежной системы. Пожалуйста, попробуйте еще раз.';
        }
        
        return $error;
    }
    
    /**
     * Обработка успешного платежа (возврат с ЮKassa)
     */
    public function paymentSuccess(Request $request)
    {
        Log::info('=== PAYMENT SUCCESS CALLBACK ===', $request->all());
        
        $paymentId = $request->get('payment_id');
        
        if (!$paymentId && session()->has('last_payment_id')) {
            $paymentId = session()->get('last_payment_id');
        }
        
        if (!$paymentId) {
            return view('payment.generic_success', [
                'message' => 'Платеж обрабатывается. Проверьте статус в истории платежей.',
            ]);
        }
        
        try {
            $payment = $this->client->getPaymentInfo($paymentId);
            $dbPayment = Payment::where('payment_id', $paymentId)->first();
            
            if ($dbPayment) {
                $this->processSuccessfulPayment($dbPayment, $payment);
                
                if ($payment->getPaid()) {
                    return view('payment.success', [
                        'payment' => $dbPayment,
                        'amount' => $payment->getAmount()->getValue(),
                        'message' => $dbPayment->type === 'ticket' 
                            ? 'Билет успешно оплачен!' 
                            : 'Бронирование успешно оплачено!',
                        'type' => $dbPayment->type,
                    ]);
                }
            }
            
            return view('payment.generic_success', [
                'message' => 'Платеж обрабатывается. Если платеж прошел, статус обновится автоматически.',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Payment success error: ' . $e->getMessage());
            
            return view('payment.generic_success', [
                'message' => 'Платеж обрабатывается. При возникновении проблем проверьте историю платежей.',
            ]);
        }
    }
    
    /**
     * Обработка успешного платежа
     */
    private function processSuccessfulPayment($dbPayment, $yooPayment)
    {
        $oldPaid = $dbPayment->paid;
        
        $dbPayment->status = $yooPayment->getStatus();
        $dbPayment->paid = $yooPayment->getPaid();
        
        if ($yooPayment->getPaid() && !$oldPaid) {
            $dbPayment->paid_at = now();
            
            if ($dbPayment->type === 'ticket') {
                $ticket = Ticket::create([
                    'full_name' => $dbPayment->full_name,
                    'phone' => $dbPayment->phone,
                    'email' => $dbPayment->email,
                    'amount' => $dbPayment->amount,
                    'payment_id' => $dbPayment->payment_id,
                    'payment_status' => 'paid',
                    'is_used' => false
                ]);
                $dbPayment->ticket_id = $ticket->id;
                
            } elseif ($dbPayment->type === 'booking') {
                $metadata = $dbPayment->metadata;
                
                if (isset($metadata['skate_id']) && $metadata['skate_id']) {
                    $skate = Skate::find($metadata['skate_id']);
                    if ($skate) {
                        $skate->decreaseQuantity();
                    }
                }
                
                $booking = Booking::create([
                    'full_name' => $dbPayment->full_name,
                    'phone' => $dbPayment->phone,
                    'hours' => $metadata['hours'] ?? 1,
                    'skate_id' => $metadata['skate_id'] ?? null,
                    'skate_size' => $metadata['skate_size'] ?? null,
                    'has_own_skates' => $metadata['has_own_skates'] ?? false,
                    'total_amount' => $dbPayment->amount,
                    'payment_id' => $dbPayment->payment_id,
                    'payment_status' => 'paid'
                ]);
                $dbPayment->booking_id = $booking->id;
            }
        }
        
        $dbPayment->save();
    }
    
    /**
     * Webhook для уведомлений от ЮKassa
     */
    public function webhook(Request $request)
    {
        Log::info('=== YOOKASSA WEBHOOK ===');
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            return response('No data', 400);
        }
        
        try {
            $paymentId = $data['object']['id'] ?? null;
            $status = $data['object']['status'] ?? null;
            $paid = $data['object']['paid'] ?? false;
            
            if (!$paymentId) {
                return response('No payment ID', 400);
            }
            
            $dbPayment = Payment::where('payment_id', $paymentId)->first();
            
            if ($dbPayment) {
                $oldPaid = $dbPayment->paid;
                
                $dbPayment->status = $status;
                $dbPayment->paid = $paid;
                
                if ($paid && !$oldPaid) {
                    $dbPayment->paid_at = now();
                    
                    if ($dbPayment->type === 'ticket' && !$dbPayment->ticket_id) {
                        Ticket::create([
                            'full_name' => $dbPayment->full_name,
                            'phone' => $dbPayment->phone,
                            'email' => $dbPayment->email,
                            'amount' => $dbPayment->amount,
                            'payment_id' => $dbPayment->payment_id,
                            'payment_status' => 'paid',
                            'is_used' => false
                        ]);
                        
                    } elseif ($dbPayment->type === 'booking' && !$dbPayment->booking_id) {
                        $metadata = $dbPayment->metadata;
                        
                        if (isset($metadata['skate_id']) && $metadata['skate_id']) {
                            $skate = Skate::find($metadata['skate_id']);
                            if ($skate) {
                                $skate->decreaseQuantity();
                            }
                        }
                        
                        Booking::create([
                            'full_name' => $dbPayment->full_name,
                            'phone' => $dbPayment->phone,
                            'hours' => $metadata['hours'] ?? 1,
                            'skate_id' => $metadata['skate_id'] ?? null,
                            'skate_size' => $metadata['skate_size'] ?? null,
                            'has_own_skates' => $metadata['has_own_skates'] ?? false,
                            'total_amount' => $dbPayment->amount,
                            'payment_id' => $dbPayment->payment_id,
                            'payment_status' => 'paid'
                        ]);
                    }
                }
                
                $dbPayment->save();
                Log::info('Webhook processed: ' . $paymentId . ' - ' . $status);
            }
            
            return response('OK', 200);
            
        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());
            return response('Error', 500);
        }
    }
    
    /**
     * Проверка статуса платежа (AJAX)
     */
    public function checkPaymentStatus(Request $request)
    {
        $paymentId = $request->get('payment_id');
        
        if (!$paymentId) {
            return response()->json(['error' => 'No payment ID'], 400);
        }
        
        try {
            $payment = $this->client->getPaymentInfo($paymentId);
            
            return response()->json([
                'success' => true,
                'status' => $payment->getStatus(),
                'paid' => $payment->getPaid(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * История платежей пользователя
     */
    public function index()
    {
        $payments = Payment::orderBy('created_at', 'desc')->paginate(20);
        return view('payment.index', ['payments' => $payments]);
    }
    
    /**
     * Просмотр конкретного платежа
     */
    public function show($id)
    {
        $payment = Payment::with(['booking', 'ticket'])->findOrFail($id);
        
        try {
            $yooPayment = $payment->payment_id 
                ? $this->client->getPaymentInfo($payment->payment_id) 
                : null;
        } catch (\Exception $e) {
            $yooPayment = null;
            Log::error('Failed to get YooKassa payment info: ' . $e->getMessage());
        }
        
        return view('payment.show', [
            'payment' => $payment,
            'yooPayment' => $yooPayment,
            'isTest' => $payment->test,
        ]);
    }
}