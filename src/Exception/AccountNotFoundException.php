<?php 

namespace App\Exception;

class AccountNotFoundException extends \Exception
{
    protected  $message = 'Account not found';

}