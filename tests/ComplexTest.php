<?php

namespace SegfaultInc\Finite\Tests;

use SegfaultInc\Finite\Graph;
use SegfaultInc\Finite\State;
use PHPUnit\Framework\TestCase;
use SegfaultInc\Finite\Transition;
use SegfaultInc\Finite\Tests\Stubs\Subject;

class ComplexTest extends TestCase
{
    /** @var Graph */
    private $graph;

    /** @var Subject */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->graph = (new Graph)
            ->setStates([
                State::initial('init'),

                State::normal('in_progress'),

                State::final('done'),

                State::final('canceled')
                    ->variations(['in_progress']),
            ])
            ->setTransitions([
                Transition::new('init', 'in_progress', 'progress'),

                Transition::new('in_progress', 'canceled', 'cancel'),

                Transition::new('canceled', 'in_progress', 'open'),

                Transition::new('in_progress', 'done', 'finish'),
            ]);

        $this->subject = new Subject('');
    }

    /** @test */
    public function complex_test()
    {
        $this->graph->initialize($this->subject);

        $this->graph->apply($this->subject, 'progress');
        $this->assertEquals('in_progress', $this->subject->getFiniteState());

        $this->graph->apply($this->subject, 'cancel');
        $this->assertEquals('canceled:in_progress', $this->subject->getFiniteState());

        $this->graph->apply($this->subject, 'open');
        $this->assertEquals('in_progress', $this->subject->getFiniteState());

        $this->graph->apply($this->subject, 'finish');
        $this->assertEquals('done', $this->subject->getFiniteState());
    }
}
