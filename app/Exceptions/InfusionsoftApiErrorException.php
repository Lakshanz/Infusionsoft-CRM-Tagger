<?php


namespace App\Exceptions;


class InfusionsoftApiErrorException extends \Exception
{
    protected $message = "Api Connection Error.";

    protected $code = 500;
}