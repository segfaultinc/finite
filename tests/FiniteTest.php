<?php

namespace SegfaultInc\Finite\Tests;

use SegfaultInc\Finite\Graph;
use SegfaultInc\Finite\State;
use PHPUnit\Framework\TestCase;
use SegfaultInc\Finite\Transition;
use SegfaultInc\Finite\Exceptions\InvalidStateException;

class FiniteTest extends TestCase
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
        $this->assertSame($init, $machine->getStates()[0]);
        $this->assertSame($work, $machine->getStates()[1]);
        $this->assertSame($done, $machine->getStates()[2]);
    }

    /** @test */
    public function it_finds_state_by_key()
    {
        $finite = (new Graph)
            ->setStates([
                $init = State::initial('init'),
            ]);

        $this->assertSame($init, $finite->getState('init'));
    }

    /** @test */
    public function it_throws_when_trying_to_find_non_existing_state()
    {
        $this->expectException(InvalidStateException::class);

        $finite = (new Graph);

        $finite->getState('NON-EXISTING');
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
                $one = Transition::new('a', 'b', 0),
                $two = Transition::new('b', 'c', 1),
            ]);

        $this->assertCount(2, $machine->getTransitions());
        $this->assertSame($one, $machine->getTransitions()[0]);
        $this->assertSame($two, $machine->getTransitions()[1]);
    }
}
