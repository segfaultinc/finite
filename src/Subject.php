<?php

namespace SegfaultInc\Finite;

interface Subject
{
    /**
     * Returns a string representing the state of the subject in the machine.
     */
    public function getFiniteState(): string;

    /**
     * Set the state of the subject in the machine.
     */
    public function setFiniteState(string $state): void;

    /**
     * Apply given input to the subject given the machine.
     */
    public function applyTransition(string $input): void;
}
