<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\ContactRequest;
use App\Jobs\SendEmailJob;
use App\Mail\Contact\ContactForm;
use App\Mail\Contact\Offer as OfferMail;
use App\Mail\Contact\UserProfileContact;
use App\Models\Offer;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends Controller
{
    public function sendOfferEmail(ContactRequest $request, Offer $offer): Response
    {
        dispatch(
            new SendEmailJob(
                new OfferMail(
                    $request->email,
                    $request->name,
                    $request->message,
                    $request->wantToSee,
                    $offer
                )
            )
        );
        return response()->success('', Response::HTTP_NO_CONTENT);
    }

    public function sendProfileEmail(ContactRequest $request, User $user): Response
    {
        dispatch(
            new SendEmailJob(
                new UserProfileContact(
                    $request->email,
                    $request->name,
                    $request->message,
                    $user
                )
            )
        );
        return response()->success('', Response::HTTP_NO_CONTENT);
    }

    public function sendContactForm(ContactRequest $request): Response
    {
        dispatch(
            new SendEmailJob(
                new ContactForm(
                    $request->email,
                    $request->name,
                    $request->message,
                    $request->topic
                )
            )
        );
        return response()->success('', Response::HTTP_NO_CONTENT);
    }
}
