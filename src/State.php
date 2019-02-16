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

    /** @var array */
    public $extra;

    /** @var array */
    private $hooks = [
        'entering' => [],
        'leaving'  => [],
    ];

    private function __construct(string $type, string $key)
    {
        $this->key = $key;
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
     * Register hook for when leaving this state.
     */
    public function leaving(callable $hook): self
    {
        $this->hooks['leaving'][] = $hook;

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
     * Execute hooks when leaving this state.
     */
    public function executeLeavingHooks(Subject $subject): void
    {
        foreach ($this->hooks['leaving'] as $hook) {
            $hook($subject);
        }
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
