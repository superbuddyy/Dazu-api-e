<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewsletterEmailJob;
use App\Models\NewsletterMail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsletterMailController extends Controller
{
    public function index(): Response
    {
        $result = NewsletterMail::query()->paginate(config('dazu.pagination.per_page'));

        return response()->success($result);
    }

    public function store(Request $request): Response
    {
        $result = NewsletterMail::create(['title' => $request->title, 'content' => $request->get('content'), 'receiver' => $request->receiver]);
        dispatch(new SendNewsletterEmailJob($result));

        return response()->success($result, Response::HTTP_CREATED);
    }
}
