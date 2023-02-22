<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Footer\FooterCollection;
use App\Http\Resources\Footer\FooterResource;
use App\Managers\FooterManager;
use App\Models\Footer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FooterController extends Controller
{
    /** @var FooterManager */
    protected $footerManager;

    public function __construct(FooterManager $footerManager)
    {
        $this->footerManager = $footerManager;
    }

    public function index(Request $request): Response
    {
        return response()->success(
            new FooterCollection(
                $this->footerManager->getList(
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
        return response()->success(new FooterResource($post));
    }

    public function store(Request $request): Response
    {
        $post = $this->footerManager->store(
            $request->get('title'),
            $request->get('content'),
            $request->file('main_photo')
        );

        return response()->success($post, Response::HTTP_CREATED);
    }

    public function update(Request $request, Post $post): Response
    {
        $post = $this->footerManager->update(
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
        return response()->success($this->footerManager->delete($post));
    }
}
