<?php

namespace SegfaultInc\Finite\Tests;

use SegfaultInc\Finite\Graph;
use SegfaultInc\Finite\State;
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

        Graph::make([
            State::normal('a'),
        ], []);
    }

    /** @test */
    public function there_cannot_be_multiple_initial_states()
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('There must exist a single initial state. Multiple present [a, b].');

        Graph::make([
            State::initial('a'),
            State::initial('b'),
        ], []);
    }

    /** @test */
    public function there_cannot_be_multiple_states_with_same_key()
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Every state much have a unique key. Contains duplicate keys [a, b].');

        Graph::make([
            State::initial('a'),
            State::normal('a'),
            State::normal('b'),
            State::normal('b'),
            State::normal('c'),
        ], []);
    }

    /** @test */
    public function transitions_must_refer_to_existing_from_state()
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Transition [non-existing --(a)--> foo] is referring to a non-existing state [non-existing].');

        Graph::make([
            State::initial('bar'),
        ], [
            Transition::make('non-existing', 'foo', 'a'),
        ]);
    }

    /** @test */
    public function transitions_must_refer_to_existing_to_state()
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Transition [foo --(a)--> non-existing] is referring to a non-existing state [non-existing].');

        Graph::make([
            State::initial('foo'),
        ], [
            Transition::make('foo', 'non-existing', 'a'),
        ]);
    }

    /** @test */
    public function there_cannot_be_two_transitions_with_same_input_from_single_state()
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('There are 2 transitions coming out of [new] with same input [a]. Specifically: [new --(a)--> foo], [new --(a)--> bar].');

        Graph::make([
            State::initial('new'),
            State::normal('foo'),
            State::normal('bar'),
        ], [
            Transition::make('new', 'foo', 'a'),
            Transition::make('new', 'bar', 'a'),
        ]);
    }
}
