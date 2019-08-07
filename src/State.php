<?php

namespace SegfaultInc\Finite;

class State
{
    const INITIAL = 'INITIAL';
    const NORMAL = 'NORMAL';
    const FINAL = 'FINAL';

    /** @var string */
    public $key;

    /** @var string */
    public $type;

    /** @var string */
    public $label;

    /** @var array|null */
    public $variations = null;

    /** @var string|null */
    public $variationKey = null;

    /** @var array */
    public $extra = [];

    /** @var array */
    private $hooks = [
        'entering' => [],
        'entered'  => [],
        'leaving'  => [],
        'left'     => [],
    ];

    private function __construct(string $type, string $key)
    {
        $this->key = $this->label = $key;
        $this->type = $type;
    }

    /**
     * Set label for the state.
     */
    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Set extra properties for the state.
     */
    public function variations(array $variations): self
    {
        $this->variations = $variations;

        return $this;
    }

    /**
     * Set extra properties for the state.
     */
    public function extra(array $extra): self
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * Register hook for when entering this state.
     */
    public function entering(callable $hook): self
    {
        $this->hooks['entering'][] = $hook;

        return $this;
    }

    /**
     * Register hook for when after entered this state.
     */
    public function entered(callable $hook): self
    {
        $this->hooks['entered'][] = $hook;

        return $this;
    }

    /**
     * Register hook for when leaving this state.
     */
    public function leaving(callable $hook): self
    {
        $this->hooks['leaving'][] = $hook;

        return $this;
    }

    /**
     * Register hook for when after left this state.
     */
    public function left(callable $hook): self
    {
        $this->hooks['left'][] = $hook;

        return $this;
    }

    /**
     * Execute hooks when entering this state.
     */
    public function executeEnteringHooks(Subject $subject): void
    {
        foreach ($this->hooks['entering'] as $hook) {
            $hook($subject);
        }
    }

    /**
     * Execute hooks when after entered this state.
     */
    public function executeEnteredHooks(Subject $subject): void
    {
        foreach ($this->hooks['entered'] as $hook) {
            $hook($subject);
        }
    }

    /**
     * Execute hooks when leaving this state.
     */
    public function executeLeavingHooks(Subject $subject): void
    {
        foreach ($this->hooks['leaving'] as $hook) {
            $hook($subject);
        }
    }

    /**
     * Execute hooks when after left this state.
     */
    public function executeLeftHooks(Subject $subject): void
    {
        foreach ($this->hooks['left'] as $hook) {
            $hook($subject);
        }
    }

    /**
     * Return string representation.
     */
    public function __toString(): string
    {
        return $this->key;
    }

    /**
     * Clone the state and provide new key.
     */
    public function clone(string $key): self
    {
        $clone = clone $this;

        $clone->key = $key;
        $clone->variationKey = $this->key;

        return $clone;
    }

    /**
     * Create new initial state.
     */
    public static function initial(string $key): self
    {
        return new self(self::INITIAL, $key);
    }

    /**
     * Create new normal state.
     */
    public static function normal(string $key): self
    {
        return new self(self::NORMAL, $key);
    }

    /**
     * Create new final state.
     */
    public static function final(string $key): self
    {
        return new self(self::FINAL, $key);
    }
}
