<?php

namespace SegfaultInc\Finite\Exceptions;

use Exception;

class SubjectInInvalidStateException extends Exception
{
    public static function new(string $state): self
    {
        return new self("Subject is in invalid state [$state].");
    }
}
