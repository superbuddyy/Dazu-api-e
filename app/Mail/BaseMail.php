<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BaseMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /** @var array */
    public $from = [];

    public function __construct()
    {
        $this->from = [
            'from_address' => config('dazu.email.from_address'),
            'from_name' => env('dazu.email.from_name'),
        ];
    }
}
