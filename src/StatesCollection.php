<?php

namespace SegfaultInc\Finite;

use SegfaultInc\Finite\Support\Collection;

class StatesCollection extends Collection
{
    public function find(string $key): State
    {
        return $this
            ->filter(function (State $state) use ($key) {
                return $state->key == $key;
            })
            ->first(function () use ($key) {
                throw Exceptions\InvalidStateException::new($key);
            });
    }

    public function initial(): State
    {
        return $this
            ->filter(function (State $state) {
                return $state->type == State::INITIAL;
            })
            ->first(function () {
                throw Exceptions\NoInitialStateException::new();
            });
    }
}
