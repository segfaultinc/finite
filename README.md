# WORK IN PROGRESS

---

## Usage

```php
$order = new Order(); // see below
$graph = new Graph(); // see below

$graph->apply($order, 'transition-input');
```

```php
use SegfaultInc\Finite\Subject;

class Order extends Model implements Subject
{
    public function setFiniteState($state)
    {
        $this->update([
            'status' => $state,
        ]);
    }

    public function getFiniteState($state)
    {
        return $this->status;
    }
}
```

## Graph definition

```php
(new Graph)
    ->setStates([

        State::initial('new')
            ->label('New')
            ->leaving(function (Order $order) {
                // This is executed every time the `in_progress` is **transitioned from**.
                // Useful if you want to do something that is "state" dependent

                $order->update([
                    'started_at' => now()
                ]);
            }),

        State::normal('in_progress')
            ->label('In Progress'),

        State::final('finished')
            ->label('Finised'),

        State::final('canceled')
            ->label('Canceled')
            ->entering(function (Order $order) {
                // If you transition to "canceled" state from multiple states,
                // this is the place to perform the common logic
            }),

    ])
    ->setTransitions([

        Transition::make('new', 'in_progress', 'progress')
            ->pre(function (Order $order) {
                if ($order->validate()) {
                    throw new OrderValidationException('When an exception is thrown in "pre" hook, the transition is not applied');
                }
            }),

        Transition::make('in_progress', 'finished', 'progress'),

        Transition::make('in_progress', 'canceled', 'cancel')
            ->post(function (Order $order) {
                $order->user->notify(new YourOrderWasCanceled);
            }),

    ]);
```

## Visualization

```php
$graph->visualize()

// uses GraphViz under the hood
```

![](https://user-images.githubusercontent.com/4470539/53287565-e4bfff00-3775-11e9-9aea-8b498ba5ac09.png)
