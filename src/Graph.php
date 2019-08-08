<?php

namespace SegfaultInc\Finite;

use SegfaultInc\Finite\Support\Validator;
use SegfaultInc\Finite\Support\Collection;

class Graph
{
    /** @var array */
    protected $states = [];

    /** @var array */
    protected $transitions = [];

    /** @var array */
    private $hooks = [
        'applied'  => [],
        'applying' => [],
    ];

    /**
     * Set states for the graph.
     */
    public function setStates(array $states): self
    {
        $this->states = Collection::make(Validator::states($states))
            ->map(function (State $state) use ($states) {
                return Variations::state($state, $states);
            })
            ->flatten()
            ->toArray();

        return $this;
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
                return $state->key == $key;
            })
            ->firstOr(function () use ($key) {
                throw Exceptions\InvalidStateException::new($key);
            });
    }

    /**
     * Set transition for the graph.
     */
    public function setTransitions(array $transitions): self
    {
        $transitions = Collection::make($transitions)
            ->map(function (Transition $transition) {
                if ($inverse = $transition->invert()) {
                    return [$transition, $inverse];
                }

                return [$transition];
            })
            ->flatten()
            ->map(function (Transition $transition) {
                return Variations::transition($transition, $this->states);
            })
            ->toArray();

        $this->transitions = Validator::transitions($transitions, $this->states);

        return $this;
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
                return $state->type == State::INITIAL;
            })
            ->firstOr(function () {
                throw Exceptions\NoInitialStateException::new();
            });

        $initial->executeEnteringHooks($subject);
        $subject->setFiniteState($initial->key);
        $initial->executeEnteredHooks($subject);
    }

    /**
     * Apply given input to the graph on given subject.
     */
    public function apply(Subject $subject, string $input): void
    {
        Validator::subject($this->states, $this->transitions, $subject, $input);

        $transition = Collection::make($this->transitions)
            ->filter(function ($transition) use ($subject, $input) {
                return $transition->from() == $subject->getFiniteState()
                    && $transition->input() == $input;
            })
            ->first();

        $from = $this->getState($transition->from());
        $to = $this->getState($transition->to());

        $to->executeEnteringHooks($subject);
        $from->executeLeavingHooks($subject);
        $this->executeApplyingHooks($subject, $from, $to, $transition);
        $transition->executePreHooks($subject);

        $subject->setFiniteState($to->key);

        $transition->executePostHooks($subject);
        $this->executeAppliedHooks($subject, $from, $to, $transition);
        $from->executeLeftHooks($subject);
        $to->executeEnteredHooks($subject);
    }

    /**
     * Register hooks for "applied" event.
     */
    public function applied(callable $hook): self
    {
        $this->hooks['applied'][] = $hook;

        return $this;
    }

    /**
     * Register hooks for "applying" event.
     */
    public function applying(callable $hook): self
    {
        $this->hooks['applying'][] = $hook;

        return $this;
    }

    /**
     * Execute hooks for "applying" event.
     */
    public function executeApplyingHooks(Subject $subject, State $from, State $to, Transition $transition): self
    {
        foreach ($this->hooks['applying'] as $hook) {
            $hook($subject, $from, $to, $transition);
        }

        return $this;
    }

    /**
     * Execute hooks for "applied" event.
     */
    public function executeAppliedHooks(Subject $subject, State $from, State $to, Transition $transition): self
    {
        foreach ($this->hooks['applied'] as $hook) {
            $hook($subject, $from, $to, $transition);
        }

        return $this;
    }
}
