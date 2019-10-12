<?php

namespace SegfaultInc\Finite\Tests;

use Mockery as M;
use SegfaultInc\Finite\Graph;
use SegfaultInc\Finite\State;
use PHPUnit\Framework\TestCase;
use SegfaultInc\Finite\Transition;
use SegfaultInc\Finite\Support\Hooks;
use SegfaultInc\Finite\Tests\Stubs\Subject;
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

        $finite = Graph::make([
            State::initial('new'),
            State::normal('foo'),
        ], [
            Transition::make('new', 'foo', 'a')
                ->pre($hook)
                ->pre($hook2),
        ]);

        $finite->apply($subject = new Subject('new'), 'a');

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

        $finite = Graph::make([
            State::initial('new'),
            State::normal('foo'),
        ], [
            Transition::make('new', 'foo', 'a')
                ->post($hook)
                ->post($hook2),
        ]);

        $finite->apply($subject = new Subject('new'), 'a');

        $hook->shouldHaveBeenCalled()
            ->once()
            ->with($subject);

        $hook2->shouldHaveBeenCalled()
            ->once()
            ->with($subject);
    }

    /** @test */
    public function pre_and_post_hooks_are_executed_in_correct_order()
    {
        $results = [];

        $pre = M::spy(function () use (&$results) {
            $results[] = 'pre';
        });

        $post = M::spy(function () use (&$results) {
            $results[] = 'post';
        });

        $finite = Graph::make([
            State::initial('new'),
            State::normal('foo'),
        ], [
            Transition::make('new', 'foo', 'a')
                ->pre($pre)
                ->post($post),
        ]);

        $finite->apply(new Subject('new'), 'a');

        $this->assertSame(['pre', 'post'], $results);
    }

    /** @test */
    public function it_does_not_apply_transition_if_pre_hook_throws()
    {
        $pre = M::spy(function () {
            throw new Whoops('¯\_(ツ)_/¯');
        });

        $finite = Graph::make([
            State::initial('new'),
            State::normal('foo'),
        ], [
            Transition::make('new', 'foo', 'a')
                ->pre($pre),
        ]);

        try {
            $finite->apply($subject = new Subject('new'), 'a');
        } catch (Whoops $e) {
            $this->assertEquals('new', $subject->getFiniteState());
        }
    }

    /** @test */
    public function it_allows_to_register_entering_hooks()
    {
        $hook = M::spy(function () {
            //
        });

        $finite = Graph::make([
            State::initial('new'),
            State::normal('foo')
                ->entering($hook),
        ], [
            Transition::make('new', 'foo', 'a'),
        ]);

        $finite->apply($subject = new Subject('new'), 'a');

        $hook->shouldHaveBeenCalled()
            ->once()
            ->with($subject);
    }

    /** @test */
    public function it_allows_to_register_entered_hooks()
    {
        $hook = M::spy(function () {
            //
        });

        $finite = Graph::make([
            State::initial('new'),
            State::normal('foo')
                ->entered($hook),
        ], [
            Transition::make('new', 'foo', 'a'),
        ]);

        $finite->apply($subject = new Subject('new'), 'a');

        $hook->shouldHaveBeenCalled()
            ->once()
            ->with($subject);
    }

    /** @test */
    public function entering_and_entered_hooks_are_executed_in_correct_order()
    {
        $states = [];

        $entering = function (Subject $subject) use (&$states) {
            $states[] = 'entering:'.$subject->getFiniteState();
        };

        $entered = function (Subject $subject) use (&$states) {
            $states[] = 'entered:'.$subject->getFiniteState();
        };

        $finite = Graph::make([
            State::initial('new'),
            State::normal('foo')
                ->entering($entering)
                ->entered($entered),
        ], [
            Transition::make('new', 'foo', 'a'),
        ]);

        $finite->apply($subject = new Subject('new'), 'a');

        $this->assertEquals(['entering:new', 'entered:foo'], $states);
    }

    /** @test */
    public function it_allows_to_register_leaving_hooks()
    {
        $hook = M::spy(function () {
            //
        });

        $finite = Graph::make([
            State::initial('new')
                ->leaving($hook),
            State::normal('foo'),
        ], [
            Transition::make('new', 'foo', 'a'),
        ]);

        $finite->apply($subject = new Subject('new'), 'a');

        $hook->shouldHaveBeenCalled()
            ->once()
            ->with($subject);
    }

    /** @test */
    public function it_allows_to_register_left_hooks()
    {
        $hook = M::spy(function () {
            //
        });

        $finite = Graph::make([
            State::initial('new')
                ->left($hook),
            State::normal('foo'),
        ], [
            Transition::make('new', 'foo', 'a'),
        ]);

        $finite->apply($subject = new Subject('new'), 'a');

        $hook->shouldHaveBeenCalled()
            ->once()
            ->with($subject);
    }

    /** @test */
    public function leaving_and_left_hooks_are_executed_in_correct_order()
    {
        $states = [];

        $leaving = function (Subject $subject) use (&$states) {
            $states[] = 'leaving:'.$subject->getFiniteState();
        };

        $left = function (Subject $subject) use (&$states) {
            $states[] = 'left:'.$subject->getFiniteState();
        };

        $finite = Graph::make([
            State::initial('new')
                ->leaving($leaving)
                ->left($left),
            State::normal('foo'),
        ], [
            Transition::make('new', 'foo', 'a'),
        ]);

        $finite->apply($subject = new Subject('new'), 'a');

        $this->assertEquals(['leaving:new', 'left:foo'], $states);
    }

    /** @test */
    public function it_allows_to_register_global_transition_applying_hooks()
    {
        $hook = M::spy(function () {
            //
        });

        $finite = Graph::make([
            $new = State::initial('new'),
            $foo = State::normal('foo'),
        ], [
            $new_to_foo = Transition::make('new', 'foo', 'a'),
        ])->applying($hook);

        $finite->apply($subject = new Subject('new'), 'a');

        $hook->shouldHaveBeenCalled()
            ->once()
            ->with($subject, $new, $foo, $new_to_foo);
    }

    /** @test */
    public function it_allows_to_register_global_transition_applied_hooks()
    {
        $hook = M::spy(function () {
            //
        });

        $finite = Graph::make([
            $new = State::initial('new'),
            $foo = State::normal('foo'),
        ], [
            $new_to_foo = Transition::make('new', 'foo', 'a'),
        ])->applied($hook);

        $finite->apply($subject = new Subject('new'), 'a');

        $hook->shouldHaveBeenCalled()
            ->once()
            ->with($subject, $new, $foo, $new_to_foo);
    }

    /** @test */
    public function applying_and_applied_hooks_are_executed_in_correct_order()
    {
        $states = [];

        $applying = function (Subject $subject, State $from, State $to) use (&$states) {
            $states[] = 'applying:'.$subject->getFiniteState();
        };

        $applied = function (Subject $subject, State $from, State $to) use (&$states) {
            $states[] = 'applied:'.$subject->getFiniteState();
        };

        $finite = Graph::make([
            State::initial('new'),
            State::normal('foo'),
        ], [
            Transition::make('new', 'foo', 'a'),
        ])
        ->applying($applying)
        ->applied($applied);

        $finite->apply($subject = new Subject('new'), 'a');

        $this->assertEquals(['applying:new', 'applied:foo'], $states);
    }

    /** @test */
    public function entering_hooks_are_executed_when_initializing()
    {
        $hook = M::spy(function () {
            //
        });

        $finite = Graph::make([
            State::initial('new')
                ->entering($hook),
        ], []);

        $finite->initialize($subject = new Subject('[empty]'));

        $hook->shouldHaveBeenCalled()
            ->once()
            ->with($subject);
    }

    /** @test */
    public function entered_hooks_are_executed_when_initializing()
    {
        $hook = M::spy(function () {
            //
        });

        $finite = Graph::make([
            State::initial('new')
                ->entered($hook),
        ], []);

        $finite->initialize($subject = new Subject('[empty]'));

        $hook->shouldHaveBeenCalled()
            ->once()
            ->with($subject);
    }

    /** @test */
    public function entering_and_entered_hooks_are_executed_in_correct_order_when_initializing()
    {
        $states = [];

        $entering = function (Subject $subject) use (&$states) {
            $states[] = 'entering:'.$subject->getFiniteState();
        };

        $entered = function (Subject $subject) use (&$states) {
            $states[] = 'entered:'.$subject->getFiniteState();
        };

        $finite = Graph::make([
            State::initial('new')
                ->entering($entering)
                ->entered($entered),
        ], []);

        $finite->initialize($subject = new Subject('[empty]'));

        $this->assertEquals(['entering:[empty]', 'entered:new'], $states);
    }

    /** @test */
    public function objects_with_hooks_can_be_serialized()
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('serialized foo');

        $object = new ObjectWithHooks;

        $object->register('foo', function () {
            throw new \Exception('serialized foo');
        });

        unserialize(serialize($object))->execute('foo');
    }
}

class Whoops extends \Exception
{
    //
}

class ObjectWithHooks
{
    /** @var Hooks */
    private $hooks;

    public function __construct()
    {
        $this->hooks = new Hooks;
    }

    public function register(string $key, callable $hook): void
    {
        $this->hooks->register($key, $hook);
    }

    public function execute(string $key)
    {
        $this->hooks->execute($key);
    }
}
