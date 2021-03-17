<?php

declare(strict_types=1);

namespace App\Managers;

use App\Enums\OfferStatus;
use App\Enums\AttributeType;
use App\Enums\OfferType;
use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Photo;
use App\Models\Post;
use App\Models\User;
use App\Services\ImageService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\FileBag;

class PostManager
{
    /**
     * @param int $perPage
     * @param string|null $status
     * @param bool $randomOrder
     * @return mixed
     */
    public function getList(int $perPage, ?string $status = null, bool $randomOrder = false)
    {
        $query = Post::query();

        if ($status != null) {
            $query->where('status', $status);
        }

        if ($randomOrder) {
            $query->inRandomOrder();
        }

        return $query->paginate($perPage);
    }

    public function getItem()
    {
        //
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
        File $mainImage,
        string $status = PostStatus::ACTIVE,
        string $userId = null
    ) {
        $imageService = resolve(ImageService::class);
        $userId = $userId ?: Auth::id();
        return Post::create(
            [
                'title' => $title,
                'content' => $content,
                'status' => $status, // Default
                'main_photo' => $imageService->store($mainImage, Post::class),
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
        Post $post,
        string $title,
        string $content,
        ?File $mainImage,
        string $status = PostStatus::ACTIVE
    ) {
        $imageService = resolve(ImageService::class);
        $toUpdate = [
            'title' => $title,
            'content' => $content,
            'status' => $status, // Default
        ];

        if ($mainImage) {
            $toUpdate['main_photo'] = $imageService->store($mainImage, Post::class);
        }

        return $post->update($toUpdate);
    }

    /**
     * @param Post $post
     * @return bool|null
     * @throws \Exception
     */
    public function delete(Post $post)
    {
        return $post->delete();
    }
}
