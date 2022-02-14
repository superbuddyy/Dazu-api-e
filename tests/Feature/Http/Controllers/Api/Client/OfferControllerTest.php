<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api\Client;

use App\Enums\OfferStatus;
use App\Enums\OfferType;
use App\Laravue\Acl;
use App\Laravue\Models\Role;
use App\Models\Category;
use App\Models\Company;
use App\Models\Offer;
use App\Models\Attribute;
use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OfferControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testShow(): void
    {
        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl'
        ]);
        factory(UserProfile::class)->create(['user_id' => $user->id]);

        $offer = factory(Offer::class)->create([
            'status' => OfferStatus::ACTIVE,
            'user_id' => $user->id
        ]);

        $response = $this->request('GET', route('offers.show', ['offer' => $offer->slug]));
         $response->assertStatus(Response::HTTP_OK);

        $content = json_decode($response->getContent(), true);
        $this->assertEquals($offer->title, $content['title']);
        $this->assertEquals($offer->description, $content['description']);
        $this->assertEquals($offer->price, $content['price']);
        $this->assertEquals($offer->id, $content['id']);
        $this->assertEquals($offer->type, $content['type']);
    }

    public function testShowInActiveByOwner(): void
    {
        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl'
        ]);
        factory(UserProfile::class)->create(['user_id' => $user->id]);

        $offer = factory(Offer::class)->create([
            'status' => OfferStatus::IN_ACTIVE,
            'user_id' => $user->id
        ]);

        Sanctum::actingAs(
            $user
        );

        $response = $this->request('GET', route('offers.show', ['offer' => $offer->slug]));
        $response->assertStatus(Response::HTTP_OK);

        $content = json_decode($response->getContent(), true);
        $this->assertEquals($offer->title, $content['title']);
        $this->assertEquals($offer->description, $content['description']);
        $this->assertEquals($offer->price, $content['price']);
        $this->assertEquals($offer->id, $content['id']);
        $this->assertEquals($offer->type, $content['type']);
    }

    public function testShowInActiveByCompanyAgent(): void
    {
        $company = factory(Company::class)->create([
            'name' => 'testCorp'
        ]);

        $agent = factory(User::class)->create([
            'email' => 'agent@dazu.pl',
            'company_id' => $company->id
        ]);

        $agentRole = Role::findByName(Acl::ROLE_AGENT);
        $agent->syncRoles($agentRole);

        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl',
            'company_id' => $company->id,
            'own_company_id' => $company->id
        ]);
        $companyRole = Role::findByName(Acl::ROLE_COMPANY);
        $user->syncRoles($companyRole);

        factory(UserProfile::class)->create(['user_id' => $user->id]);

        $offer = factory(Offer::class)->create([
            'status' => OfferStatus::IN_ACTIVE,
            'user_id' => $user->id
        ]);

        Sanctum::actingAs(
            $agent
        );

        $response = $this->request('GET', route('offers.show', ['offer' => $offer->slug]));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShowInActiveByCompanyOwner(): void
    {
        $company = factory(Company::class)->create([
            'name' => 'testCorp'
        ]);

        $agent = factory(User::class)->create([
            'email' => 'agent@dazu.pl',
            'company_id' => $company->id
        ]);

        $agentRole = Role::findByName(Acl::ROLE_AGENT);
        $agent->syncRoles($agentRole);

        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl',
            'company_id' => $company->id,
            'own_company_id' => $company->id
        ]);
        $companyRole = Role::findByName(Acl::ROLE_COMPANY);
        $user->syncRoles($companyRole);

        factory(UserProfile::class)->create(['user_id' => $user->id]);

        $offer = factory(Offer::class)->create([
            'status' => OfferStatus::IN_ACTIVE,
            'user_id' => $agent->id
        ]);

        Sanctum::actingAs(
            $user
        );

        $response = $this->request('GET', route('offers.show', ['offer' => $offer->slug]));
        $response->assertStatus(Response::HTTP_OK);

        $content = json_decode($response->getContent(), true);
        $this->assertEquals($offer->title, $content['title']);
        $this->assertEquals($offer->description, $content['description']);
        $this->assertEquals($offer->price, $content['price']);
        $this->assertEquals($offer->id, $content['id']);
        $this->assertEquals($offer->type, $content['type']);
    }

    public function testShow404InActiveOffers()
    {
        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl'
        ]);
        factory(UserProfile::class)->create(['user_id' => $user->id]);

        $ad = factory(Offer::class)->create([
            'status' => OfferStatus::IN_ACTIVE,
            'user_id' => $user->id
        ]);

        $response = $this->request('GET', route('offers.show', ['offer' => $ad->slug]));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShow404InActiveByUserOffers()
    {
        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl'
        ]);
        factory(UserProfile::class)->create(['user_id' => $user->id]);

        $ad = factory(Offer::class)->create([
            'status' => OfferStatus::IN_ACTIVE_BY_USER,
            'user_id' => $user->id
        ]);

        $response = $this->request('GET', route('offers.show', ['offer' => $ad->slug]));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShow404ExpiredOffer()
    {
        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl'
        ]);
        factory(UserProfile::class)->create(['user_id' => $user->id]);

        $ad = factory(Offer::class)->create([
            'status' => OfferStatus::ACTIVE,
            'user_id' => $user->id
        ]);

        $ad->update([
            'expire_time' => Carbon::now()->subDays(2)
        ]);

        $response = $this->request('GET', route('offers.show', ['offer' => $ad->slug]));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShow404NotExist()
    {
        $user = factory(User::class)->create([
            'email' => 'test@dazu.pl'
        ]);
        factory(UserProfile::class)->create(['user_id' => $user->id]);

        factory(Offer::class)->create([
            'status' => OfferStatus::ACTIVE,
            'user_id' => $user->id
        ]);

        $response = $this->request('GET', route('offers.show', ['offer' => 'no-exist']));
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    // TODO:: Fix test
//    public function testStoreOffer()
//    {
//        $category = factory(Category::class)->create();
//        $user = factory(User::class)->create([
//            'email' => 'test@dazu.pl'
//        ]);
//        factory(UserProfile::class)->create(['user_id' => $user->id]);
//
//
//        factory(Attribute::class)->create();
//        $userRole = Role::findByName(\App\Laravue\Acl::ROLE_USER);
//        $user->syncRoles($userRole);
//
//        Sanctum::actingAs(
//            $user
//        );
//
//        $body = [
//            'title' => 'House',
//            'description' => 'Lorem ipsum house',
//            'price' => 1000,
//            'lat' => '0',
//            'lon' => '0',
//            'location_name' => 'Wwa',
//            'category' => $category->slug,
//            'attributes' => [
//                1 => 'sprzedaz',
//                2 => 1000
//            ],
//            'files' => [UploadedFile::fake()->image('house.jpg', 400, 400)]
//        ];
//
//        $response = $this->request('POST', route('offers.store'), $body);
//        $response->assertStatus(Response::HTTP_CREATED);
//
//        $content = json_decode($response->getContent(), true);
//
//        $this->assertDatabaseHas('offers', [
//            'id' => $content['id'],
//        ]);
//        $this->assertEquals($body['title'], $content['title']);
//        $this->assertEquals($body['description'], $content['description']);
//        $this->assertEquals('house', $content['slug']);
//        $this->assertNotNull($content['main_photo']['file']['original_name']);
//        $this->assertNotNull($content['main_photo']['file']['path_name']);
//        $this->assertEquals($body['type'], $content['type']);
//    }
}
