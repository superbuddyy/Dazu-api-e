<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\Newsletter\Newsletter;
use App\Models\NewsletterMail;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Mail;
use Mail;

class SendNewsletterEmailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var NewsletterMail */
    public $newsletterMail;

    /**
     * SendNewsletterEmailJob constructor.
     * @param NewsletterMail $newsletterMail
     */
    public function __construct(NewsletterMail $newsletterMail)
    {
        $this->newsletterMail = $newsletterMail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            
            if($this->newsletterMail->receiver == 'all'){
                    User::chunk(50, function ($users) {
                        foreach ($users as $user) {
                            try{
                                Mail::send(
                                    new Newsletter(
                                        $user->email,
                                        $this->newsletterMail->title,
                                        $this->newsletterMail->content
                                    )
                                );
                                var_dump('ok');
                            }
                            catch (Exception $e) {
                                var_dump('error');
                            }
                            
                        }
                    });
            }else if($this->newsletterMail->receiver == 'subscribers'){
                User::whereHas('profile', function ($query) {
                    return $query->where('newsletter', true);
                })
                    ->chunk(50, function ($users) {
                        foreach ($users as $user) {
                            
                            try{
                                Mail::send(
                                    new Newsletter(
                                        $user->email,
                                        $this->newsletterMail->title,
                                        $this->newsletterMail->content
                                    )
                                );
                                var_dump('ok');
                            }
                            catch (Exception $e) {
                                var_dump('error');
                            }
                        }
                    });
            }
        } catch (Exception $e) {
            Log::error('job.newsletter_mail_failed', ['msg' => $e->getMessage()]);
        }
    }
}
