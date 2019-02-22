<?php

namespace SegfaultInc\Finite\Tests;

use SegfaultInc\Finite\Graph;
use SegfaultInc\Finite\State;
use PHPUnit\Framework\TestCase;
use SegfaultInc\Finite\Transition;

class GraphTest extends TestCase
{
    /** @test */
    public function can_register_states()
    {
        $machine = (new Graph)
            ->setStates([
                $init = State::initial('init'),
                $work = State::normal('work'),
                $done = State::final('done'),
            ]);

        $this->assertCount(3, $machine->getStates());

        $this->assertSame(
            [$init, $work, $done],
            $machine->getStates()->toArray()
        );
    }

    /** @test */
    public function can_register_transitions()
    {
        $machine = (new Graph)
            ->setStates([
                State::initial('a'),
                State::normal('b'),
                State::final('c'),
            ])
            ->setTransitions([
                $one = Transition::new('a', 'b', '0'),
                $two = Transition::new('b', 'c', '1'),
            ]);

        $this->assertCount(2, $machine->getTransitions());

        $this->assertSame(
            [$one, $two],
            $machine->getTransitions()->toArray()
        );
    }
}
