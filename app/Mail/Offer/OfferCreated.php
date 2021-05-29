<?php

declare(strict_types=1);

namespace App\Mail\Offer;

use App\Laravue\Models\User;
use App\Mail\BaseMail;
use App\Models\Offer;

class OfferCreated extends BaseMail
{
    /** @var Offer $offer */
    public $offer;

    /** @var User $user */
    public $user;

    /** @var $variant */
    public $variant;

    /** @var string  */
    public const VISIBLE_IN_FUTURE = 'visible_in_future';

    /** @var string  */
    public const STANDARD = 'standard';

    public function __construct(Offer $offer, User $user, string $variant = self::STANDARD)
    {
        parent::__construct();
        $this->offer = $offer;
        $this->user = $user;
        $this->variant = $variant;
    }

    public function build(): self
    {
        if ($this->variant === self::VISIBLE_IN_FUTURE) {
            $title = trans('mail.offer_created_visible_in_future');
            $template = 'mail.offer.created';
        } else { // STANDARD
            $title = trans('mail.offer_created');
            $template = 'mail.offer.created';
        }

        $this->to($this->user->email)
            ->from($this->from['from_address'], $this->from['from_name'])
            ->subject($title);
        return $this->markdown($template);
    }
}
