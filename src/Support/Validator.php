<?php

namespace SegfaultInc\Finite\Support;

use SegfaultInc\Finite\State;
use SegfaultInc\Finite\Transition;
use SegfaultInc\Finite\Tests\Stubs\Subject;
use SegfaultInc\Finite\Exceptions\InvalidInputException;
use SegfaultInc\Finite\Exceptions\ConfigurationException;
use SegfaultInc\Finite\Exceptions\SubjectInInvalidStateException;

class Validator
{
    public static function states(array $states): array
    {
        $initial = Collection::make($states)
            ->filter(function (State $state) {
                return $state->type == State::INITIAL;
            });

        if ($initial->count() == 0) {
            throw ConfigurationException::noInitialState();
        }

        if ($initial->count() > 1) {
            throw ConfigurationException::multipleInitialStates($initial);
        }

        $duplicates = Collection::make($states)
            ->duplicates(function (State $state) {
                return $state->key;
            });

        if ($duplicates->count() > 0) {
            throw ConfigurationException::duplicateKeys($duplicates);
        }

        return $states;
    }

    public static function transitions(array $transitions, array $states): array
    {
        foreach ($transitions as $transition) {
            [$from, $to] = [$transition->from(), $transition->to()];

            Collection::make($states)
                ->filter(function (State $state) use ($from) {
                    return $state->key == $from;
                })
                ->ifEmpty(function () use ($transition, $from) {
                    throw ConfigurationException::nonExistingState($transition, $from);
                });

            Collection::make($states)
                ->filter(function (State $state) use ($to) {
                    return $state->key == $to;
                })
                ->ifEmpty(function () use ($transition, $to) {
                    throw ConfigurationException::nonExistingState($transition, $to);
                });
        }

        Collection::make($transitions)
            ->groupBy(function ($transition) {
                return $transition->from().' <+> '.$transition->input();
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
                return $state->key == $subject->getFiniteState();
            })
            ->ifEmpty(function () use ($subject) {
                throw SubjectInInvalidStateException::new($subject->getFiniteState());
            });

        Collection::make($transitions)
            ->filter(function (Transition $transition) use ($subject, $input) {
                return $transition->from() == $subject->getFiniteState()
                    && $transition->input() == $input;
            })
            ->ifEmpty(function () use ($subject, $input) {
                throw InvalidInputException::new($subject->getFiniteState(), $input);
            });
    }
}
