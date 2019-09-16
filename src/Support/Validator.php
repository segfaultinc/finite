<?php

namespace SegfaultInc\Finite\Support;

use SegfaultInc\Finite\State;
use SegfaultInc\Finite\Subject;
use SegfaultInc\Finite\Transition;
use SegfaultInc\Finite\Exceptions\InvalidInputException;
use SegfaultInc\Finite\Exceptions\ConfigurationException;
use SegfaultInc\Finite\Exceptions\SubjectInInvalidStateException;

/**
 * @internal
 */
class Validator
{
    public static function states(array $states): array
    {
        $initial = Collection::make($states)
            ->filter(function (State $state) {
                return $state->getType() == State::INITIAL;
            });

        if ($initial->count() == 0) {
            throw ConfigurationException::noInitialState();
        }

        if ($initial->count() > 1) {
            throw ConfigurationException::multipleInitialStates($initial);
        }

        $duplicates = Collection::make($states)
            ->duplicates(function (State $state) {
                return $state->getKey();
            });

        if ($duplicates->count() > 0) {
            throw ConfigurationException::duplicateKeys($duplicates);
        }

        return $states;
    }

    public static function transitions(array $transitions, array $states): array
    {
        foreach ($transitions as $transition) {
            [$from, $to] = [$transition->getFrom(), $transition->getTo()];

            Collection::make($states)
                ->filter(function (State $state) use ($from) {
                    return $state->getKey() == $from;
                })
                ->ifEmpty(function () use ($transition, $from) {
                    throw ConfigurationException::nonExistingState($transition, $from);
                });

            Collection::make($states)
                ->filter(function (State $state) use ($to) {
                    return $state->getKey() == $to;
                })
                ->ifEmpty(function () use ($transition, $to) {
                    throw ConfigurationException::nonExistingState($transition, $to);
                });
        }

        Collection::make($transitions)
            ->groupBy(function (Transition $transition) {
                return $transition->getFrom().' <+> '.$transition->getInput();
            })
            ->filter(function ($transitions) {
                return $transitions->count() > 1;
            })
            ->each(function ($transitions) {
                throw ConfigurationException::nonDeterministicTransitions($transitions);
            });

        return $transitions;
    }

    public static function subject(array $states, array $transitions, Subject $subject, string $input): void
    {
        Collection::make($states)
            ->filter(function (State $state) use ($subject) {
                return $state->getKey() == $subject->getFiniteState();
            })
            ->ifEmpty(function () use ($subject) {
                throw SubjectInInvalidStateException::new($subject->getFiniteState());
            });

        Collection::make($transitions)
            ->filter(function (Transition $transition) use ($subject, $input) {
                return $transition->getFrom() == $subject->getFiniteState()
                    && $transition->getInput() == $input;
            })
            ->ifEmpty(function () use ($subject, $input) {
                throw InvalidInputException::new($subject->getFiniteState(), $input);
            });
    }
}
