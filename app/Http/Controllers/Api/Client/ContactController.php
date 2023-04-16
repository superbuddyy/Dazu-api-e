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
// use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\ContactEmails;
use Illuminate\Support\Str;
use App\Mail\Contact\ContactConfirmation;
use Mail;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function sendOfferEmail(ContactRequest $request, Offer $offer): JsonResponse
    {
        $user =  DB::table('users')->where('email', $request->email)->first();
        if ($user) {
            $template_data = ['email'=>$request->email, 'name'=>$request->name, 'message' => $request->message];
            Mail::send('mail.contact.offer', $template_data, function($message) use($request){
                    $message->to($request->email)->subject('Offer Form');
            });
            return response()->success('', Response::HTTP_NO_CONTENT);
        // }
        // if (User::where('email', $request->email)->exists()) {
        //     dispatch(
        //         new SendEmailJob(
        //             new OfferMail(
        //                 $request->email,
        //                 $request->name,
        //                 $request->message,
        //                 $request->wantToSee,
        //                 $offer
        //             )
        //         )
        //     );
        //     return response()->success('', Response::HTTP_NO_CONTENT);
        } else {
            $json_ary = [
                'email' => $request->email,
                'name' => $request->name,
                'message' => $request->message,
                'wantToSee' => $request->wantToSee,
                'offer' => $offer->id
            ];
            $json_data = json_encode($json_ary);
            $data = $this->createRecord($json_data,'offer');
            $url = '/ogloszenia/'.$offer->slug;
            $this->sendConfirmMail($request->email,$data,$url);
            return response()->success('verify_email');
        }
    }

    public function sendProfileEmail(ContactRequest $request, User $user): Response
    {
        if (User::where('email', $request->email)->exists()) {
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
        } else {
            $json_ary = [
                'email' => $request->email,
                'name' => $request->name,
                'message' => $request->message,
                'user' => $user->id
            ];
            $json_data = json_encode($json_ary);
            $data = $this->createRecord($json_data,'profile');
            $url = '/';
            $this->sendConfirmMail($request->email,$data,$url);
            return response()->success('verify_email');
        }
    }

    public function sendContactForm(ContactRequest $request): Response
    {
        // if (User::where('email', $request->email)->exists()) {
        //     dispatch(
        //         new SendEmailJob(
        //             new ContactForm(
        //                 $request->email,
        //                 $request->name,
        //                 $request->message,
        //                 $request->topic
        //             )
        //         )
        //     );
        //     return response()->success('', Response::HTTP_NO_CONTENT);
        // }
        $user =  DB::table('users')->where('email', $request->email)->first();
        if ($user) {
            // $template_data = ['email'=>'dazudeweloper1yahoocom', 'name'=>'asd', 'message' => 'message', 'topic'=>'topic'];
            $template_data = ['email'=>$request->email, 'name'=>$request->name, 'message' => $request->message, 'topic'=>$request->topic];
            // var_dump($template_data);
            // die;
            Mail::send('mail.contact.contact_form', $template_data, function($message) use($request){
                    $message->to($request->email)->subject('Contact Form');
            });
            return response()->success('', Response::HTTP_NO_CONTENT);
        }else {
            $json_ary = [
                'email' => $request->email,
                'name' => $request->name,
                'message' => $request->message,
                'topic' => $request->topic
            ];
            $json_data = json_encode($json_ary);
            $data = $this->createRecord($json_data,'contact');
            $url = '/';
            $this->sendConfirmMail($request->email,$data,$url);
            return response()->success('verify_email');
        }
    }
    public function createRecord($json_data,$type)
    {
        return ContactEmails::Create([
            'email_data' => $json_data,
            'verification_token' => Str::uuid()->toString(),
            'type' => $type,
            'status' => 0
            ]
        );
    }
    public function  updateRecord($contact) {
        $data = ContactEmails::where('id',$contact->id)->update([
            'status' => 1
        ]);
        return $data;
    }
    public function sendConfirmMail($email,$data,$url)
    {
        $template_data = ['email'=>$email, 'url_nav'=>$url, 
        'verification_token'=>$data['verification_token'], 'data'=>$data, 'url' => $url];
        Mail::send('mail.contact.contact_confirmation', $template_data, function($message) use($email){
                $message->to($email)->subject('Contact Confirmation');
        });
        // dispatch(
        //     new SendEmailJob(
        //         new ContactConfirmation(
        //             $email,
        //             $data,
        //             $url
        //         )
        //     )
        // );
        return true;
    }

    public function confirmContact(Request $request): Response
    {
        $exist = ContactEmails::where('verification_token', $request->get('token'))->where('status',0)->first();
        if (!$exist) {
            return response()->error('Invalid token', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $decode_data = json_decode($exist->email_data);
        if ($exist->type == 'offer') {
            $offer = Offer::where('id',$decode_data->offer)->first();
            dispatch(
                new SendEmailJob(
                    new OfferMail(
                        $decode_data->email,
                        $decode_data->name,
                        $decode_data->message,
                        $decode_data->wantToSee,
                        $offer
                    )
                )
            );
            $this->updateRecord($exist);
        }
        if ($exist->type == 'profile') {
            $user = User::where('id',$decode_data->user)->first();
            dispatch(
                new SendEmailJob(
                    new UserProfileContact(
                        $decode_data->email,
                        $decode_data->name,
                        $decode_data->message,
                        $user
                    )
                )
            );
            $this->updateRecord($exist);
        }
        if ($exist->type == 'contact') {
            dispatch(
                new SendEmailJob(
                    new ContactForm(
                        $decode_data->email,
                        $decode_data->name,
                        $decode_data->message,
                        $decode_data->topic
                    )
                )
            );
            $this->updateRecord($exist);
        }
        return response()->success('', Response::HTTP_NO_CONTENT);   
    }
}
