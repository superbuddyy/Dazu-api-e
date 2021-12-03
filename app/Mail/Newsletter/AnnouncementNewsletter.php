<?php

declare(strict_types=1);

namespace App\Mail\Newsletter;

use App\Laravue\Models\User;
use App\Mail\BaseMail;

class AnnouncementNewsletter extends BaseMail
{
    /** @var string */
    private $email;

    /** @var string */
    public $title;

    /** @var string */
    public $content;


    /**
     * EmailConfirmation constructor.
     * @param string $email
     * @param string $title
     * @param string $content
     */
    public function __construct(string $email, string $title, string $content)
    {
        parent::__construct();
        $this->email = $email;
        $this->title = $title;
        $this->content = $content;
    }

    public function build(): self
    {
        $this->to($this->email)
            ->from($this->from['from_address'], $this->from['from_name'])
            ->subject($this->title);
        return $this->markdown('mail.newsletter.newsletter_mail');
    }
}
