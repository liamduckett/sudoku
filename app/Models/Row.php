<?php

namespace App\Models;

use Livewire\Wireable;

class Row implements Wireable
{
    /** @param array<Tile> $tiles */
    public function __construct(
        public int $blockRow,
        public int $blockColumn,
        public array $tiles,
        public array $uniqueCandidates,
    ) {}

    public function isUniqueCandidate(Tile $tile): bool
    {
        return array_intersect($tile->candidates, $this->uniqueCandidates) !== [];
    }

    public function toLivewire(): array
    {
        return [
            'blockRow' => $this->blockRow,
            'blockColumn' => $this->blockColumn,
            'tiles' => $this->tiles,
            'uniqueCandidates' => $this->uniqueCandidates,
        ];
    }

    public static function fromLivewire($value): static
    {
        return new static(
            $value['blockRow'],
            $value['blockColumn'],
            $value['tiles'],
            $value['uniqueCandidates'],
        );
    }
}
