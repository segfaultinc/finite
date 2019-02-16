<?php

namespace SegfaultInc\Finite;

use SegfaultInc\Finite\Support\Collection;

class Graph
{
    /** @var StatesCollection */
    private $states;

    /** @var Collection */
    private $transitions = [];

    /** @var bool */
    private $hooks = true;

    public function __construct()
    {
        $this->states = StatesCollection::make([]);
        $this->transitions = Collection::make([]);
    }

    /**
     * Set states for the graph.
     */
    public function setStates(array $states): self
    {
        $this->states = Validator::states(
            StatesCollection::make($states)
        );

        return $this;
    }

    /**
     * Retrieve all states for the graph.
     */
    public function getStates(): StatesCollection
    {
        return $this->states;
    }

    /**
     * Set transition for the graph.
     */
    public function setTransitions(array $transitions): self
    {
        $this->transitions = Validator::transitions(
            Collection::make($transitions)
        );

        return $this;
    }

    /**
     * Retrieve all transition for the graph.
     */
    public function getTransitions(): Collection
    {
        return $this->transitions;
    }

    /**
     * Apply given input to the graph on given subject.
     */
    public function apply(Subject $subject, string $input): void
    {
        Validator::subject($this->states, $this->transitions, $subject, $input);

        $transition = $this->getTransitions()
            ->filter(function (Transition $transition) use ($subject, $input) {
                return $transition->from()->key == $subject->getFiniteState()
                    && $transition->input() == $input;
            })
            ->first();

        [$from, $to] = [$transition->from(), $transition->to()];

        if ($this->hooks) {
            $transition->executePreHooks($subject);
        }

        $subject->setFiniteState($to->key);

        if ($this->hooks) {
            $to->executeEnteringHooks($subject);
            $from->executeLeavingHooks($subject);
        }

        if ($this->hooks) {
            $transition->executePostHooks($subject);
        }
    }

    /**
     * Disable all hooks.
     */
    public function disableHooks(): self
    {
        $this->hooks = false;

        return $this;
    }

    /**
     * Enable all hooks.
     */
    public function enableHooks(): self
    {
        $this->hooks = true;

        return $this;
    }
}
