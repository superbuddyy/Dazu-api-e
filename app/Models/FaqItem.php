<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqItem extends Model
{
    /** @var array */
    protected $fillable = ['title', 'content', 'file'];
}
