<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository
{
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function findById(int $id): ?Transaction
    {
        return Transaction::find($id);
    }

    public function updateStatus(Transaction $transaction, string $status, ?array $responseData = null): Transaction
    {
        $updateData = ['status' => $status];
        if ($responseData) {
            $updateData['response_data'] = $responseData;
        }

        $transaction->update($updateData);
        return $transaction;
    }
}
