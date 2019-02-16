<?php

namespace SegfaultInc\Finite\Exceptions;

use Exception;
use SegfaultInc\Finite\State;
use SegfaultInc\Finite\Transition;
use SegfaultInc\Finite\Support\Collection;

class ConfigurationException extends Exception
{
    public static function noInitialState(): self
    {
        return self::new('There must exist a single initial state. None present.');
    }

    public static function multipleInitialStates(Collection $initial): self
    {
        $states = $initial
            ->map(function (State $state) {
                return $state->key;
            })
            ->implode(', ');

        return self::new("There must exist a single initial state. Multiple present [$states].");
    }

    public static function duplicateKeys(Collection $duplicates): self
    {
        return self::new('Every state much have a unique key. Contains duplicate keys ['.$duplicates->implode(', ').'].');
    }

    public static function nonDeterministicTransitions(Collection $transitions): self
    {
        $nonDeterministic = $transitions
            ->map(function (Transition $transition) {
                return "[{$transition->toString()}]";
            })
            ->implode(', ');

        return self::new(
            'There are %s transitions coming out of [%s] with same input [%s]. Specifically: %s.',
            $transitions->count(),
            $transitions->first()->from()->key,
            $transitions->first()->input(),
            $nonDeterministic
        );
    }

    public static function new(...$args)
    {
        return new self(sprintf(...$args));
    }
}
