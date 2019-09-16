<?php

namespace SegfaultInc\Finite;

use SegfaultInc\Finite\Support\Hooks;

class State
{
    const INITIAL = 'INITIAL';
    const NORMAL = 'NORMAL';
    const FINAL = 'FINAL';

    /** @var string */
    protected $key;

    /** @var string */
    protected $type;

    /** @var string */
    protected $label;

    /** @var array */
    protected $extra = [];

    /** @var \SegfaultInc\Finite\Support\Hooks */
    protected $hooks;

    private function __construct(string $type, string $key, string $label = null)
    {
        $this->key = $key;
        $this->type = $type;
        $this->label = $label ?: $key;

        $this->hooks = new Hooks;
    }

    /**
     * Get state key.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get state type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get state label.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Set extra properties.
     */
    public function setExtra(array $extra): self
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * Get extra properties.
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * Register hook for when entering this state.
     */
    public function entering(callable $hook): self
    {
        $this->hooks->register('entering', $hook);

        return $this;
    }

    /**
     * Register hook for when after entered this state.
     */
    public function entered(callable $hook): self
    {
        $this->hooks->register('entered', $hook);

        return $this;
    }

    /**
     * Register hook for when leaving this state.
     */
    public function leaving(callable $hook): self
    {
        $this->hooks->register('leaving', $hook);

        return $this;
    }

    /**
     * Register hook for when after left this state.
     */
    public function left(callable $hook): self
    {
        $this->hooks->register('left', $hook);

        return $this;
    }

    /**
     * Get hooks configuration.
     */
    public function getHooks(): Hooks
    {
        return $this->hooks;
    }

    /**
     * Create new initial state.
     */
    public static function initial(string $key, string $label = null): self
    {
        return new self(self::INITIAL, $key, $label);
    }

    /**
     * Create new normal state.
     */
    public static function normal(string $key, string $label = null): self
    {
        return new self(self::NORMAL, $key, $label);
    }

    /**
     * Create new final state.
     */
    public static function final(string $key, string $label = null): self
    {
        return new self(self::FINAL, $key, $label);
    }
}
