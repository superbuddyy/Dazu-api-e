<?php

declare(strict_types=1);

namespace App\Http\Resources\PreviewOffer;

use App\Http\Resources\Base\ResourceCollection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class PreviewOfferCollection extends ResourceCollection
{
    /**
     * @var false|mixed
     */
    private $isExtended;

    /**
     * @param mixed $resource
     * @param bool $isExtended
     */
    public function __construct($resource, $isExtended = false)
    {
        parent::__construct($resource);
        $this->isExtended = $isExtended;
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
            if ($this->isExtended) {
                return (new OfferExtendedResource($ad));
            }
            return (new OfferResource($ad));
        });

        return parent::toArray($request);
    }
}
