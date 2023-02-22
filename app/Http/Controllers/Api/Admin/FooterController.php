<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewsletterEmailJob;
use App\Models\Footer;
use App\Managers\FooterManager;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FooterController extends Controller
{
    /** @var FooterManager */
    protected $footerManager;

    public function index(): Response
    {
        $result = Footer::query()->paginate(config('dazu.pagination.per_page'));

        return response()->success($result);
    }

    public function store(Request $request): Response
    {
        $post = $this->footerManager->store(
            $request->get('title'),
            $request->get('content'),
            $request->get('name')
        );

        return response()->success($post, Response::HTTP_CREATED);
    }
}
