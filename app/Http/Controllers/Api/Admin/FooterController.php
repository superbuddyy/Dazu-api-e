<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewsletterEmailJob;
use App\Models\Footer;
use App\Managers\FooterManager;
use Illuminate\Http\Request;
use App\Http\Resources\Footer\FooterCollection;
use App\Http\Resources\Footer\FooterResource;
use Symfony\Component\HttpFoundation\Response;

class FooterController extends Controller
{
    /** @var FooterManager */
    protected $footerManager;

    public function __construct(FooterManager $postManager)
    {
        $this->footerManager = $postManager;
    }

    public function index(): Response
    {
        $result = Footer::query()->paginate(config('dazu.pagination.per_page'));

        return response()->success($result);
    }
    public function show(Footer $post): Response
    {
        return response()->success(new FooterResource($post));
    }
    public function update(Request $request, Footer $post): Response
    {
        $post = $this->footerManager->update(
            $post,
            $request->get('title'),
            $request->get('content'),
            $request->get('name')
        );

        return response()->success($post);
    }

    public function store(Request $request): Response
    {
        $result = Footer::create(['title' => $request->title, 'content' => $request->get('content'),
        'name' => $request->get('name')]);
        return response()->success($result, Response::HTTP_CREATED);
    }
    public function destroy(Footer $post): Response
    {
        return response()->success($this->footerManager->delete($post));
    }
}
