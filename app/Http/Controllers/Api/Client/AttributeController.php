<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Client;

use App\Http\Resources\Attributes\AttributesCollection;
use App\Models\Attribute;

class AttributeController
{

    public function __construct()
    {
        //
    }

    public function index()
    {
        $attributes = Attribute::all();
        return response()->success(new AttributesCollection($attributes));
    }
}
