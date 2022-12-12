<?php

namespace App\Validator\UserManagement;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConfirmEmailValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
      
    }
}
