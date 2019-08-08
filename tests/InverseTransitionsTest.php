<?php

namespace SegfaultInc\Finite\Tests;

use SegfaultInc\Finite\Graph;
use SegfaultInc\Finite\State;
use PHPUnit\Framework\TestCase;
use SegfaultInc\Finite\Transition;

class InverseTransitionsTest extends TestCase
{
    /** @test */
    public function it_allows_to_register_transition_and_its_inverse_in_one_go()
    {
        $graph = (new Graph)
            ->setStates([
                State::initial('init'),

                State::normal('foo'),
            ])
            ->setTransitions([
                Transition::new('init', 'foo', 'progress')->inverse('regress'),
            ]);

        $this->assertEquals([
            Transition::new('init', 'foo', 'progress')->inverse('regress'),
            Transition::new('foo', 'init', 'regress'),
        ], $graph->getTransitions());
    }
}
