<?php

namespace SegfaultInc\Finite\Tests;

use SegfaultInc\Finite\Subject;

class SampleSubject implements Subject
{
    /** @var string */
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

    public function applyTransition(string $input): void
    {
        //
    }
}
