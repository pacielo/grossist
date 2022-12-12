<?php

namespace App\Validator\UserManagement;

use App\Repository\UserManagement\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RegisteredUserValidator extends ConstraintValidator
{
    private $userRepository;

    public function __construct(UserRepository $repository)
    {
        $this->userRepository = $repository;
    }

    public function validate($value, Constraint $constraint): void
    {
        /* @var $constraint \App\Validator\RegisteredUser */

        if (null === $value || '' === $value) {
            return;
        }

        $existingUser = $this->userRepository->findOneBy(['email' => $value, 'isEnable'=>true]);

        if (null === $existingUser) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
