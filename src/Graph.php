<?php

namespace SegfaultInc\Finite;

use SegfaultInc\Finite\Support\Hooks;
use SegfaultInc\Finite\Support\Validator;
use SegfaultInc\Finite\Support\Collection;

class Graph
{
    /** @var array */
    protected $states = [];

    /** @var array */
    protected $transitions = [];

    /** @var \SegfaultInc\Finite\Support\Hooks */
    protected $hooks;

    /**
     * Create new instance.
     */
    protected function __construct(array $states, array $transitions)
    {
        $this->states = Validator::states(
            Collection::make($states)->flatten()->toArray()
        );

        $this->transitions = Validator::transitions(
            Collection::make($transitions)->flatten()->toArray(),
            $this->states
        );

        $this->hooks = new Hooks;
    }

    /**
     * Retrieve all states for the graph.
     */
    public function getStates(): array
    {
        return $this->states;
    }

    /**
     * Retrieve state by given key.
     */
    public function getState(string $key): State
    {
        return Collection::make($this->states)
            ->filter(function (State $state) use ($key) {
                return $state->getKey() == $key;
            })
            ->firstOr(function () use ($key) {
                throw Exceptions\InvalidStateException::new($key);
            });
    }

    /**
     * Retrieve all transition for the graph.
     */
    public function getTransitions(): array
    {
        return $this->transitions;
    }

    /**
     * Initialize given subject.
     */
    public function initialize(Subject $subject): void
    {
        $initial = Collection::make($this->states)
            ->filter(function (State $state) {
                return $state->getType() == State::INITIAL;
            })
            ->first();

        $initial->getHooks()->execute('entering', $subject);
        $subject->setFiniteState($initial->getKey());
        $initial->getHooks()->execute('entered', $subject);
    }

    /**
     * Apply given input to the graph on given subject.
     */
    public function apply(Subject $subject, string $input): void
    {
        Validator::subject($this->states, $this->transitions, $subject, $input);

        $transition = Collection::make($this->transitions)
            ->filter(function (Transition $transition) use ($subject, $input) {
                return $transition->getFrom() == $subject->getFiniteState()
                    && $transition->getInput() == $input;
            })
            ->first();

        $from = $this->getState($transition->getFrom());
        $to = $this->getState($transition->getTo());

        $to->getHooks()->execute('entering', $subject);
        $from->getHooks()->execute('leaving', $subject);
        $this->getHooks()->execute('applying', $subject, $from, $to, $transition);
        $transition->getHooks()->execute('pre', $subject);

        $subject->setFiniteState($to->getKey());

        $transition->getHooks()->execute('post', $subject);
        $this->getHooks()->execute('applied', $subject, $from, $to, $transition);
        $from->getHooks()->execute('left', $subject);
        $to->getHooks()->execute('entered', $subject);
    }

    /**
     * Register hooks for "applying" event.
     */
    public function applying(callable $hook): self
    {
        $this->hooks->register('applying', $hook);

        return $this;
    }

    /**
     * Register hooks for "applied" event.
     */
    public function applied(callable $hook): self
    {
        $this->hooks->register('applied', $hook);

        return $this;
    }

    /**
     * Get hooks configuration.
     */
    public function getHooks(): Hooks
    {
        return $this->hooks;
    }

    public static function make(array $states, array $transitions): self
    {
        return new self($states, $transitions);
    }
}
