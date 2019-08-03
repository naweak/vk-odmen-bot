<?php


namespace Exceptions;

use \Exception as Exception;
use Throwable;

class VkException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
