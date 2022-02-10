<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Managers\FaqManager;

class FaqController extends Controller
{
    /** @var FaqManager  */
    private $faqManager;

    public function __construct(FaqManager $faqManager)
    {
        $this->faqManager = $faqManager;
    }

    public function index()
    {
        return $this->faqManager->getAll();
    }
}
