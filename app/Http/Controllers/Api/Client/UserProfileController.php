<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Events\User\AgentCreated;
use App\Events\User\NewsletterActivated;
use App\Events\User\NewsletterDeactivated;
use App\Http\Resources\User\UserProfileResource;
use App\Managers\UserManager;
use App\Managers\UserProfileManager;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController
{
    /**
     * @var UserProfileManager
     */
    private $userProfileManager;

    /**
     * UserProfileManager constructor.
     * @param UserProfileManager $userProfileManager
     */
    public function __construct(UserProfileManager $userProfileManager)
    {
        $this->userProfileManager = $userProfileManager;
    }

    public function show()
    {
        $userProfile = $this->userProfileManager->getItem();
        return response()->success(new UserProfileResource($userProfile));
    }

    public function update(Request $request)
    {
        $profile = $this->userProfileManager->update($request->only(
            ['name', 'phone', 'city', 'street', 'zip_code', 'country', 'nip']
        ));

        return response()->success(new UserProfileResource($profile));
    }
    
    public function updateDefaultAvatar(Request $request)
    {
        $profile = $this->userProfileManager->updateAvatar($request->get('default_avatar'));

        return response()->success(new UserProfileResource($profile));
    }

    public function toggleNewsletter()
    {
        /** @var User $user */
        $user = Auth::user();
        $newsletterStatus = $this->userProfileManager->toggleNewsletter();

        $newsletterStatus ? event(new NewsletterActivated($user)): event(new NewsletterDeactivated($user));

        return response()->success(['newsletter' => $newsletterStatus]);
    }
}
