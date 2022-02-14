<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;
use  Mail;
use App\Events\User\AgentCreated;
use App\Events\User\UserCreated;
use App\Http\Controllers\Api\Admin\BaseController;
use App\Http\Requests\Auth\SetPasswordRequest;
use App\Http\Resources\User\UserResource;
use App\Jobs\SendEmailJob;
use App\Laravue\Acl;
use App\Laravue\Models\Role;
use App\Mail\Auth\RemindPassword;
use App\Mail\User\NewsletterActivated as NewsletterActivatedMail;
use App\Managers\UserManager;
use App\Managers\UserProfileManager;
use App\Models\Company;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers\Api\Auth
 */
class AuthController extends BaseController
{
    /**
     * @var UserManager
     */
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->error(['error' => 'Błędny email lub hasło'], Response::HTTP_UNAUTHORIZED);
        }

        $user = $request->user();
        if ($user->email_verified_at === null) {
            return response()->error(['error' => 'Konto nie zostało zweryfikowane!'], Response::HTTP_UNAUTHORIZED);
        }
	//$template_data = ['emailBody'=>'ResetPassword', 'emailTitle'=>'Your password reseted!'];
	//Mail::send('mail.contact.reset', $template_data, function($message){
    	//$message->to('future55star@mail.ru','Artisans Web')->subject('Artisans Web Testing Mail');
	//});

        return response()->success(new UserResource($user), Response::HTTP_OK);
    }

    public function register(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            if (User::withTrashed()->where('email', $request->email)->exists()) {
                DB::rollback();
                return response()->error(['error' => 'email_already_exist']);
            }
            $user = $this->userManager->createUser(
                $request->get('email'),
                $request->get('password'),
                $request->get('name'),
                $request->get('type')
            );

            event(new UserCreated($user));
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Fail to register user', ['message' => $e->getMessage()]);
            return response()->error('Fail to register user');
        }

        return response()->success('', Response::HTTP_NO_CONTENT);
    }

    public function completeRegistration(Request $request): JsonResponse
    {
        $user = User::where('verification_token', $request->token)->firstOrFail();
        $user->email_verified_at = Carbon::now();
        $user->verification_token = null;
        $user->save();

        return response()->success('completed', Response::HTTP_OK);
    }

    public function setPassword(SetPasswordRequest $request): JsonResponse
    {
        $user = User::where('verification_token', $request->get('token'))->firstOrFail();
        $user->email_verified_at = Carbon::now();
        $user->verification_token = null;
        $user->password = Hash::make($request->get('password'));
        $user->save();

        return response()->success('', Response::HTTP_NO_CONTENT);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!Hash::check($request->oldPassword, $user->password)) {
            return response()->json(['error' => 'Niepoprawne hasło'], Response::HTTP_BAD_REQUEST);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->success('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        return response()->success('', Response::HTTP_NO_CONTENT);
    }

    public function check(): \Symfony\Component\HttpFoundation\Response
    {
       return response()->success(Auth::check(), Response::HTTP_OK);
    }

    public function remindPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            $user->verification_token = Str::uuid()->toString();
            $user->save();
            dispatch(new SendEmailJob(new RemindPassword($user)));
        }

        return response()->success('', Response::HTTP_NO_CONTENT);
    }

    public function sendmail(Request $request)
    {
	$user = User::where('email', $request->email)->first();
	$token = Hash::make($request->time);
	$link = 'https://admin.dazu.pl/#/reset?token='.$token;
	if($user) {
		$template_data = ['emailBody'=>'ResetPassword', 'emailTitle'=>'Password reset', 'link' => $link];
		Mail::send('mail.contact.reset', $template_data, function($message){
    			$message->to('future55star@mail.ru', 'Artisans Web')->subject('Artisans Web Testing Mail');
		});
	}	

	
    }
}
