<?php
namespace App\Repositories;

use App\Models\ScheduledTransaction;

class SheduledTransactionRepo
{
    protected $model;

    public function __construct(ScheduledTransaction $scheduledTransaction)
    {
        $this->model = $scheduledTransaction;
    }

    public function findById($transactionId)
    {
        return $this->model::find($transactionId);
    }

    public function getSheduledTransactionByClientId($clientId){
        return $this->model::where('client_id', $clientId)->get();
    } 


}