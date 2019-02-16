<?php

namespace SegfaultInc\Finite\Tests\Stubs;

use SegfaultInc\Finite\Subject as SubjectContract;

class Subject implements SubjectContract
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
