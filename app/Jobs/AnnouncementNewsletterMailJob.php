<?php

namespace App\Jobs;

use App\Mail\Newsletter\AnnouncementNewsletter;
use App\Models\AnnouncementNewsletterMails;
use App\Models\User;
use Exception;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AnnouncementNewsletterMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $newsletterMail;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(AnnouncementNewsletterMails $newsletterMail)
    {
        $this->newsletterMail = $newsletterMail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $testids = array('testing.techleaf@gmail.com','test@yopmail.com','whitedevil3244@gmail.com','r.w84@hotmail.co.uk','waszka73@yahoo.com');
        try {
            User::whereHas('profile')
            ->whereIn('email',$testids)
                ->chunk(50, function ($users) {
                    foreach ($users as $user) {
                        Mail::send(
                            new AnnouncementNewsletter(
                                $user->email,
                                $this->newsletterMail->title,
                                $this->newsletterMail->content
                            )
                        );
                    }
                });
            // Mail::send(
            //     new AnnouncementNewsletter(
            //         'makemydazutest@yopmail.com',
            //         $this->newsletterMail->title,
            //         $this->newsletterMail->content
            //     )
            // );
        } catch (Exception $e) {
            Log::error('job.newsletter_mail_failed', ['msg' => $e->getMessage()]);
        }
    }
}
