<?php

namespace SegfaultInc\Finite\Tests;

use Mockery;
use Graphp\GraphViz\GraphViz;
use SegfaultInc\Finite\Graph;
use SegfaultInc\Finite\State;
use PHPUnit\Framework\TestCase;
use SegfaultInc\Finite\Transition;
use SegfaultInc\Finite\Visualizer;
use Fhaculty\Graph\Graph as VisualGraph;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class VisualizerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function visualizer_test()
    {
        $graph = new VisualGraph;

        $foo = $graph->createVertex('foo');
        $foo->setAttribute('graphviz.color', 'blue');

        $bar = $graph->createVertex('bar');

        $edge = $foo->createEdgeTo($bar);
        $edge->setAttribute('graphviz.label', 'x');

        $mock = Mockery::mock(GraphViz::class);

        $mock->shouldReceive('display')
            ->once()
            ->with(Mockery::on(function ($g) use ($graph) {
                $this->assertEquals($graph, $g);

                return true;
            }));

        $graph = Graph::make([
            State::initial('foo'),
            State::normal('bar'),
        ], [
            Transition::make('foo', 'bar', 'x'),
        ]);

        (new Visualizer)->withMockRenderer($mock)->visualize($graph);
    }
}
