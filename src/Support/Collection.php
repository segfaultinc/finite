<?php

namespace SegfaultInc\Finite\Support;

use Countable;
use ArrayAccess;

class Collection implements Countable, ArrayAccess
{
    /** @var array */
    private $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function first(callable $fn = null)
    {
        return $this->items[0] ?? $fn();
    }

    public function filter(callable $fn): self
    {
        return new static(array_filter($this->items, $fn));
    }

    public function map(callable $fn): self
    {
        return new static(array_map($fn, $this->items));
    }

    public function each(callable $fn): void
    {
        foreach ($this->items as $item) {
            $fn($item);
        }
    }

    public function duplicates(): self
    {
        return new static(array_values(array_unique(array_diff_assoc($this->items, array_unique($this->items)))));
    }

    public function groupBy(callable $fn): self
    {
        return new self(array_reduce($this->items, function ($xs, $x) use ($fn) {
            $key = $fn($x);
            $xs[$key] = $xs[$key] ?? new static([]);
            $xs[$key][] = $x;
            return $xs;
        }, []));
    }

    public function intersect(array $other): self
    {
        return new static(array_values(array_intersect($this->items, $other)));
    }

    public function implode(string $glue): string
    {
        return implode($glue, $this->items);
    }

    public function keys(): self
    {
        return new self(array_keys($this->items));
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function empty(): bool
    {
        return $this->count() == 0;
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function whenEmpty(callable $fn): self
    {
        if ($this->empty()) {
            $fn($this);
        }

        return $this;
    }

    public function whenNotEmpty(callable $fn): self
    {
        if (!$this->empty()) {
            $fn($this);
        }

        return $this;
    }

    public function offsetSet($key, $value)
    {
        if ($key) {
            $this->items[$key] = $value;
        } else {
            $this->items[] = $value;
        }
    }

    public function offsetExists($key)
    {
        return isset($this->items[$key]);
    }

    public function offsetUnset($key)
    {
        unset($this->items[$key]);
    }

    public function offsetGet($key)
    {
        return $this->items[$key];
    }

    public static function make(array $items = []): self
    {
        return new self($items);
    }
}
