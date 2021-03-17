<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Auth;

class UserProfileManager
{
    /**
     * @param User|null $user
     * @return UserProfile
     */
    public function getItem(User $user = null): UserProfile
    {
        $user = $user ?: Auth::user();
        return $user->profile;
    }

    public function store(User $user, string $name): UserProfile
    {
        $profile = new UserProfile();
        $profile->name = $name;
        $user->profile()->save($profile);

        return $user->profile;
    }

    /**
     * @param User|null $user
     * @param array $newProfileData
     * @return UserProfile
     */
    public function update(array $newProfileData, User $user = null): UserProfile
    {
        $profile = ($user ?: Auth::user())->profile;
        $profile->name = $newProfileData['name'];
        $profile->phone = $newProfileData['phone'];
        $profile->city = $newProfileData['city'];
        $profile->street = $newProfileData['street'];
        $profile->zip_code = $newProfileData['zip_code'];
        $profile->country = $newProfileData['country'];
        $profile->nip = $newProfileData['nip'];
        $profile->save();

        return $profile;
    }

    /**
     * @param User|null $user
     * @return bool
     */
    public function toggleNewsletter(User $user = null): bool
    {
        $profile = ($user ?: Auth::user())->profile;
        $profile->newsletter = !$profile->newsletter;
        $profile->save();

        return $profile->newsletter;
    }

    public function destroy(User $user = null)
    {
        //
    }
}
