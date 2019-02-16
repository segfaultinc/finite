<?php

namespace SegfaultInc\Finite\Tests;

use SegfaultInc\Finite\Graph;
use SegfaultInc\Finite\State;
use PHPUnit\Framework\TestCase;
use SegfaultInc\Finite\Exceptions\InvalidStateException;
use SegfaultInc\Finite\Exceptions\NoInitialStateException;

class StatesTest extends TestCase
{

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
}
