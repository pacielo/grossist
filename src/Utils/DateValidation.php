<?php

namespace App\Utils;

class DateValidation implements DateValidationInterface
{
    public function validate($date, $format = 'Y-m-d H:i:s'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) === $date && $d->format('Y') > 2020) {
            return true;
        }
        return false;
    }
}
