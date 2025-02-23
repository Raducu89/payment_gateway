<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PaymentService;
use App\Repositories\OrderRepository;
use App\Repositories\TransactionRepository;
use App\Services\Payment\PaymentProviderInterface;
use App\Models\Order;
use App\Models\Transaction;
use Mockery;

class PaymentServiceTest extends TestCase
{
    public function testProcessPaymentSuccess()
    {
        // Creăm un dummy Order și o tranzacție asociată
        $order = new Order();
        $order->id = 1;
        $transaction = new Transaction();
        $transaction->id = 10;
        $transaction->status = 'pending';
        // Setăm relația încărcată a comenzii
        $order->setRelation('transaction', $transaction);

        // Mock pentru PaymentProviderInterface, simulăm plata cu succes
        $paymentProviderMock = Mockery::mock(PaymentProviderInterface::class);
        $paymentProviderMock->shouldReceive('pay')
            ->once()
            ->with($order)
            ->andReturn([
                'status'   => 'success',
                'response' => [
                    'transaction_id' => 'TX123',
                    'details'        => 'Test payment success'
                ],
            ]);

        // Mock pentru OrderRepository: actualizează status-ul comenzii
        $orderRepositoryMock = Mockery::mock(OrderRepository::class);
        $orderRepositoryMock->shouldReceive('updateStatus')
            ->once()
            ->with($order, 'paid')
            ->andReturn($order);

        // Mock pentru TransactionRepository: actualizează status-ul tranzacției
        $transactionRepositoryMock = Mockery::mock(TransactionRepository::class);
        $transactionRepositoryMock->shouldReceive('updateStatus')
            ->once()
            ->with($transaction, 'paid', \Mockery::subset([
                'transaction_id' => 'TX123'
            ]))
            ->andReturn($transaction);

        $paymentService = new PaymentService(
            $paymentProviderMock,
            $orderRepositoryMock,
            $transactionRepositoryMock
        );

        $result = $paymentService->processPayment($order);

        $this->assertEquals('paid', $result['transaction_status']);
        $this->assertArrayHasKey('transaction_id', $result);
    }

    public function testProcessPaymentFailure()
    {
        // Creăm un dummy Order și o tranzacție asociată
        $order = new Order();
        $order->id = 1;
        $transaction = new Transaction();
        $transaction->id = 10;
        $transaction->status = 'pending';
        $order->setRelation('transaction', $transaction);

        // Simulăm eșecul plății prin aruncarea unei excepții în PaymentProvider
        $paymentProviderMock = Mockery::mock(PaymentProviderInterface::class);
        $paymentProviderMock->shouldReceive('pay')
            ->once()
            ->with($order)
            ->andThrow(new \Exception("Payment failed"));

        $orderRepositoryMock = Mockery::mock(OrderRepository::class);
        $orderRepositoryMock->shouldReceive('updateStatus')
            ->once()
            ->with($order, 'failed')
            ->andReturn($order);

        $transactionRepositoryMock = Mockery::mock(TransactionRepository::class);
        $transactionRepositoryMock->shouldReceive('updateStatus')
            ->once()
            ->with($transaction, 'failed', \Mockery::subset(['error' => 'Payment failed']))
            ->andReturn($transaction);

        $paymentService = new PaymentService(
            $paymentProviderMock,
            $orderRepositoryMock,
            $transactionRepositoryMock
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Payment failed");

        $paymentService->processPayment($order);
    }
}
