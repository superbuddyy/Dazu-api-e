<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Api\Client;

use App\Enums\OfferStatus;
use App\Enums\OfferType;
use App\Laravue\Models\Role;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Attribute;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex(): void
    {
        $category = Category::create([
            'name' => 'Garaż',
            'children' => [
                ['name' => 'Drewniany'],
                ['name' => 'Murowany'],
                ['name' => 'Metalowy'],
                ['name' => 'Wolnostojący'],
                ['name' => 'Podziemny'],
                ['name' => 'Inna'],
            ],
        ]);
        $response = $this->request('GET', route('categories.index'));
        $response->assertStatus(Response::HTTP_OK);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals($category->name, $content[0]['name']);
        $this->assertTrue($content[0]['is_active']);
        $this->assertCount(6, $content[0]['children']);
    }
}
