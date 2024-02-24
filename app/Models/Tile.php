<?php

namespace App\Models;

class Tile
{
    public function __construct(
        public int $row,
        public int $column,
    ) {}

    public function toDto(): array
    {
        return [
            'row' => $this->row,
            'column' => $this->column,
        ];
    }

    public static function fromDto(array $dto): self
    {
        return new static(
            $dto['row'],
            $dto['column']
        );
    }

    public function toJson(): string
    {
        return json_encode($this->toDto());
    }
}
