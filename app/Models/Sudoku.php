<?php

namespace App\Models;

use Livewire\Wireable;

class Sudoku implements Wireable
{
    /** @param array<array<Tile>> $grid */
    public function __construct(public array $grid) {}

    public static function setUp(array $grid): static
    {
        // I think 3x3 grid is easier to work with when arrays start at 1
        $grid = static::addEmptyMetaData($grid);
        return new static($grid);
    }

    /** @return array<Tile> */
    public function row(Tile $tile): array
    {
        return $this->grid[$tile->row];
    }

    /** @return array<Tile> */
    public function column(Tile $tile): array
    {
        return array_column($this->grid, $tile->column);
    }

    /** @return array<Tile> */
    public function section(Tile $tile): array
    {
        // maps:
        //  0,1,2 => 0
        //  3,4,5 => 1
        //  6,7,8 => 2
        $blockRow = floor($tile->row / 3);
        $blockColumn = floor($tile->column / 3);

        // get the rows between in the block
        // maps:
        //  0 => 0,1,2
        //  1 => 3,4,5
        //  2 => 6,7,8
        $rows = array_slice($this->grid, $blockRow * 3, 3);

        return array_merge([], ...[
            array_column($rows, $blockColumn * 3),
            array_column($rows, $blockColumn * 3 + 1),
            array_column($rows, $blockColumn * 3 + 2),
        ]);
    }

    /** @return array<int> */
    public function canBePlayedAt(Tile $tile): array
    {
        $nearby = [
            ...$this->row($tile),
            ...$this->column($tile),
            ...$this->section($tile),
        ];

        $values = array_map(fn(Tile $tile) => $tile->value, $nearby);
        $unplayable = array_filter($values, fn(?int $value) => $value !== null);
        $options = [1, 2, 3, 4, 5, 6, 7, 8, 9];

        return array_diff($options, $unplayable);
    }

    public function hasMetaData(): bool
    {
        $hasMetaData = false;

        foreach($this->grid as $row) {
            foreach ($row as $item) {
                if ($item->value === null) {
                    $hasMetaData = $item->meta !== [];
                    break 2;
                }
            }
        }

        return $hasMetaData;
    }

    public function fillChoicelessTiles(): void
    {
        foreach($this->grid as $rowKey => $row) {
            foreach($row as $columnKey => $item) {
                if($item->value === null and count($item->meta) === 1) {
                    $this->grid[$rowKey][$columnKey]->value = $item->meta[array_key_first($item->meta)];
                }

                $this->grid[$rowKey][$columnKey]->meta = [];
            }
        }
    }

    public function addMetaData(): void
    {
        foreach($this->grid as $row) {
            foreach($row as $item) {
                if($item->value === null) {
                    // only do this when tile is null
                    $item->meta = $this->canBePlayedAt($item);
                }
            }
        }
    }

    protected static function addEmptyMetaData($grid): array
    {
        $newGrid = [];

        foreach($grid as $rowKey => $row) {
            foreach($row as $columnKey => $item) {
                $tile = new Tile(
                    row: $rowKey,
                    column: $columnKey,
                    value: $item,
                    meta: [],
                );

                $newGrid[$rowKey][$columnKey] = $tile;
            }
        }

        return $newGrid;
    }

    public function toLivewire(): array
    {
        return $this->grid;
    }

    public static function fromLivewire($value): static
    {
        return new static($value);
    }
}
