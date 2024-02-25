<?php

namespace App\Models;

use Livewire\Wireable;

class Column implements Wireable
{
    /** @param array<Tile> $tiles */
    public function __construct(
        public int $blockRow,
        public int $blockColumn,
        public array $tiles,
    ) {}

    /** @return array{blockRow: int, blockColumn: int, tiles: array<Tile>} */
    public function toLivewire(): array
    {
        return [
            'blockRow' => $this->blockRow,
            'blockColumn' => $this->blockColumn,
            'tiles' => $this->tiles,
        ];
    }

    /** @param array{blockRow: int, blockColumn: int, tiles: array<Tile>} $value */
    public static function fromLivewire(mixed $value): self
    {
        return new self(
            $value['blockRow'],
            $value['blockColumn'],
            $value['tiles'],
        );
    }
}
