<?php

namespace SegfaultInc\Finite\Tests;

use SegfaultInc\Finite\Graph;
use SegfaultInc\Finite\State;
use PHPUnit\Framework\TestCase;
use SegfaultInc\Finite\Exceptions;
use SegfaultInc\Finite\Transition;

class ApplyTest extends TestCase
{
    /** @test */
    public function it_applies_transition()
    {
        $subject = new SampleSubject('new');

        $finite = (new Graph)
            ->setStates([
                $new = State::initial('new'),
                $foo = State::normal('foo'),
            ])
            ->setTransitions([
                Transition::new($new, $foo, 'a'),
            ]);

        $finite->apply($subject, 'a');

        $this->assertEquals('foo', $subject->getFiniteState());
    }

    /** @test */
    public function object_must_be_in_valid_state_when_applying_transition()
    {
        $this->expectException(Exceptions\SubjectInInvalidStateException::class);

        (new Graph)->apply(new SampleSubject('invalid'), 'a');
    }

    /** @test */
    public function transition_with_given_input_must_exist_when_it_is_being_applied()
    {
        $this->expectException(Exceptions\InvalidInputException::class);

        $finite = (new Graph)
         ->setStates([
             $new = State::initial('new'),
             $foo = State::normal('foo'),
         ])
         ->setTransitions([
             Transition::new($new, $foo, 'a'),
         ]);

        $finite->apply(new SampleSubject('new'), 'b');
    }
}
