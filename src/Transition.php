<?php

namespace SegfaultInc\Finite;

use SegfaultInc\Finite\Support\Hooks;

class Transition
{
    /** @var string */
    protected $to;

    /** @var string */
    protected $from;

    /** @var string */
    protected $input;

    /** @var \SegfaultInc\Finite\Support\Hooks */
    protected $hooks;

    /**
     * Create new transition.
     */
    protected function __construct(string $from, string $to, string $input)
    {
        $this->from = $from;
        $this->to = $to;
        $this->input = $input;

        $this->hooks = new Hooks;
    }

    /**
     * Get the "from" state.
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * Get the "to" state.
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * Get the "input", on which the transition happens.
     */
    public function getInput(): string
    {
        return $this->input;
    }

    /**
     * Register hooks, which are executed prior to apply the transition.
     * If any of these throws, transition is not applied.
     */
    public function pre(callable $hook): self
    {
        $this->hooks->register('pre', $hook);

        return $this;
    }

    /**
     * Register hooks, which are executed after the transition is applied.
     */
    public function post(callable $hook): self
    {
        $this->hooks->register('post', $hook);

        return $this;
    }

    /**
     * Get hooks configuration.
     */
    public function getHooks(): Hooks
    {
        return $this->hooks;
    }

    /**
     * Create new transition.
     */
    public static function make(string $from, string $to, string $input): self
    {
        return new self($from, $to, $input);
    }
}
