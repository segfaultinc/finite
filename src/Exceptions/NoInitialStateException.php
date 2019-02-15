<?php

namespace SegfaultInc\Finite\Exceptions;

use Exception;

class NoInitialStateException extends Exception
{
    public static function new(): self
    {
        return new self('No initial state set.');
    }
}
