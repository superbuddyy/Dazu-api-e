<?php

declare(strict_types=1);

namespace App\Http\Resources\User;

use App\Http\Resources\Base\ResourceCollection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UserCollection extends ResourceCollection
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
        $this->collection->transform(function ($ad) {
            return (new UserResource($ad));
        });

        return parent::toArray($request);
    }
}
