<?php

declare(strict_types=1);

namespace App\Managers;

use App\Enums\AvatarType;
use App\Enums\CompanyType;
use App\Exceptions\UserExists;
use App\Laravue\Acl;
use App\Laravue\Models\Role;
use App\Models\Avatar;
use App\Models\PreviewOffer;
use App\Models\PreviewAvatar;
use App\Models\Company;
use App\Models\Photo;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\ImageService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserManager
{
    public function createUser(string $email, string $password, string $name, string $type): User
    {
        if (User::withTrashed()->where('email', $email)->exists()) {
            throw new UserExists();
        }
        /** @var User $user */
        $user = User::create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        if ($type === CompanyType::AGENCY) {
            $company = new Company();
            $company->name = $name;
            $company->type = $type;
            $company->save();

            $user->company_id = $company->id;
            $user->own_company_id = $company->id;

            $role = Role::findByName(Acl::ROLE_COMPANY);
            $user->syncRoles($role);
        } else if ($type === CompanyType::DEVELOPER){
            $role = Role::findByName(Acl::ROLE_DEVELOPER);
            $user->syncRoles($role);
        } else {
            $role = Role::findByName(Acl::ROLE_USER);
            $user->syncRoles($role);
        }

        $profile = new UserProfile();
        $profile->name = $name;
        $user->profile()->save($profile);

        $user->save();

        return $user;
    }

    public function getUserCompanyAgents(): LengthAwarePaginator
    {
        return User::query()
            ->whereHas('roles', function ($query) {
                return $query->where('name', Acl::ROLE_AGENT);
            })
            ->whereHas('company', function ($query) {
                return $query->where('id', Auth::user()->company_id);
            })->paginate(25);
    }

    /**
     * @param string $email
     * @param string $name
     * @return User
     */
    public function storeAgent(string $email, string $name): User
    {
        /** @var User $user */
        $user = User::create(['email' => $email, 'password' => Hash::make(Str::random(6))]);
        $profile = new UserProfile();
        $profile->name = $name;
        $user->profile()->save($profile);

        $user->syncRoles(Acl::ROLE_AGENT);

        return $user;
    }

    /**
     * @param User $user
     * @param $file
     * @param false $isActive
     * @return false|\Illuminate\Database\Eloquent\Model
     */
    public function storeAvatar(User $user, $file, $isActive = false)
    {
        $this->removeAvatars($user);
        $imageService = resolve(ImageService::class);
        $photo = Avatar::make([
            'file' => $imageService->store($file, Avatar::class),
            'expire_date' => Carbon::now()->addDays(30),
            'is_active' => $isActive,
            'type' => AvatarType::PHOTO
        ]);

        return $user->avatars()->save($photo);
    }

    /**
     * @param User $user
     * @param string $url
     * @param false $isActive
     * @return false|\Illuminate\Database\Eloquent\Model
     */
    public function storeVideoAvatar(User $user, string $url, $isActive = false)
    {
        $this->removeAvatars($user, AvatarType::VIDEO_URL);
        $videoAvatar = Avatar::make([
            'file' => $url,
            'expire_date' => Carbon::now()->addDays(30),
            'is_active' => $isActive,
            'type' => AvatarType::VIDEO_URL
        ]);

        return $user->avatars()->save($videoAvatar);
    }

    public function removeAvatars(User $user, string $avatarType = AvatarType::PHOTO)
    {
        $avatars = $user->avatars()->where('type', $avatarType);
        if ($avatarType === AvatarType::PHOTO) {
            $imageService = resolve(ImageService::class);
            foreach ($avatars->get() as $avatar) {
                $imageService->delete($avatar->file['path_name']);
            }
        }

        return $avatars->delete();
    }

    public function migrateImages(PreviewOffer  $p_offer, User $model) {
        $photos = PreviewAvatar::where('preview_offer_id',$p_offer->id)->get();
        foreach ($photos as $photo) {
            $avt = Avatar::make([
                'file' => $photo->file['original_name'],
                'expire_date' => Carbon::now()->addDays(30),
                'is_active' => $photo->is_active,
                'type' => $photo->type
            ]);

            $model->avatars()->save($avt);
            // $avt->delete();
        }
        return true;
    }

    /** Payment for avatar
     * @param array $cachedInfo
     * @return Avatar|Company
     */
    public function handlePaymentCallback(array $cachedInfo)
    {
        $user = User::findOrFail($cachedInfo['user_id']);

        if ($cachedInfo['context'] === 'avatar_' . AvatarType::PHOTO) {
            $user->getAvatar(AvatarType::PHOTO)->update(['is_active' => true, 'expire_date' => Carbon::now()->addDays(30)]);
        }

        if ($cachedInfo['context'] === 'avatar_' . AvatarType::VIDEO_URL) {
            $user->getAvatar(AvatarType::VIDEO_URL)->update(['is_active' => true, 'expire_date' => Carbon::now()->addDays(30)]);
            return $user->videoAvatar;
        }

        return $user->avatar;
    }

    public function destroy(User $user = null)
    {
        $user = $user ?: Auth::user();
        $user->offers()->delete();
        return $user->delete();
    }
}
