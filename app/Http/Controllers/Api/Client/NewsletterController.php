<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Enums\NewsletterStatus;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Mail\Newsletter\ConfirmationNewsletter;
use App\Mail\User\NewsletterActivated as NewsletterActivatedMail;
use App\Models\Newsletter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class NewsletterController extends Controller
{
    public function store(Request $request): Response
    {
        if (Auth::user() && Auth::user()->profile->newsletter == true) {
            return response()->success('', Response::HTTP_OK);
        }
        if (Newsletter::where('email', $request->get('email'))->exists()) {
            return response()->success('user.exist', Response::HTTP_OK);
        }

        $token = Str::uuid();
        $newsletter = new Newsletter();
        $newsletter->token = $token;
        $newsletter->status = NewsletterStatus::IN_ACTIVE;
        $newsletter->email = $request->get('email');
        $newsletter->save();

        dispatch(new SendEmailJob(new ConfirmationNewsletter($request->get('email'), (string)$token)));

        return response()->success('', Response::HTTP_NO_CONTENT);
    }

    public function activate(Request $request)
    {
        $newsletter = Newsletter::where('token', $request->get('token'))->first();
        if (!$newsletter) {
            return response()->error('', Response::HTTP_NOT_FOUND);
        }

        if (($user = User::where('email', $newsletter->email)->first())) {
            $user->profile->newsletter = true;
            $user->profile->save();
        }

        $newsletter->update([
            'status' => NewsletterStatus::ACTIVE,
            'token' => null
        ]);
        dispatch(new SendEmailJob(new NewsletterActivatedMail(null, $newsletter->email)));
        return response()->success('', Response::HTTP_NO_CONTENT);
    }
}
