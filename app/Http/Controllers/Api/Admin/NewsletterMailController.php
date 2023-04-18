<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewsletterEmailJob;
use App\Models\NewsletterMail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Mail;

class NewsletterMailController extends Controller
{
    public function index(): Response
    {
        $result = NewsletterMail::query()->paginate(config('dazu.pagination.per_page'));

        return response()->success($result);
    }

    public function store(Request $request): Response
    {
        $result = NewsletterMail::create(['title' => $request->title, 'content' => $request->get('content'), 'receiver' => $request->receiver]);
        
        try {
            if($request->receiver == 'all'){
                DB::table('users')->chunk(50, function ($users) {
                        foreach ($users as $user) {
                            $template_data = [
                                'email'=>$user->email,
                                'title'=>$request->title,
                                'content'=>$request->get('content')
                            ];
                            Mail::send('mail.newsletter.newsletter_mail', $template_data, function($message) use($user){
                                $message->to($user->email)->subject('Newsletter');
                            });
                            // Mail::send(
                            //     new Newsletter(
                            //         $user->email,
                            //         $this->newsletterMail->title,
                            //         $this->newsletterMail->content
                            //     )
                            // );
                        }
                    });
            }else if($this->newsletterMail->receiver == 'subscribers'){
                DB::table('users')->whereHas('profile', function ($query) {
                    return $query->where('newsletter', true);
                })
                    ->chunk(50, function ($users) {
                        foreach ($users as $user) {
                            Mail::send(
                                new Newsletter(
                                    $user->email,
                                    $this->newsletterMail->title,
                                    $this->newsletterMail->content
                                )
                            );
                        }
                    });
            }
        } catch (Exception $e) {
            Log::error('job.newsletter_mail_failed', ['msg' => $e->getMessage()]);
        }
        
        // dispatch(new SendNewsletterEmailJob($result));


        return response()->success($result, Response::HTTP_CREATED);
    }
}
