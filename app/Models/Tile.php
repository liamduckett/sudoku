<?php

namespace App\Models;

use Livewire\Wireable;

class Tile implements Wireable
{
    public function __construct(
        public int $row,
        public int $column,
        public ?int $value,
        public array $candidates,
    ) {}

    public function hasSoleCandidate(): bool
    {
        return count($this->candidates) === 1;
    }

    public function toLivewire(): array
    {
        return [
            'row' => $this->row,
            'column' => $this->column,
            'value' => $this->value,
            'candidates' => $this->candidates,
        ];
    }

    public static function fromLivewire($value): static
    {
        return new static(
            $value['row'],
            $value['column'],
            $value['value'],
            $value['candidates'],
        );
    }
}
