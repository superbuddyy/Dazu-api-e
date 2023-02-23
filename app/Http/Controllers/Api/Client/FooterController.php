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
    public function index()
    {
        return $this->footerManager->getAll();
    }
     public function show(int $id)
    {
        return $this->footerManager->getItem($id);
    }
   

   
}
