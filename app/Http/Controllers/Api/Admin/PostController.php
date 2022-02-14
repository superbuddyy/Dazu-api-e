<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
use App\Managers\PostManager;
use App\Models\Post;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    /** @var PostManager */
    protected $postManager;

    public function __construct(PostManager $postManager)
    {
        $this->postManager = $postManager;
    }

    public function index(Request $request): Response
    {
        return response()->success(
            new PostCollection(
                $this->postManager->getList(
                    (int)$request->get(
                        'limit',
                        config('dazu.pagination.per_page')
                    )
                )
            )
        );
    }

    public function show(Post $post): Response
    {
        return response()->success(new PostResource($post));
    }

    public function store(Request $request): Response
    {
        $post = $this->postManager->store(
            $request->get('title'),
            $request->get('content'),
            $request->file('main_photo')
        );

        return response()->success($post, Response::HTTP_CREATED);
    }

    public function update(Request $request, Post $post): Response
    {
        $post = $this->postManager->update(
            $post,
            $request->get('title'),
            $request->get('content'),
            $request->file('main_photo', null),
            $request->get('status')
        );

        return response()->success($post);
    }

    public function destroy(Post $post): Response
    {
        return response()->success($this->postManager->delete($post));
    }
}
