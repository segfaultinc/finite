<?php

namespace SegfaultInc\Finite\Tests;

use SegfaultInc\Finite\Graph;
use SegfaultInc\Finite\State;
use PHPUnit\Framework\TestCase;
use SegfaultInc\Finite\Transition;

class VariationsTest extends TestCase
{
    /** @test */
    public function it_registers_state_variations()
    {
        $graph = (new Graph)
            ->setStates([
                State::initial('init'),

                State::normal('progress')
                    ->variations(['foo', 'bar', 'baz']),
            ]);

        $this->assertCount(4, $graph->getStates());
        $this->assertEquals(['init', 'progress:foo', 'progress:bar', 'progress:baz'], array_map(function (State $state) {
            return $state->key;
        }, $graph->getStates()));
    }

    /** @test */
    public function it_allows_to_refer_to_states_in_transitions_by_variation_key()
    {
        $graph = (new Graph)
            ->setStates([
                State::initial('init'),

                State::normal('foo'),
                State::normal('bar'),
                State::normal('baz'),

                State::normal('canceled')
                    ->variations(['foo', 'bar', 'baz']),
            ])
            ->setTransitions([
                Transition::new('foo', 'canceled', 'cancel'),
                Transition::new('bar', 'canceled', 'cancel'),
                Transition::new('baz', 'canceled', 'cancel'),

                Transition::new('canceled', 'foo', 'open'),
                Transition::new('canceled', 'bar', 'open'),
                Transition::new('canceled', 'baz', 'open'),
            ]);

        $this->assertEquals([
            Transition::new('foo', 'canceled:foo', 'cancel'),
            Transition::new('bar', 'canceled:bar', 'cancel'),
            Transition::new('baz', 'canceled:baz', 'cancel'),
            Transition::new('canceled:foo', 'foo', 'open'),
            Transition::new('canceled:bar', 'bar', 'open'),
            Transition::new('canceled:baz', 'baz', 'open'),
        ], $graph->getTransitions());
    }
}
