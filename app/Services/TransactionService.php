<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class TransactionService
{
    public function createTransaction(array $userData): Transaction
    {
        $transaction = Transaction::create([
            'user_id' => $userData['user_id'],
            'subscription_id' => $userData['subscription_id'],
            'price' => $userData['price']
        ]);

        return $transaction;
    }
}
