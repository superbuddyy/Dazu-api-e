<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\SlugableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Footer extends Model
{
    protected $table = 'footers';

    /** @var array */
    protected $fillable = [ 'title', 'name', 'content' ];

}
