<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Popup extends Model
{
    public const IMAGES_PATH = 'popups';

    protected $fillable = ['title', 'content', 'image', 'show_again_after'];

    public function getFileAttribute(): ?array
    {
        $pathname = self::IMAGES_PATH . (!Str::endsWith(self::IMAGES_PATH, '/') ? '/' : '') . $this->attributes['image'];

        return [
            'original_name' => $this->attributes['image'],
            'path_name' => $pathname,
            'url' => config('app.url') . '/storage/' . $pathname,
        ];
    }
}
