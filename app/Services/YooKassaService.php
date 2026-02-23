<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Ticket;
use YooKassa\Client;
use Illuminate\Support\Facades\Log;

class YooKassaService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuth('1224152', 'test_TQmJq6_24Ty0Z46F7Y6kejxPCZ7VnhHrJ8l8i0VO508');
        $this->client->setLogger(Log::channel('stack'));
    }

    public function createTicketPayment(array $ticketData)
    {
        try {
            $payment = $this->client->createPayment(
                [
                    'amount' => [
                        'value' => 300.00,
                        'currency' => 'RUB',
                    ],
                    'confirmation' => [
                        'type' => 'redirect',
                        'return_url' => route('payment.success', ['type' => 'ticket']),
                    ],
                    'capture' => true,
                    'description' => 'Входной билет на каток',
                    'metadata' => [
                        'type' => 'ticket',
                        'customer_name' => $ticketData['full_name'],
                        'customer_phone' => $ticketData['phone'],
                        'customer_email' => $ticketData['email'] ?? '',
                    ],
                ],
                uniqid('', true)
            );

            Log::info('Payment created', ['payment_id' => $payment->id]);
            return $payment;

        } catch (\Exception $e) {
            Log::error('YooKassa payment creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function createBookingPayment(array $bookingData)
    {
        try {
            $entranceCost = 300;
            $skatesCost = isset($bookingData['has_own_skates']) && !$bookingData['has_own_skates'] 
                ? ($bookingData['hours'] * 150) 
                : 0;
            
            $totalAmount = $entranceCost + $skatesCost;

            $description = "Вход на каток";
            if (isset($bookingData['has_own_skates']) && !$bookingData['has_own_skates']) {
                $description .= " + аренда коньков ({$bookingData['hours']}ч)";
            }

            $payment = $this->client->createPayment(
                [
                    'amount' => [
                        'value' => $totalAmount,
                        'currency' => 'RUB',
                    ],
                    'confirmation' => [
                        'type' => 'redirect',
                        'return_url' => route('payment.success', ['type' => 'booking']),
                    ],
                    'capture' => true,
                    'description' => $description,
                    'metadata' => [
                        'type' => 'booking',
                        'customer_name' => $bookingData['full_name'],
                        'customer_phone' => $bookingData['phone'],
                        'hours' => $bookingData['hours'],
                        'has_own_skates' => $bookingData['has_own_skates'] ?? false,
                        'skate_id' => $bookingData['skate_id'] ?? null,
                        'skate_size' => $bookingData['skate_size'] ?? null,
                    ],
                ],
                uniqid('', true)
            );

            Log::info('Booking payment created', ['payment_id' => $payment->id]);
            return $payment;

        } catch (\Exception $e) {
            Log::error('YooKassa booking payment creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function getPaymentInfo($paymentId)
    {
        try {
            return $this->client->getPaymentInfo($paymentId);
        } catch (\Exception $e) {
            Log::error('YooKassa get payment info failed: ' . $e->getMessage());
            return null;
        }
    }
}