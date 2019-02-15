<?php

namespace SegfaultInc\Finite;

class State
{
    const INITIAL = 'INITIAL';
    const NORMAL = 'NORMAL';
    const FINAL = 'FINAL';

    public $key;
    public $type;
    public $label;
    public $extra;

    private $hooks = [
        'entering' => [],
        'leaving'  => [],
    ];

    private function __construct(string $type, string $key)
    {
        $this->key = $this->label = $key;
        $this->type = $type;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function extra(array $extra): self
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * Hooks.
     */
    public function entering(callable $hook): self
    {
        $this->hooks['entering'][] = $hook;

        return $this;
    }

    public function leaving(callable $hook): self
    {
        $this->hooks['leaving'][] = $hook;

        return $this;
    }

    public function executeEnteringHooks(Subject $subject): void
    {
        foreach ($this->hooks['entering'] as $hook) {
            $hook($subject);
        }
    }

    public function executeLeavingHooks(Subject $subject): void
    {
        foreach ($this->hooks['leaving'] as $hook) {
            $hook($subject);
        }
    }

    public function toArray(): array
    {
        return [
            'key'   => $this->key,
            'type'  => $this->type,
            'label' => $this->label,
            'extra' => $this->extra,
        ];
    }

    public static function initial(string $key): self
    {
        return new self(self::INITIAL, $key);
    }

    public static function normal(string $key): self
    {
        return new self(self::NORMAL, $key);
    }

    public static function final(string $key): self
    {
        return new self(self::FINAL, $key);
    }
}
