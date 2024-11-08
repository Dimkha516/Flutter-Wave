<?php
namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository
{
    protected $model;

    public function __construct(Transaction $transaction)
    {
        $this->model = $transaction;
    }

    public function findById($transactionId)
    {
        return Transaction::find($transactionId);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function getTransactionsByClientId($clientId)
    {
        return Transaction::where('client_id', $clientId)->get();
    }
}