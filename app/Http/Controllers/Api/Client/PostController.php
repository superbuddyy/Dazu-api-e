<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
use App\Managers\PostManager;
use App\Models\Post;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    /** @var PostManager */
    protected $postManager;

    public function __construct(PostManager $postManager)
    {
        $this->postManager = $postManager;
    }

    public function lastPost(): Response
    {
        return response()->success(new PostResource(Post::where('status', PostStatus::ACTIVE)->latest()->first()));
    }

    public function index(Post $post): Response
    {
        $posts = $this->postManager->getList(5, PostStatus::ACTIVE, true);
        return response()->success(new PostCollection($posts));
    }

    public function show(Post $post): Response
    {
        return response()->success(new PostResource($post));
    }
}
