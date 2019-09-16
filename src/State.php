<?php

namespace SegfaultInc\Finite;

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

    /** @var array */
    protected $hooks = [
        'entering' => [],
        'entered'  => [],
        'leaving'  => [],
        'left'     => [],
    ];

    private function __construct(string $type, string $key, string $label = null)
    {
        $this->key = $key;
        $this->type = $type;
        $this->label = $label ?: $key;
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
