<?php

declare(strict_types=1);

namespace Http\Controllers\Api\Client;

use App\Laravue\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testShow(): void
    {
        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl'
        ]);

        $userRole = Role::findByName(\App\Laravue\Acl::ROLE_USER);
        $user->syncRoles($userRole);

        factory(UserProfile::class)->create(['user_id' => $user->id]);

        Sanctum::actingAs(
            $user
        );

        $response = $this->request('GET', route('user.profile'));
        $response->assertStatus(Response::HTTP_OK);

        $content = json_decode($response->getContent(), true);
        $this->assertEquals($user->profile->name, $content['name']);
        $this->assertEquals($user->profile->phone, $content['phone']);
        $this->assertEquals($user->profile->address, $content['address']);
        $this->assertEquals($user->profile->newsletter, $content['newsletter']);
    }

    public function testShow403(): void
    {
        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl'
        ]);

        Sanctum::actingAs(
            $user
        );

        $response = $this->request('GET', route('user.profile'));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testShow401(): void
    {
        factory(User::class)->create([
            'email' => 'test@dazu.pl'
        ]);

        $response = $this->request('GET', route('user.profile'));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdate()
    {
        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl'
        ]);

        $userRole = Role::findByName(\App\Laravue\Acl::ROLE_USER);
        $user->syncRoles($userRole);

        factory(UserProfile::class)->create(['user_id' => $user->id]);

        Sanctum::actingAs(
            $user
        );

        $newData = [
            'name' => 'Tester1',
            'phone' => 123123123,
            'address' => 'Warsaw',
        ];

        $response = $this->request(
            'PUT',
            route('user.profile.update'),
            $newData
        );
        $response->assertStatus(Response::HTTP_OK);

        $content = json_decode($response->getContent(), true);
        $this->assertEquals($newData['name'], $content['name']);
        $this->assertEquals($newData['phone'], $content['phone']);
        $this->assertEquals($newData['address'], $content['address']);
    }

    public function testUpdate403()
    {
        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl'
        ]);

        factory(UserProfile::class)->create(['user_id' => $user->id]);

        Sanctum::actingAs(
            $user
        );

        $newData = [
            'name' => 'Tester1',
            'phone' => 123123123,
            'address' => 'Warsaw',
        ];

        $response = $this->request(
            'PUT',
            route('user.profile.update'),
            $newData
        );
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
    public function testUpdate401()
    {
        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl'
        ]);

        factory(UserProfile::class)->create(['user_id' => $user->id]);

        $newData = [
            'name' => 'Tester1',
            'phone' => 123123123,
            'address' => 'Warsaw',
        ];

        $response = $this->request(
            'PUT',
            route('user.profile.update'),
            $newData
        );
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
