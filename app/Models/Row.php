<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Livewire\Wireable;

class Row implements Wireable
{
    /**
     * @param array<Tile> $tiles
     * @param array<int> $uniqueCandidates
     */
    public function __construct(
        public int $blockRow,
        public int $blockColumn,
        public array $tiles,
        public array $uniqueCandidates,
    ) {}

    /** @return Collection<int, int> */
    public function uniqueCandidatesIn(Tile $tile): Collection
    {
        return collect($tile->candidates)->intersect($this->uniqueCandidates);
    }

    /** @return array{blockRow: int, blockColumn: int, tiles: array<Tile>, uniqueCandidates: array<int>} */
    public function toLivewire(): array
    {
        return [
            'blockRow' => $this->blockRow,
            'blockColumn' => $this->blockColumn,
            'tiles' => $this->tiles,
            'uniqueCandidates' => $this->uniqueCandidates,
        ];
    }

    /** @param array{blockRow: int, blockColumn: int, tiles: array<Tile>, uniqueCandidates: array<int>} $value */
    public static function fromLivewire(mixed $value): self
    {
        return new self(
            $value['blockRow'],
            $value['blockColumn'],
            $value['tiles'],
            $value['uniqueCandidates'],
        );
    }
}
