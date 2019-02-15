<?php

namespace SegfaultInc\Finite\Exceptions;

use Exception;

class InvalidInputException extends Exception
{
    public static function new(string $state, string $input): self
    {
        return new self("There are no transitions from [$state] on input [$input].");
    }
}
