<?php

namespace App\Exceptions\Custom;

use Exception;

class InvalidCredentialsException extends Exception
{
    protected $message = 'Invalid credentials';
    protected $code = 401;
}

class UserStatusNotActiveException extends Exception
{
    protected $message = 'User status not active';
    protected $code = 403;
}

class UnexpectedErrorException extends Exception
{
    protected $message = 'An unexpected error occurred';
    protected $code = 500;
}
