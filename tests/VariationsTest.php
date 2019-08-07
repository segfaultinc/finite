<?php

namespace SegfaultInc\Finite\Tests;

use SegfaultInc\Finite\Graph;
use SegfaultInc\Finite\State;
use PHPUnit\Framework\TestCase;

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
}
