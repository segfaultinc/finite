<?php

namespace SegfaultInc\Finite\Tests;

use SegfaultInc\Finite\State;
use SegfaultInc\Finite\Graph;
use PHPUnit\Framework\TestCase;
use SegfaultInc\Finite\Transition;
use SegfaultInc\Finite\Exceptions\InvalidStateException;
use SegfaultInc\Finite\Exceptions\NoInitialStateException;

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
        $this->assertSame($init, $machine->getStates()->toArray()[0]);
        $this->assertSame($work, $machine->getStates()->toArray()[1]);
        $this->assertSame($done, $machine->getStates()->toArray()[2]);
    }

    /** @test */
    public function it_finds_state_by_key()
    {
        $finite = (new Graph)
            ->setStates([
                $init = State::initial('init'),
            ]);

        $this->assertSame($init, $finite->getStates()->find('init'));
    }

    /** @test */
    public function it_throws_when_trying_to_find_non_existing_state()
    {
        $this->expectException(InvalidStateException::class);

        $finite = (new Graph);

        $finite->getStates()->find('NON-EXISTING');
    }

    /** @test */
    public function it_finds_initial_state()
    {
        $finite = (new Graph)
            ->setStates([
                $init = State::initial('init'),
            ]);

        $this->assertSame($init, $finite->getStates()->initial());
    }

    /** @test */
    public function it_throws_when_trying_to_find_non_existing_initial_state()
    {
        $this->expectException(NoInitialStateException::class);

        $finite = (new Graph);

        $finite->getStates()->initial();
    }

    /** @test */
    public function can_register_transitions()
    {
        $machine = (new Graph)
            ->setStates([
                $a = State::initial('a'),
                $b = State::normal('b'),
                $c = State::final('c'),
            ])
            ->setTransitions([
                $one = Transition::new($a, $b, 0),
                $two = Transition::new($b, $c, 1),
            ]);

        $this->assertCount(2, $machine->getTransitions());
        $this->assertSame($one, $machine->getTransitions()->toArray()[0]);
        $this->assertSame($two, $machine->getTransitions()->toArray()[1]);
    }
}
