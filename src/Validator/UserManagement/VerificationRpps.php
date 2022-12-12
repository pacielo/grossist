<?php

namespace App\Validator\UserManagement;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class VerificationRpps extends Constraint
{
    public $message = "Ce numéro RPPS n'existe pas dans la base nationale";
}