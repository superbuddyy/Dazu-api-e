<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Enums\FooterStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Footer\FooterCollection;
use App\Http\Resources\Footer\FooterResource;
use App\Managers\FooterManager;
use App\Models\Footer;
use Symfony\Component\HttpFoundation\Response;

class FooterController extends Controller
{
    /** @var FooterManager */
    protected $footerManager;

    public function __construct(FooterManager $footerManager)
    {
        $this->footerManager = $footerManager;
    }

    public function lastPost(): Response
    {
        return response()->success(new FooterResource(Footer::where('status', FooterStatus::ACTIVE)->latest()->first()));
    }

    public function index(Footer $post): Response
    {
        $footers = $this->footerManager->getList(5, FooterStatus::ACTIVE, true);
        return response()->success(new FooterCollection($footers));
    }

    public function show(Footer $post): Response
    {
        return response()->success(new FooterResource($post));
    }
}
