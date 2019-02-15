<?php

namespace SegfaultInc\Finite;

use SegfaultInc\Finite\Support\Collection;

class Finite
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

    public function setStates(array $states): self
    {
        $this->states = Validator::states(
            StatesCollection::make($states)
        );

        return $this;
    }

    public function getStates(): StatesCollection
    {
        return $this->states;
    }

    public function setTransitions(array $transitions): self
    {
        $this->transitions = Validator::transitions(
            Collection::make($transitions)
        );

        return $this;
    }

    public function getTransitions(): Collection
    {
        return $this->transitions;
    }

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

        $to->executeEnteringHooks($subject);
        $from->executeLeavingHooks($subject);

        if ($this->hooks) {
            $transition->executePostHooks($subject);
        }
    }

    public function disableHooks(): self
    {
        $this->hooks = false;

        return $this;
    }

    public function enableHooks(): self
    {
        $this->hooks = true;

        return $this;
    }
}
