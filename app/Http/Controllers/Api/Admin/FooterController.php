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
        $result = Footer::create(['title' => $request->title, 'content' => $request->get('content'),
        'name' => $request->get('name')]);
        return response()->success($result, Response::HTTP_CREATED);
    }
}
