<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlackList;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlackListController extends Controller
{
    public function index(): Response
    {
        $result = BlackList::query()->paginate(config('dazu.pagination.per_page'));

        return response()->success($result);
    }

    public function store(Request $request): Response
    {
        $result = BlackList::create(['word' => $request->word]);

        return response()->success($result, Response::HTTP_CREATED);
    }

    public function destroy(BlackList $blackList): Response
    {
        return response()->success($blackList->delete());
    }
}
