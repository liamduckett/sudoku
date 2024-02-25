<?php

namespace App\Models;

use Exception;
use Livewire\Wireable;

class Tile implements Wireable
{
    /** @param array<Candidate> $candidates */
    public function __construct(
        public int $row,
        public int $column,
        public ?int $value,
        public array $candidates,
    ) {}

    public function blockRow(): int
    {
        return floor($this->row / 3);
    }

    public function blockColumn(): int
    {
        return floor($this->column / 3);
    }

    public function hasSoleCandidate(): bool
    {
        return count($this->candidates) === 1;
    }

    public function hasUniqueCandidate(): bool
    {
        // should only be 1
        $uniqueCandidate = array_filter(
            $this->candidates,
            fn(Candidate $candidate) => $candidate->unique === true,
        );

        return count($uniqueCandidate) === 1;
    }

    // TODO: use a conditional return type here
    public function uniqueCandidate(): ?Candidate
    {
        // should only be 1
        $uniqueCandidates = array_filter(
            $this->candidates,
            fn(Candidate $candidate) => $candidate->unique === true,
        );

        if(count($uniqueCandidates) > 1) {
            throw new Exception("Two unique candidates in one tile");
        }

        return count($uniqueCandidates) === 1
            ? $uniqueCandidates[array_key_first($uniqueCandidates)]
            : null;
    }

    /** @return array{row: int, column: int, value: ?int, candidates: array<Candidate>} */
    public function toLivewire(): array
    {
        return [
            'row' => $this->row,
            'column' => $this->column,
            'value' => $this->value,
            'candidates' => $this->candidates,
        ];
    }

    /** @param array{row: int, column: int, value: ?int, candidates: array<Candidate>} $value */
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
