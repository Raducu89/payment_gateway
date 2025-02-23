<?php

namespace App\Services\Payment;

use App\Models\Order;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;

class StripePaymentProvider implements PaymentProviderInterface
{   
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = new Client([
            'base_uri' => config('payment_gateways.stripe.base_uri'), 
            'timeout'  => 5,
        ]);
    }

    public function pay(Order $order): array
    {
        
        try {
            $response = $this->client->post('/v1/payment_intents', [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('payment_gateways.stripe.secret'),
                    'Content-Type'  => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                     'amount'   => $order->amount,
                     'currency' => 'usd',
                ],
            ]);

           $body = json_decode($response->getBody(), true);

            if (isset($body['status']) && $body['status'] === 'succeeded') {
                return [
                    'status' => 'success',
                    'response' => [
                        'transaction_id' => 'STRIPE_TX_12345',
                        'details' => $body,
                    ],
                ];
            } else {
                return [
                    'status' => 'failed',
                    'response' => [
                        'error'   => $body['error'] ?? 'Payment failed',
                        'details' => $body,
                    ],
                ];
            }
    
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw new Exception("Stripe API request failed: " . $e->getMessage());
        }
    }
}
