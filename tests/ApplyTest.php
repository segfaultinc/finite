<?php

namespace SegfaultInc\Finite\Tests;

use SegfaultInc\Finite\Graph;
use SegfaultInc\Finite\State;
use PHPUnit\Framework\TestCase;
use SegfaultInc\Finite\Exceptions;
use SegfaultInc\Finite\Transition;
use SegfaultInc\Finite\Tests\Stubs\Subject;

class ApplyTest extends TestCase
{
    /** @test */
    public function it_initializes_subject()
    {
        $subject = new Subject('[empty]');

        $finite = Graph::make([
            State::initial('new'),
        ], []);

        $finite->initialize($subject);

        $this->assertEquals('new', $subject->getFiniteState());
    }

    /** @test */
    public function it_applies_transition()
    {
        $subject = new Subject('new');

        $finite = Graph::make([
            State::initial('new'),
            State::normal('foo'),
        ], [
            Transition::make('new', 'foo', 'a'),
        ]);

        $finite->apply($subject, 'a');

        $this->assertEquals('foo', $subject->getFiniteState());
    }

    /** @test */
    public function object_must_be_in_valid_state_when_applying_transition()
    {
        $this->expectException(Exceptions\SubjectInInvalidStateException::class);

        Graph::make([
            State::initial('foo'),
        ], [])->apply(new Subject('invalid'), 'a');
    }

    /** @test */
    public function transition_with_given_input_must_exist_when_it_is_being_applied()
    {
        $this->expectException(Exceptions\InvalidInputException::class);

        $finite = Graph::make([
            State::initial('new'),
            State::normal('foo'),
        ], [
            Transition::make('new', 'foo', 'a'),
        ]);

        $finite->apply(new Subject('new'), 'b');
    }
}
