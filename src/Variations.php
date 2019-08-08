<?php

namespace SegfaultInc\Finite;

use SegfaultInc\Finite\Support\Collection;
use SegfaultInc\Finite\Exceptions\InvalidStateException;

class Variations
{
    public static function state(State $state, array $states): array
    {
        if (! $state->variations) {
            return [$state];
        }

        foreach ($state->variations  as $variation) {
            Collection::make($states)
                ->filter(function (State $state) use ($variation) {
                    return $state->key == $variation;
                })
                ->ifEmpty(function () use ($variation) {
                    throw InvalidStateException::new($variation);
                });
        }

        return array_map(function (string $variation) use ($state) {
            return $state->clone("{$state->key}:{$variation}");
        }, $state->variations);
    }

    /**
     * Replace "variationKey" in transition by a fully specified key.
     */
    public static function transition(Transition $transition, array $states): Transition
    {
        $fromVariation = Collection::make($states)
            ->filter(function (State $state) use ($transition) {
                return $state->variationKey == $transition->from();
            })
            ->first();

        if ($fromVariation) {
            return $transition->clone("{$transition->from()}:{$transition->to()}", null);
        }

        $toVariation = Collection::make($states)
            ->filter(function (State $state) use ($transition) {
                return $state->variationKey == $transition->to();
            })
            ->first();

        if ($toVariation) {
            return $transition->clone(null, "{$transition->to()}:{$transition->from()}");
        }

        return $transition;
    }
}
