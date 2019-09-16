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
        $keys = $initial
            ->map(function (State $state) {
                return $state->getKey();
            })
            ->implode(', ');

        return self::new("There must exist a single initial state. Multiple present [{$keys}].");
    }

    public static function duplicateKeys(Collection $duplicates): self
    {
        return self::new('Every state much have a unique key. Contains duplicate keys ['.$duplicates->implode(', ').'].');
    }

    public static function nonDeterministicTransitions(Collection $transitions): self
    {
        return self::new(
            'There are %s transitions coming out of [%s] with same input [%s]. Specifically: [%s], [%s].',
            $transitions->count(),
            $transitions->first()->getFrom(),
            $transitions->first()->getInput(),
            ...$transitions->map(function (Transition $transition) {
                return self::transitionToString($transition);
            })->toArray()
        );
    }

    public static function nonExistingState(Transition $transition, string $state): self
    {
        return self::new(
            'Transition [%s] is referring to a non-existing state [%s].',
            self::transitionToString($transition),
            $state
        );
    }

    public static function new(...$args)
    {
        return new self(sprintf(...$args));
    }

    public static function transitionToString(Transition $transition): string
    {
        return "{$transition->getFrom()} --({$transition->getInput()})--> {$transition->getTo()}";
    }
}
