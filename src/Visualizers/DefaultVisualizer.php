<?php

namespace SegfaultInc\Finite\Visualizers;

use Graphp\GraphViz\GraphViz;
use SegfaultInc\Finite\Graph;
use SegfaultInc\Finite\State;
use Fhaculty\Graph\Graph as VisualGraph;
use SegfaultInc\Finite\Support\Collection;

class DefaultVisualizer
{
    public function visualize(Graph $finite): void
    {
        $graph = new VisualGraph;

        $vertexes = Collection::make($finite->getStates())
            ->groupBy(function (State $state) {
                return $state->variationKey ?: $state->key;
            })
            ->map(function (Collection $states) {
                $state = clone $states->first();
                $state->key = $state->variationKey ?: $state->key;
                return $state;
            })
            ->mapWithKeys(function (State $state) use ($graph) {
                $vertex = $graph->createVertex($state->key);

                if ($state->type == State::INITIAL) {
                    $vertex->setAttribute('graphviz.color', 'blue');
                } elseif ($state->type == State::FINAL) {
                    $vertex->setAttribute('graphviz.color', 'red');
                }

                return [$state->key => $vertex];
            });

        Collection::make($finite->getTransitions())
            ->each(function ($transition) use ($graph, $vertexes, $finite) {
                $stateFrom = $finite->getState($transition->from());
                $stateTo = $finite->getState($transition->to());

                $from = $vertexes->toArray()[$stateFrom->variationKey ?: $stateFrom->key];
                $to = $vertexes->toArray()[$stateTo->variationKey ?: $stateTo->key];

                $edge = $from->createEdgeTo($to);
                $edge->setAttribute('graphviz.label', $transition->input());
            });

        (new GraphViz)->display($graph);
    }
}
