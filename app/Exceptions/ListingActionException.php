<?php

namespace App\Exceptions;

use Exception;

class ListingActionException extends Exception
{
    public function __construct(string $message, public readonly int $status = 409)
    {
        parent::__construct($message);
    }
}
