<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'line_items' => $this->line_items,
            'description' => $this->description,
            'number' => $this->number,
            'total' => $this->total,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
