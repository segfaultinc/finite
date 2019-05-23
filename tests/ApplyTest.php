<?php

namespace SegfaultInc\Finite\Tests;

use SegfaultInc\Finite\Graph;
use SegfaultInc\Finite\State;
use PHPUnit\Framework\TestCase;
use SegfaultInc\Finite\Exceptions;
use SegfaultInc\Finite\Transition;
use SegfaultInc\Finite\Tests\Stubs\Subject;
use SegfaultInc\Finite\Exceptions\NoInitialStateException;

class ApplyTest extends TestCase
{
    /** @test */
    public function it_initializes_subject()
    {
        $subject = new Subject('[empty]');

        $finite = (new Graph)
            ->setStates([
                State::initial('new'),
            ]);

        $finite->initialize($subject);

        $this->assertEquals('new', $subject->getFiniteState());
    }

    /** @test */
    public function it_throws_when_trying_to_initialize_without_initial_state_set()
    {
        $this->expectException(NoInitialStateException::class);

        $finite = (new Graph);

        $finite->initialize(new Subject('[empty]'));
    }

    /** @test */
    public function it_applies_transition()
    {
        $subject = new Subject('new');

        $finite = (new Graph)
            ->setStates([
                State::initial('new'),
                State::normal('foo'),
            ])
            ->setTransitions([
                Transition::new('new', 'foo', 'a'),
            ]);

        $finite->apply($subject, 'a');

        $this->assertEquals('foo', $subject->getFiniteState());
    }

    /** @test */
    public function object_must_be_in_valid_state_when_applying_transition()
    {
        $this->expectException(Exceptions\SubjectInInvalidStateException::class);

        (new Graph)->apply(new Subject('invalid'), 'a');
    }

    /** @test */
    public function transition_with_given_input_must_exist_when_it_is_being_applied()
    {
        $this->expectException(Exceptions\InvalidInputException::class);

        $finite = (new Graph)
            ->setStates([
                State::initial('new'),
                State::normal('foo'),
            ])
            ->setTransitions([
                Transition::new('new', 'foo', 'a'),
            ]);

        $finite->apply(new Subject('new'), 'b');
    }
}
