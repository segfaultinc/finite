<?php

namespace SegfaultInc\Finite;

class Variations
{
    public static function from(State $state): array
    {
        if (! $state->variations) {
            return [$state];
        }

        return array_map(function (string $variation) use ($state) {
            return $state->clone("{$state->key}:{$variation}");
        }, $state->variations);
    }
}
