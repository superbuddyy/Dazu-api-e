<?php

declare(strict_types=1);

namespace App\Http\Resources\Attributes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AttributesCollection extends ResourceCollection
{
    /**
     * @param mixed $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request|FormRequest $request
     * @return array
     */
    public function toArray($request): array
    {
        $attributes = [];
        foreach ($this->collection as $attribute) {
            $attributes[(string)'_' . $attribute->id] = AttributeResource::make($attribute)->resolve();
        }

        return $attributes;
    }
}
