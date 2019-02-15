<?php

namespace SegfaultInc\Finite\Exceptions;

use Exception;

class InvalidStateException extends Exception
{
    public static function new(string $state): self
    {
        return new self("State [$state] does not exist.");
    }
}
