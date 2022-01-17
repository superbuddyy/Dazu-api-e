<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactEmails extends Model
{
    /** @var string  */
    protected $table = 'contact_emails';

    /** @var array */
    protected $fillable = [ 'email_data', 'verification_token', 'status','type' ];
}
