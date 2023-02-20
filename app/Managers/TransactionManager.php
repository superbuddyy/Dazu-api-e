<?php

declare(strict_types=1);

namespace App\Managers;

use App\Enums\TransactionStatus;
use App\Jobs\SendEmailJob;
use App\Mail\NewInvoice;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Invoice;

class TransactionManager
{
    public function getList(string $userId = null, string $status = TransactionStatus::PAID, bool $visible = true)
    {
        $query = Transaction::query();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $query->where('status', $status);
        $query->where('visible', $visible);
        $query->orderBy('created_at', 'DESC')->get();

        return $query->paginate(config('dazu.pagination.per_page'));
    }

    /**
     * @param Transaction $transaction
     * @return Invoice
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getInvoice(Transaction $transaction): Invoice
    {
        $seller = new Party([
            'name'          => config('dazu.company_info.name'),
            // 'email'         => config('dazu.company_info.email'),
            'custom_fields' => [
                // 'Email'         => config('dazu.company_info.email'),
                // 'NIP'        => config('dazu.company_info.nip'),
            ],
        ]);

        $address = [
            'city' => $transaction->address['city'],
            'street' => $transaction->address['street'],
            'zip_code' => $transaction->address['zip_code'],
            'country' => $transaction->address['country'],
        ];

        $buyer = new Party([
            'name'          => $transaction->name,
            'code'          => $transaction->code,
            'custom_fields' => [
                'Numer zamówienia' => $transaction->id,
                'address' => $address,
            ],
        ]);

        $items = array_map(function ($item) {
            return (new InvoiceItem())->title($item['description'])
                    ->pricePerUnit((float)($item['price'] / 100))
                    ->quantity((float)$item['qty'])
                    ->units($item['unit']);
        }, $transaction->line_items);

        // Note if needed
        $notes = [];
        $notes = implode("<br>", $notes);

        $invoice = Invoice::make('Opłata za pakiet premium')
            ->series('A'.$transaction->id)
            ->sequence((int)$transaction->created_at->format('Y'))
            ->serialNumberFormat('{SEQUENCE}/{SERIES}')
            ->seller($seller)
            ->buyer($buyer)
            ->date(now())
            ->dateFormat('d/m/Y')
            ->payUntilDays(14)
            ->currencySymbol('zł')
            ->currencyCode('PLN')
            ->currencyFormat('{VALUE}{SYMBOL}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->filename('invoices/' . $transaction->user_id . '/invoice-' . $transaction->id)
            ->addItems($items)
            ->notes($notes)
            ->logo(public_path('vendor/invoices/logo.svg'))
            // You can additionally save generated invoice to configured disk
            ->save('public');

            $link = $invoice->url();
        // Then send email to party with link

        // And return invoice itself to browser or have a different view
        return $invoice;
    }

    public function store(
        array $lineItems,
        ?string $offerId,
        string $description = '',
        User $user = null,
        bool $visibleForUser = true
    ): Transaction {
        $user = $user ?: Auth::user();

        $total = array_sum(array_column($lineItems, 'price'));
        $lineItems = array_map(function ($item) {
            return [
                'qty' => $item['qty'],
                'unit' => $item['unit'],
                'description' => $item['description'],
                'price' => $item['price'],
                'id' => $item['id'] ?? null,
            ];
        }, $lineItems);

        $address = [
            'city' => $user->profile->city,
            'street' => $user->profile->street,
            'zip_code' => $user->profile->zip_code,
            'country' => $user->profile->country,
        ];

        $transaction = Transaction::create([
            'description' => $description,
            'name' => $user->profile->name,
            'address' => $address,
            'code' => $user->profile->nip,
            'status' => TransactionStatus::PENDING,
            'line_items' => $lineItems,
            'total' => $total,
            'visible' => true,
            'user_id' => $user->id,
            'offer_id' => $offerId,
        ]);

        if ($transaction) {
            if ($visibleForUser) {
                try {
                    dispatch(new SendEmailJob(new NewInvoice($user, $transaction)));
                } catch (\Exception $e) {
                    dd($e);
                }
            }
            return $transaction;
        } else {
            throw new \Exception('Fail to create transaction');
        }
    }

    /**
     * @param Transaction $transaction
     * @param string $status
     * @return bool
     */
    public function updateStatus(Transaction $transaction, string $status)
    {
        $transaction->status = $status;
        return $transaction->save();
    }
}
