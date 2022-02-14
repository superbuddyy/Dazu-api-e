<?php

declare(strict_types=1);

namespace Http\Controllers\Api\Auth;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testLogin()
    {
        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl'
        ]);

        factory(UserProfile::class)->create(['user_id' => $user->id]);

        $response = $this->request(
            'POST',
            route('auth.login'),
            ['email' => $user->email, 'password' => 'password']
        );
        $content = json_decode($response->getContent(), true);

        $this->assertEquals($user->profile->name, $content['name']);
    }

    public function testSetPassword()
    {
        $user = factory(User::class)->create([
            'email' => 'test@dazu.app',
            'verification_token' => '123',
        ]);

        $response = $this->request(
            'POST',
            route('auth.set-password'),
            ['token' => '123', 'password' => 'password']
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertNull($user->fresh()->verification_token);
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
