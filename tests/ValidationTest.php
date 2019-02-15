<?php

namespace SegfaultInc\Finite\Tests;

use SegfaultInc\Finite\State;
use SegfaultInc\Finite\Graph;
use PHPUnit\Framework\TestCase;
use SegfaultInc\Finite\Transition;
use SegfaultInc\Finite\Exceptions\ConfigurationException;

class ValidationTest extends TestCase
{
    /** @test */
    public function there_must_be_initial_state()
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('There must exist a single initial state. None present.');

        (new Graph)
            ->setStates([
                State::normal('work'),
            ]);
    }

    /** @test */
    public function there_cannot_be_multiple_initial_states()
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('There must exist a single initial state. Multiple present [a, b].');

        (new Graph)
            ->setStates([
                State::initial('a'),
                State::initial('b'),
            ]);
    }

    /** @test */
    public function there_cannot_be_multiple_states_with_same_key()
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Every state much have a unique key. Contains duplicate keys [a, b].');

        (new Graph)
            ->setStates([
                State::initial('a'),
                State::normal('a'),
                State::normal('b'),
                State::normal('b'),
                State::normal('c'),
            ]);
    }

    /** @test */
    public function there_cannot_be_two_transitions_with_same_input_from_single_state()
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('There are 2 transitions coming out of [new] with same input [a]. Specifically: [new --(a)--> foo], [new --(a)--> bar].');

        (new Graph)
            ->setStates([
                $new = State::initial('new'),
                $foo = State::normal('foo'),
                $bar = State::normal('bar'),
            ])
            ->setTransitions([
                Transition::new($new, $foo, 'a'),
                Transition::new($new, $bar, 'a'),
            ]);
    }
}
