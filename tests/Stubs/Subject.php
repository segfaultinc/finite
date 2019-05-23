<?php

namespace SegfaultInc\Finite\Tests\Stubs;

use SegfaultInc\Finite\Subject as BaseSubject;

class Subject implements BaseSubject
{
    private $state;

    public function __construct(string $state)
    {
        $this->state = $state;
    }

    public function getFiniteState(): string
    {
        return $this->state;
    }

    public function setFiniteState(string $state): void
    {
        $this->state = $state;
    }
}
