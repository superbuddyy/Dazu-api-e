<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementNewsletter extends Model
{
    /** @var string  */
    protected $table = 'annnouncement_newsletter';

    /** @var array */
    protected $fillable = [ 'email', 'token', 'status' ];
}
