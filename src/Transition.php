<?php

namespace SegfaultInc\Finite;

class Transition
{
    /** @var string */
    protected $to;

    /** @var string */
    protected $from;

    /** @var string */
    protected $input;

    /** @var array */
    protected $hooks = [
        'pre'  => [],
        'post' => [],
    ];

    /**
     * Create new transition.
     */
    private function __construct(string $from, string $to, string $input)
    {
        $this->from = $from;
        $this->to = $to;
        $this->input = $input;
    }

    /**
     * Get the "from" state.
     */
    public function from(): string
    {
        return $this->from;
    }

    /**
     * Get the "to" state.
     */
    public function to(): string
    {
        return $this->to;
    }

    /**
     * Get the "input", on which the transition happens.
     */
    public function input(): string
    {
        return $this->input;
    }

    /**
     * Register hooks, which are executed prior to apply the transition.
     * If any of these throws, transition is not applied.
     */
    public function pre(callable $hook): self
    {
        $this->hooks['pre'][] = $hook;

        return $this;
    }

    /**
     * Register hooks, which are executed after the transition is applied.
     */
    public function post(callable $hook): self
    {
        $this->hooks['post'][] = $hook;

        return $this;
    }

    /**
     * Execute pre-hooks.
     */
    public function executePreHooks(Subject $subject): void
    {
        foreach ($this->hooks['pre'] as $hook) {
            $hook($subject);
        }
    }

    /**
     * Execute post-hooks.
     */
    public function executePostHooks(Subject $subject): void
    {
        foreach ($this->hooks['post'] as $hook) {
            $hook($subject);
        }
    }

    /**
     * Return a string representation of the transition.
     */
    public function __toString(): string
    {
        return "{$this->from} --({$this->input})--> {$this->to}";
    }

    /**
     * Clone the transition, potentially replace from/to states.
     */
    public function clone(?string $from = null, ?string $to = null, ?string $input = null): self
    {
        $clone = clone $this;

        $clone->from  = $from ?: $clone->from;
        $clone->to    = $to ?: $clone->to;
        $clone->input = $input ?: $clone->input;

        return $clone;
    }

    /**
     * Create new transition.
     */
    public static function new(string $from, string $to, string $input): self
    {
        return new self($from, $to, $input);
    }
}
