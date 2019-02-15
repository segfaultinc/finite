<?php

namespace SegfaultInc\Finite\Tests;

use Mockery as M;
use SegfaultInc\Finite\State;
use SegfaultInc\Finite\Finite;
use PHPUnit\Framework\TestCase;
use SegfaultInc\Finite\Transition;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class HooksTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function it_allows_to_register_pre_hooks()
    {
        $hook = M::spy(function () {
            //
        });

        $hook2 = M::spy(function () {
            //
        });

        $finite = (new Finite)
            ->setStates([
                $new = State::initial('new'),
                $foo = State::normal('foo'),
            ])
            ->setTransitions([
                Transition::new($new, $foo, 'a')
                    ->pre($hook)
                    ->pre($hook2),
            ]);

        $finite->apply($subject = new SampleSubject('new'), 'a');

        $hook->shouldHaveBeenCalled()
            ->once()
            ->with($subject);

        $hook2->shouldHaveBeenCalled()
            ->once()
            ->with($subject);
    }

    /** @test */
    public function it_allows_to_register_post_hooks()
    {
        $hook = M::spy(function () {
            //
        });

        $hook2 = M::spy(function () {
            //
        });

        $finite = (new Finite)
            ->setStates([
                $new = State::initial('new'),
                $foo = State::normal('foo'),
            ])
            ->setTransitions([
                Transition::new($new, $foo, 'a')
                    ->post($hook)
                    ->post($hook2),
            ]);

        $finite->apply($subject = new SampleSubject('new'), 'a');

        $hook->shouldHaveBeenCalled()
            ->once()
            ->with($subject);

        $hook2->shouldHaveBeenCalled()
            ->once()
            ->with($subject);
    }

    /** @test */
    public function hooks_are_executed_in_correct_order()
    {
        $results = [];

        $pre = M::spy(function () use (&$results) {
            $results[] = 'pre';
        });

        $post = M::spy(function () use (&$results) {
            $results[] = 'post';
        });

        $finite = (new Finite)
            ->setStates([
                $new = State::initial('new'),
                $foo = State::normal('foo'),
            ])
            ->setTransitions([
                Transition::new($new, $foo, 'a')
                    ->pre($pre)
                    ->post($post),
            ]);

        $finite->apply($subject = new SampleSubject('new'), 'a');

        $this->assertSame(['pre', 'post'], $results);
    }

    /** @test */
    public function it_does_not_apply_transition_if_pre_hook_throws()
    {
        $pre = M::spy(function () {
            throw new Whoops('¯\_(ツ)_/¯');
        });

        $finite = (new Finite)
            ->setStates([
                $new = State::initial('new'),
                $foo = State::normal('foo'),
            ])
            ->setTransitions([
                Transition::new($new, $foo, 'a')
                    ->pre($pre),
            ]);

        try {
            $finite->apply($subject = new SampleSubject('new'), 'a');
        } catch (Whoops $e) {
            $this->assertEquals('new', $subject->getFiniteState());
        }
    }

    /** @test */
    public function it_allows_to_disable_and_reenable_hooks()
    {
        $hook = M::spy(function () {
            //
        });

        $finite = (new Finite)
            ->setStates([
                $new = State::initial('new'),
                $foo = State::normal('foo'),
            ])
            ->setTransitions([
                Transition::new($new, $foo, 'a')
                    ->pre($hook),
            ]);

        $finite->disableHooks();

        $finite->apply($subject = new SampleSubject('new'), 'a');

        $hook->shouldNotHaveBeenCalled();

        $finite->enableHooks();

        $finite->apply($subject = new SampleSubject('new'), 'a');

        $hook->shouldHaveBeenCalled()
             ->once()
             ->with($subject);
    }

    /** @test */
    public function it_allows_to_register_entering_hooks()
    {
        $hook = M::spy(function () {
            //
        });

        $finite = (new Finite)
            ->setStates([
                $new = State::initial('new'),
                $foo = State::normal('foo')
                    ->entering($hook),
            ])
            ->setTransitions([
                Transition::new($new, $foo, 'a'),
            ]);

        $finite->apply($subject = new SampleSubject('new'), 'a');

        $hook->shouldHaveBeenCalled()
            ->once()
            ->with($subject);
    }

    /** @test */
    public function it_allows_to_register_leaving_hooks()
    {
        $hook = M::spy(function () {
            //
        });

        $finite = (new Finite)
            ->setStates([
                $new = State::initial('new')
                    ->leaving($hook),
                $foo = State::normal('foo'),
            ])
            ->setTransitions([
                Transition::new($new, $foo, 'a'),
            ]);

        $finite->apply($subject = new SampleSubject('new'), 'a');

        $hook->shouldHaveBeenCalled()
            ->once()
            ->with($subject);
    }
}

class Whoops extends \Exception
{
    //
}
