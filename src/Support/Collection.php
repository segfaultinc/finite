<?php

namespace SegfaultInc\Finite\Support;

final class Collection
{
    /** @var array */
    private $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function empty(): bool
    {
        return empty($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function first()
    {
        return $this->items[0] ?? null;
    }

    public function firstOr(callable $fn)
    {
        return $this->items[0] ?? $fn();
    }

    public function ifEmpty(callable $fn): self
    {
        if ($this->empty()) {
            $fn($this);
        }

        return $this;
    }

    public function map(callable $fn): self
    {
        return new self(array_values(array_map($fn, $this->items)));
    }

    public function filter(callable $fn): self
    {
        return new self(array_values(array_filter($this->items, $fn)));
    }

    public function each(callable $fn): self
    {
        foreach ($this->items as $item) {
            $fn($item);
        }

        return $this;
    }

    public function groupBy(callable $fn): self
    {
        $items = [];

        foreach ($this->items as $item) {
            $key = $fn($item);

            $items[$key] = new self(array_merge(
                $items[$key]->items ?? [],
                [$item]
            ));
        }

        return new self($items);
    }

    public function mapWithKeys(callable $fn): self
    {
        $items = [];

        foreach ($this->items as $item) {
            $items = array_merge($items, $fn($item));
        }

        return new self($items);
    }

    public function duplicates(callable $fn): self
    {
        return new self(
            array_keys(array_filter(array_count_values($this->map($fn)->toArray()), function ($count) {
                return $count > 1;
            }))
        );
    }

    public function implode(string $glue): string
    {
        return implode($glue, $this->items);
    }

    public function flatten(): self
    {
        return new self(array_reduce($this->items, 'array_merge', []));
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function dd(): void
    {
        die(var_dump($this->items));
    }

    public static function make(array $items = []): self
    {
        return new self($items);
    }
}
