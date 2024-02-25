<?php

namespace App\Models;

use Livewire\Wireable;

class Tile implements Wireable
{
    /** @param array<int> $candidates */
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

    /** @return array{row: int, column: int, value: ?int, candidates: array<int>} */
    public function toLivewire(): array
    {
        return [
            'row' => $this->row,
            'column' => $this->column,
            'value' => $this->value,
            'candidates' => $this->candidates,
        ];
    }

    /** @param array{row: int, column: int, value: ?int, candidates: array<int>} $value */
    public static function fromLivewire(mixed $value): self
    {
        return new self(
            $value['row'],
            $value['column'],
            $value['value'],
            $value['candidates'],
        );
    }
}
