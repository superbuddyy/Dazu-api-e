<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Transaction\TransactionCollection;
use App\Managers\TransactionManager;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TransactionController
{
    private $transactionManager;

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
    }

    public function index(): Response
    {
        $transactions = $this->transactionManager->getList(Auth::id());
        return response()->success(new TransactionCollection($transactions));
    }

    public function generateInvoice(Transaction $transaction)
    {
        $invoice = $this->transactionManager->getInvoice($transaction);
        return response()->success(['url' => $invoice->url()], Response::HTTP_OK);
    }
}
