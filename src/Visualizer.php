<?php

namespace SegfaultInc\Finite;

use Graphp\GraphViz\GraphViz;
use Fhaculty\Graph\Graph as VisualGraph;
use SegfaultInc\Finite\Support\Collection;

class FullVisualizer
{
    public function visualize(Graph $finite): void
    {
        $graph = new VisualGraph;

        $vertexes = Collection::make($finite->getStates())
            ->mapWithKeys(function ($state) use ($graph) {
                $vertex = $graph->createVertex($state->key);

                if ($state->type == State::INITIAL) {
                    $vertex->setAttribute('graphviz.color', 'blue');
                } elseif ($state->type == State::FINAL) {
                    $vertex->setAttribute('graphviz.color', 'red');
                }

                return [$state->key => $vertex];
            });

        Collection::make($finite->getTransitions())
            ->each(function ($transition) use ($graph, $vertexes) {
                $from = $vertexes->toArray()[$transition->from()];
                $to = $vertexes->toArray()[$transition->to()];

                $edge = $from->createEdgeTo($to);
                $edge->setAttribute('graphviz.label', $transition->input());
            });

        (new GraphViz)->display($graph);
    }
}
