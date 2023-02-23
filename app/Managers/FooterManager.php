<?php

declare(strict_types=1);

namespace App\Managers;

use App\Enums\OfferStatus;
use App\Enums\AttributeType;
use App\Enums\OfferType;
use App\Enums\PostStatus;
use App\Enums\FooterStatus;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Photo;
use App\Models\Post;
use App\Models\Footer;
use App\Models\User;
use App\Services\ImageService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\FileBag;

class FooterManager
{
    /**
     * @param int $perPage
     * @param string|null $status
     * @param bool $randomOrder
     * @return mixed
     */
    public function getList(int $perPage, ?string $status = null, bool $randomOrder = false)
    {
        $query = Footer::query();

        if ($status != null) {
            $query->where('status', $status);
        }

        if ($randomOrder) {
            $query->inRandomOrder();
        }

        return $query->paginate($perPage);
    }

    public function getAll() {
        $data = Footer::all();
        foreach ($data as $key) {
            $key['content'] = html_entity_decode($key['content']);
        }
        return $data;
        // return FaqItem::orderBy('id', 'ASC')->get();
    }

    public function getItem($id)
    {
        return Footer::findOrFail($id);
    }

    /**
     * @param string $title
     * @param string $content
     * @param File $mainImage
     * @param string $status
     * @param string|null $userId
     * @return mixed
     */
    public function store(
        string $title,
        string $content,
        string $name,
        string $status = FooterStatus::ACTIVE,
        string $userId = null
    ) {
       
        $userId = $userId ?: Auth::id();
        return Footer::create(
            [
                'title' => $title,
                'content' => $content,
                'status' => $status, // Default
                'name' => $name,
                'user_id' => $userId,
            ]
        );
    }

    /**
     * @param Post $post
     * @param string $title
     * @param string $content
     * @param File|null $mainImage
     * @param string $status
     * @return mixed
     */
    public function update(
        Footer $post,
        string $title,
        string $content,
        string $name,
        string $status = FooterStatus::ACTIVE
    ) {
        $toUpdate = [
            'title' => $title,
            'content' => $content,
            'name' => $name,
            'status' => $status, // Default
        ];

        return $post->update($toUpdate);
    }

    /**
     * @param Post $post
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Footer $post)
    {
        return $post->delete();
    }
}
