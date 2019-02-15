<?php

namespace SegfaultInc\Finite;

interface Subject
{
    /**
     * Returns string representing state in FSM.
     */
    public function getFiniteState(): string;

    /**
     * Sets state of subject in FSM.
     */
    public function setFiniteState(string $state): void;

    /**
     * Apply given input to the machine.
     */
    public function applyTransition(string $input): void;
}
