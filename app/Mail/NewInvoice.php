<?php

declare(strict_types=1);

namespace App\Mail;

use App\Laravue\Models\User;
use App\Managers\TransactionManager;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class NewInvoice extends BaseMail
{
    /** @var User $user */
    public $user;

    /** @var Transaction */
    public $transaction;

    public function __construct(User $user, Transaction $transaction)
    {
        parent::__construct();
        $this->transaction = $transaction;
        $this->user = $user;
    }

    public function build(): self
    {
        try {
            // Create invoice
            $invoice = resolve(TransactionManager::class)->getInvoice($this->transaction);

            $this->to($this->user->email)
                ->from($this->from['from_address'], $this->from['from_name'])
                ->subject(trans('mail.new_invoice'));
            $this->attachData($invoice->output, $this->transaction->created_at->format('Y-m-d') . '-Faktura.pdf');
            return $this->markdown('mail.invoice.new_invoice');
        } catch (\Exception $e) {
            Log::error('Fail to build new invoice email', ['error' => $e->getMessage()]);
        }
    }
}
