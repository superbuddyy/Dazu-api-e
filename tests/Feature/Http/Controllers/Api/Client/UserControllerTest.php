<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api\Client;

use App\Laravue\Acl;
use App\Laravue\Models\Role;
use App\Models\Company;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreAgent(): void
    {
        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl'
        ]);

        $userRole = Role::findByName(Acl::ROLE_USER);
        $user->syncRoles($userRole);

        factory(UserProfile::class)->create(['user_id' => $user->id]);

        Sanctum::actingAs(
            $user
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
        $this->assertEquals('agent', $content['roles'][0]);
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
            'email' => 'test@dazu.pl'
        ]);

        $userRole = Role::findByName(Acl::ROLE_USER);
        $user->syncRoles($userRole);
        $user->company()->associate($company);
        factory(UserProfile::class)->create(['user_id' => $user->id]);

        $agent = factory(User::class)->create([
            'email' => 'agent@dazu.pl'
        ]);
        $agentRole = Role::findByName(Acl::ROLE_USER);
        $agent->syncRoles($agentRole);
        $agent->company()->associate($company);

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
}
