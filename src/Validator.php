<?php

namespace SegfaultInc\Finite;

use SegfaultInc\Finite\Support\Collection;
use SegfaultInc\Finite\Exceptions\InvalidInputException;
use SegfaultInc\Finite\Exceptions\ConfigurationException;
use SegfaultInc\Finite\Exceptions\SubjectInInvalidStateException;

class Validator
{
    /**
     * Validate states by checking for 'no initial state',
     * 'multiple initial states' or 'duplicate state' cases.
     */
    public static function states(StatesCollection $states): StatesCollection
    {
        $initial = $states
            ->filter(function (State $state) {
                return $state->type == State::INITIAL;
            });

        if ($initial->count() == 0) {
            throw ConfigurationException::noInitialState();
        }

        if ($initial->count() > 1) {
            throw ConfigurationException::multipleInitialStates($initial);
        }

        $states
            ->map(function (State $state) {
                return $state->key;
            })
            ->duplicates()
            ->whenNotEmpty(function ($duplicates) {
                throw ConfigurationException::duplicateKeys($duplicates);
            });

        return $states;
    }

    /**
     * Validate transition by checking for 'non deterministic' transitions.
     */
    public static function transitions(Collection $transitions): Collection
    {
        $transitions
            ->map(function (Transition $transition) {
                return $transition->from()->key.' <+> '.$transition->input();
            })
            ->duplicates()
            ->map(function ($key) {
                return explode(' <+> ', $key);
            })
            ->map(function ($xs) use ($transitions) {
                [$from, $input] = $xs;

                $nonDeterministic = $transitions
                    ->filter(function (Transition $transition) use ($from, $input) {
                        return $transition->from()->key == $from
                            && $transition->input() == $input;
                    });

                throw ConfigurationException::nonDeterministicTransitions($nonDeterministic);
            });

        return $transitions;
    }

    /**
     * Validate given subject and input by checking for
     * 'subject in invalid state' and 'invalid input' cases.
     */
    public static function subject(StatesCollection $states, Collection $transitions, Subject $subject, string $input): void
    {
        $states
            ->filter(function (State $state) use ($subject) {
                return $state->key == $subject->getFiniteState();
            })
            ->whenEmpty(function () use ($subject) {
                throw SubjectInInvalidStateException::new($subject->getFiniteState());
            });

        $transitions
            ->filter(function ($transition) use ($subject, $input) {
                return $transition->from()->key == $subject->getFiniteState()
                    && $transition->input() == $input;
            })
            ->whenEmpty(function () use ($subject, $input) {
                throw InvalidInputException::new($subject->getFiniteState(), $input);
            });
    }
}
