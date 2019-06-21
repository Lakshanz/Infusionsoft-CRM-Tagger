<?php


namespace App\Exceptions;


class CustomerNotFoundException extends \Exception
{
    protected $message = "Customer not found.";
    protected $code = 404;
}