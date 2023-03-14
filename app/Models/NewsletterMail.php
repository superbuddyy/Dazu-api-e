<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterMail extends Model
{
    /** @var array */
    protected $fillable = [ 'title', 'content', 'receiver' ];
}
