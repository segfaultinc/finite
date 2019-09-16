<?php

namespace SegfaultInc\Finite\Tests;

use SegfaultInc\Finite\Graph;
use SegfaultInc\Finite\State;
use PHPUnit\Framework\TestCase;
use SegfaultInc\Finite\Transition;
use SegfaultInc\Finite\Exceptions\InvalidStateException;

class GraphTest extends TestCase
{
    /** @test */
    public function can_register_states()
    {
        $machine = Graph::make([
            $init = State::initial('init'),
            $work = State::normal('work'),
            $done = State::final('done'),
        ], []);

        $this->assertCount(3, $machine->getStates());
        $this->assertSame($init, $machine->getStates()[0]);
        $this->assertSame($work, $machine->getStates()[1]);
        $this->assertSame($done, $machine->getStates()[2]);
    }

    /** @test */
    public function it_finds_state_by_key()
    {
        $finite = Graph::make([
            $init = State::initial('init'),
        ], []);

        $this->assertSame($init, $finite->getState('init'));
    }

    /** @test */
    public function it_throws_when_trying_to_find_non_existing_state()
    {
        $this->expectException(InvalidStateException::class);

        $finite = Graph::make([
            State::initial('foo'),
        ], []);

        $finite->getState('NON-EXISTING');
    }

    /** @test */
    public function can_register_transitions()
    {
        $machine = Graph::make([
            State::initial('a'),
            State::normal('b'),
            State::final('c'),
        ], [
            $one = Transition::make('a', 'b', 'x'),
            $two = Transition::make('b', 'c', 'x'),
        ]);

        $this->assertCount(2, $machine->getTransitions());
        $this->assertSame($one, $machine->getTransitions()[0]);
        $this->assertSame($two, $machine->getTransitions()[1]);
    }

    /**
     * @test
     *
     * This "feature" exists so "state variations" can be easily implemented in user land.
     *
     * eg.:
     *   [
     *     State::new('state:foo'),
     *     State::new('state:bar'),
     *   ]
     *
     * This can be represented as:
     *   [
     *     array_map(function (string $key) {
     *       return State::new("state:{$key}");
     *     }, ['foo', 'bar'])
     *   ]
     *
     * This technique helps when there are a lot of state variations and "current:previous" syntax is used.
     *
     * However, this feature will be removed once https://wiki.php.net/rfc/spread_operator_for_array can be used.
     */
    public function can_register_states_and_transitions_using_nested_arrays()
    {
        $machine = Graph::make([
            $init = State::initial('init'),

            [
                $foo = State::normal('foo'),
                $bar = State::normal('bar'),
            ],

            $done = State::final('done'),
        ], [
            $x = Transition::make('init', 'foo', 'x'),

            [
                $y = Transition::make('foo', 'bar', 'y'),
            ],

            $z = Transition::make('bar', 'done', 'z'),
        ]);

        $this->assertCount(4, $machine->getStates());
        $this->assertSame($init, $machine->getStates()[0]);
        $this->assertSame($foo, $machine->getStates()[1]);
        $this->assertSame($bar, $machine->getStates()[2]);
        $this->assertSame($done, $machine->getStates()[3]);

        $this->assertCount(3, $machine->getTransitions());
        $this->assertSame($x, $machine->getTransitions()[0]);
        $this->assertSame($y, $machine->getTransitions()[1]);
        $this->assertSame($z, $machine->getTransitions()[2]);
    }
}
