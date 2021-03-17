<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Attributes\AttributesCollection;
use App\Managers\CategoryManager;
use App\Models\Attribute;

class CategoryController
{
    /** @var CategoryManager */
    protected $categoryManager;

    public function __construct(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    public function index()
    {
        return response()->success($this->categoryManager->getCategoryTree());
    }
}
