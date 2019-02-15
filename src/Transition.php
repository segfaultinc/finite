<?php

namespace SegfaultInc\Finite;

class Transition
{
    /** @var State */
    protected $to;

    /** @var State */
    protected $from;

    /** @var State */
    protected $input;

    /** @var array */
    protected $hooks = [
        'pre'  => [],
        'post' => [],
    ];

    /**
     * Create new transition.
     */
    private function __construct(State $from, State $to, string $input)
    {
        $this->from = $from;
        $this->to = $to;
        $this->input = $input;
    }

    /**
     * Get the "from" state.
     */
    public function from(): State
    {
        return $this->from;
    }

    /**
     * Get the "to" state.
     */
    public function to(): State
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

    public function toString(): string
    {
        return "{$this->from()->key} --({$this->input()})--> {$this->to()->key}";
    }

    public static function new(State $from, State $to, string $input): self
    {
        return new self($from, $to, $input);
    }
}
