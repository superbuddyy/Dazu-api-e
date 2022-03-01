<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pages extends Model
{
	/** @var string  */
    protected $table = 'pages';
    
    /** @var array */
    protected $fillable = [ 'page_key', 'name', 'content' ];
}
