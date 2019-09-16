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
    protected $hooks = [
        'applied'  => [],
        'applying' => [],
    ];

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

        $initial->executeEnteringHooks($subject);
        $subject->setFiniteState($initial->getKey());
        $initial->executeEnteredHooks($subject);
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

        $to->executeEnteringHooks($subject);
        $from->executeLeavingHooks($subject);
        $this->executeApplyingHooks($subject, $from, $to, $transition);
        $transition->executePreHooks($subject);

        $subject->setFiniteState($to->getKey());

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

    public static function make(array $states, array $transitions): self
    {
        return new self($states, $transitions);
    }
}
