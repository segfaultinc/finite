<?php

namespace SegfaultInc\Finite\Support;

use Closure;
use Opis\Closure\SerializableClosure;

/**
 * @internal
 */
class Hooks
{
    /** @var array */
    protected $hooks = [];

    public function register(string $event, callable $hook): void
    {
        if (! isset($this->hooks[$event])) {
            $this->hooks[$event] = [];
        }

        $this->hooks[$event][] = SerializableClosure::from(Closure::fromCallable($hook));
    }

    public function execute(string $event, ...$args): void
    {
        foreach ($this->hooks[$event] ?? [] as $hook) {
            $hook(...$args);
        }
    }
}
