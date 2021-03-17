<?php

declare(strict_types=1);

namespace App\Events\Newsletter;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Queue\SerializesModels;

class NewsletterMailCreated
{
    use SerializesModels;

    /** @var string */
    public $content;

    /** @var string */
    public $title;

    /**
     * NewsletterMailCreated constructor.
     * @param string $title
     * @param string $content
     */
    public function __construct(string $title, string $content)
    {
        $this->title = $title;
        $this->content = $content;
    }
}
