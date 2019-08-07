<?php

namespace SegfaultInc\Finite\Tests;

use PHPUnit\Framework\TestCase;
use SegfaultInc\Finite\Transition;

class TransitionTest extends TestCase
{
    /** @test */
    public function it_clones_transition()
    {
        $transition = Transition::new('foo', 'bar', 'progress');

        $clone = $transition->clone();

        $this->assertNotSame($transition, $clone);
        $this->assertEquals('foo', $clone->from());
        $this->assertEquals('bar', $clone->to());
        $this->assertEquals('progress', $clone->input());
    }

    /** @test */
    public function it_clones_transition_and_replaces_from_key()
    {
        $transition = Transition::new('foo', 'bar', 'progress');

        $clone = $transition->clone('replaced_from');

        $this->assertNotSame($transition, $clone);
        $this->assertEquals('replaced_from', $clone->from());
        $this->assertEquals('bar', $clone->to());
        $this->assertEquals('progress', $clone->input());
    }

    /** @test */
    public function it_clones_transition_and_replaces_to_key()
    {
        $transition = Transition::new('foo', 'bar', 'progress');

        $clone = $transition->clone(null, 'replaced_to');

        $this->assertNotSame($transition, $clone);
        $this->assertEquals('foo', $clone->from());
        $this->assertEquals('replaced_to', $clone->to());
        $this->assertEquals('progress', $clone->input());
    }

    /** @test */
    public function it_clones_transition_and_replaces_input()
    {
        $transition = Transition::new('foo', 'bar', 'progress');

        $clone = $transition->clone(null, null, 'regress');

        $this->assertNotSame($transition, $clone);
        $this->assertEquals('foo', $clone->from());
        $this->assertEquals('bar', $clone->to());
        $this->assertEquals('regress', $clone->input());
    }
}
