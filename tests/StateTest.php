<?php

namespace SegfaultInc\Finite\Tests;

use SegfaultInc\Finite\State;
use PHPUnit\Framework\TestCase;

class StateTest extends TestCase
{
    /** @test */
    public function it_creates_initial_state()
    {
        $state = State::initial('foo', 'Foo');

        $this->assertEquals('foo', $state->getKey());
        $this->assertEquals('Foo', $state->getLabel());
        $this->assertEquals(State::INITIAL, $state->getType());
    }

    /** @test */
    public function it_creates_normal_state()
    {
        $state = State::normal('foo', 'Foo');

        $this->assertEquals('foo', $state->getKey());
        $this->assertEquals('Foo', $state->getLabel());
        $this->assertEquals(State::NORMAL, $state->getType());
    }

    /** @test */
    public function it_creates_final_state()
    {
        $state = State::final('foo', 'Foo');

        $this->assertEquals('foo', $state->getKey());
        $this->assertEquals('Foo', $state->getLabel());
        $this->assertEquals(State::FINAL, $state->getType());
    }

    /** @test */
    public function it_allows_to_set_extra_data()
    {
        $state = State::normal('foo')
            ->setExtra(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $state->getExtra());
    }
}
