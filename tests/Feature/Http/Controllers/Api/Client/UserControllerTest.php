<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api\Client;

use App\Enums\AvatarType;
use App\Laravue\Acl;
use App\Laravue\Models\Role;
use App\Models\Avatar;
use App\Models\Company;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreAgent(): void
    {
        $company = factory(Company::class)->create();

        $companyUser = factory(User::class)->create([
            'email' => 'test@dazu.pl',
            'company_id' => $company->id,
            'own_company_id' => $company->id,
        ]);

        $userRole = Role::findByName(Acl::ROLE_COMPANY);
        $companyUser->syncRoles($userRole);

        factory(UserProfile::class)->create(['user_id' => $companyUser->id]);

        Sanctum::actingAs(
            $companyUser
        );

        $response = $this->request(
            'POST',
            route('user.agent.store'),
            ['email' => 'agent@dazu.app', 'name' => 'agent']
        );

        $response->assertStatus(Response::HTTP_CREATED);
        $content = json_decode($response->getContent(), true);
        $this->assertEquals('agent', $content['name']);
        $this->assertEquals('agent@dazu.app', $content['email']);
    }

    public function testStoreAgent403(): void
    {
        $agentUser = factory(User::class)->create([
            'email' => 'test@dazu.pl'
        ]);

        $agentUser->syncRoles(Acl::ROLE_AGENT);

        factory(UserProfile::class)->create(['user_id' => $agentUser->id]);

        Sanctum::actingAs(
            $agentUser
        );

        $response = $this->request(
            'POST',
            route('user.agent.store'),
            ['email' => 'agent@dazu.app', 'name' => 'agent']
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testStoreAgent401(): void
    {
        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl'
        ]);

        $user->syncRoles(Acl::ROLE_USER);

        $response = $this->request(
            'POST',
            route('user.agent.store'),
            ['email' => 'agent@dazu.app', 'name' => 'agent']
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeleteAgent(): void
    {
        $company = factory(Company::class)->create();
        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl',
            'company_id' => $company->id,
            'own_company_id' => $company->id,
        ]);

        $userRole = Role::findByName(Acl::ROLE_COMPANY);
        $user->syncRoles($userRole);

        factory(UserProfile::class)->create(['user_id' => $user->id]);

        $agent = factory(User::class)->create([
            'email' => 'agent@dazu.pl',
            'company_id' => $company->id,
        ]);
        $agentRole = Role::findByName(Acl::ROLE_USER);
        $agent->syncRoles($agentRole);

        Sanctum::actingAs(
            $user
        );

        $response = $this->request(
            'DELETE',
            route('user.agent.delete', ['user' => $agent->id])
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertSoftDeleted('users', [
            'id' => $agent->id,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    public function testDeleteAgentWithDifferentCompanyThenUser(): void
    {
        $company = factory(Company::class)->create();
        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl'
        ]);

        $userRole = Role::findByName(Acl::ROLE_USER);
        $user->syncRoles($userRole);
        $user->company()->associate($company);
        factory(UserProfile::class)->create(['user_id' => $user->id]);

        $agent = factory(User::class)->create([
            'email' => 'agent@dazu.pl'
        ]);
        $company2 = factory(Company::class)->create();
        $agentRole = Role::findByName(Acl::ROLE_USER);
        $agent->syncRoles($agentRole);
        $agent->company()->associate($company2);


        Sanctum::actingAs(
            $user
        );

        $response = $this->request(
            'DELETE',
            route('user.agent.delete', ['user' => $agent->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseHas('users', [
            'id' => $agent->id,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    public function testDeleteAvatar(): void
    {
        $user = factory(User::class)->create([
            'email' => 'user@example.com',
        ]);

        $userRole = Role::findByName(Acl::ROLE_USER);
        $user->syncRoles($userRole);

        $avatar = factory(Avatar::class)->create(['user_id' => $user->id]);

        Sanctum::actingAs(
            $user
        );

        $response = $this->request(
            'DELETE',
            route('user.avatar.delete', ['avatar_type' => AvatarType::PHOTO])
        );
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('avatars', [
            'id' => $avatar->id,
        ]);
    }

    public function testDeleteVideoAvatar(): void
    {
        $user = factory(User::class)->create([
            'email' => 'user@example.com',
        ]);

        $userRole = Role::findByName(Acl::ROLE_USER);
        $user->syncRoles($userRole);

        $avatar = Avatar::create([
            'user_id' => $user->id,
            'is_active' => true,
            'expire_date' => Carbon::now(),
            'type' => AvatarType::VIDEO_URL,
            'file' => 'https://picsum.photos/200/300'
        ]);

        Sanctum::actingAs(
            $user
        );

        $response = $this->request(
            'DELETE',
            route('user.avatar.delete', ['avatar_type' => AvatarType::VIDEO_URL])
        );
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('avatars', [
            'id' => $avatar->id,
        ]);
    }

    public function testDeleteAvatarWithoutAvatars(): void
    {
        $user = factory(User::class)->create([
            'email' => 'user@example.com',
        ]);

        $userRole = Role::findByName(Acl::ROLE_USER);
        $user->syncRoles($userRole);

        Sanctum::actingAs(
            $user
        );

        $response = $this->request(
            'DELETE',
            route('user.avatar.delete', ['avatar_type' => AvatarType::PHOTO])
        );
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteAvatar401(): void
    {
        $response = $this->request(
            'DELETE',
            route('user.avatar.delete', ['avatar_type' => AvatarType::PHOTO])
        );
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
