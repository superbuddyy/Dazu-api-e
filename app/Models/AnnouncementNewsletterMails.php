<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementNewsletterMails extends Model
{
	/** @var string  */
    protected $table = 'announcement_newsletter_mails';
    
    /** @var array */
    protected $fillable = [ 'title', 'content' ];
}
