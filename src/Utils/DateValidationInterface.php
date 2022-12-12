<?php

namespace App\Utils;

interface DateValidationInterface
{
    public function validate($date, $format = 'Y-m-d H:i:s'): bool;
}
