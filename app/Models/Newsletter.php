<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    /** @var string  */
    protected $table = 'newsletter';

    /** @var array */
    protected $fillable = [ 'email', 'token', 'status' ];
}
