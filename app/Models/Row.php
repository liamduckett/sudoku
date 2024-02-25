<?php

namespace App\Models;

use Livewire\Wireable;

class Row implements Wireable
{
    /** @param array<Tile> $tiles */
    public function __construct(
        public array $tiles,
    ) {}

    /** @return array{tiles: array<Tile>} */
    public function toLivewire(): array
    {
        return [
            'tiles' => $this->tiles,
        ];
    }

    /** @param array{tiles: array<Tile>} $value */
    public static function fromLivewire(mixed $value): self
    {
        return new self(
            $value['tiles'],
        );
    }
}
