<?php

namespace SegfaultInc\Finite;

use Graphp\GraphViz\GraphViz;
use Fhaculty\Graph\Graph as VisualGraph;
use SegfaultInc\Finite\Support\Collection;

class Visualizer
{
    public function visualize(Graph $finite): void
    {
        $graph = new VisualGraph;

        $vertexes = Collection::make($finite->getStates())
            ->mapWithKeys(function (State $state) use ($graph) {
                $vertex = $graph->createVertex($state->getKey());

                if ($state->getType() == State::INITIAL) {
                    $vertex->setAttribute('graphviz.color', 'blue');
                } elseif ($state->getType() == State::FINAL) {
                    $vertex->setAttribute('graphviz.color', 'red');
                }

                return [$state->getKey() => $vertex];
            });

        Collection::make($finite->getTransitions())
            ->each(function (Transition $transition) use ($graph, $vertexes) {
                $from = $vertexes->toArray()[$transition->getFrom()];
                $to = $vertexes->toArray()[$transition->getTo()];

                $edge = $from->createEdgeTo($to);
                $edge->setAttribute('graphviz.label', $transition->getInput());
            });

        (new GraphViz)->display($graph);
    }
}
