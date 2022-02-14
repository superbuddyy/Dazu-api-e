<?php

namespace App\Exceptions;

use Exception;

class UserExists extends Exception
{
    protected $message = 'email_already_exist';
}
