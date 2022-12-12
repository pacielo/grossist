<?php

namespace App\Validator\UserManagement;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class RegisteredUser extends Constraint
{
    /**
     * @var string
     */
    public $message = "Nous n'avons pas réussi à trouver votre adresse de courriel ou votre compte n'est pas été activé ";
}
