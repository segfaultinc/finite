<?php

namespace SegfaultInc\Finite\Tests;

use SegfaultInc\Finite\State;
use PHPUnit\Framework\TestCase;
use SegfaultInc\Finite\Tests\Stubs\Subject;

class StateTest extends TestCase
{
    /** @test */
    public function it_clones_state()
    {
        $ran = false;

        $foo = State::final('foo')
            ->label('Foo')
            ->entering(function () use (&$ran) {
                $ran = true;
            });

        $bar = $foo->clone('bar');

        $bar->executeEnteringHooks(new Subject('foo'));

        $this->assertEquals('bar', $bar->key);
        $this->assertEquals('Foo', $bar->label);
        $this->assertTrue($ran);
    }
}
