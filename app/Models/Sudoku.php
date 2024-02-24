<?php

namespace App\Models;

use Livewire\Wireable;

class Sudoku implements Wireable
{
    public function __construct(public array $grid) {}

    public static function setUp(array $grid): static
    {
        // I think 3x3 grid is easier to work with when arrays start at 1
        $grid = static::recursivelyIndexFromOne($grid);
        $grid = static::addEmptyMetaData($grid);

        return new static($grid);
    }

    public function row(Tile $tile): array
    {
        return $this->grid[$tile->row];
    }

    public function column(Tile $tile): array
    {
        return array_column($this->grid, $tile->column);
    }

    public function section(Tile $tile): array
    {
        // maps:
        //  1,2,3 => 1
        //  4,5,6 => 2
        //  7,8,9 => 3
        $blockRow = (int) floor(($tile->row - 1) / 3) + 1;
        $blockColumn = (int) floor(($tile->column - 1) / 3) + 1;

        // get the rows between in the block
        $rows = array_slice($this->grid, $blockRow * 3 - 3, 3);

        return array_merge([], ...[
            array_column($rows, $blockColumn * 3 - 2),
            array_column($rows, $blockColumn * 3 - 1),
            array_column($rows, $blockColumn * 3),
        ]);
    }

    public function canBePlayedAt(Tile $tile): array
    {
        $nearby = [
            ...$this->row($tile),
            ...$this->column($tile),
            ...$this->section($tile),
        ];

        $values = array_map(fn(array $item) => $item['value'], $nearby);
        $unplayable = array_filter($values, fn(?int $item) => $item !== null);
        $options = [1, 2, 3, 4, 5, 6, 7, 8, 9];

        return array_diff($options, $unplayable);
    }

    public function hasMetaData(): bool
    {
        $hasMetaData = false;

        foreach($this->grid as $row) {
            foreach ($row as $item) {
                if ($item['value'] === null) {
                    $hasMetaData = $item['meta'] !== [];
                }
            }
        }

        return $hasMetaData;
    }

    public function fillChoicelessTiles(): void
    {
        foreach($this->grid as $rowKey => $row) {
            foreach($row as $columnKey => $item) {
                if($item['value'] === null and count($item['meta']) === 1) {
                    $this->grid[$rowKey][$columnKey]['value'] = $item['meta'][array_key_first($item['meta'])];
                }

                $this->grid[$rowKey][$columnKey]['meta'] = [];
            }
        }
    }

    public function addMetaData(): void
    {
        foreach($this->grid as $rowKey => $row) {
            foreach($row as $columnKey => $item) {
                if($item['value'] === null) {
                    // only do this when tile is null
                    $tile = new Tile($rowKey, $columnKey);

                    $this->grid[$rowKey][$columnKey]['meta'] = $this->canBePlayedAt($tile);
                }
            }
        }
    }

    protected static function recursivelyIndexFromOne(array $array): array
    {
        foreach($array as $key => $item) {
            if(is_array($item)) {
                $array[$key] = static::recursivelyIndexFromOne($item);
            }
        }

        $keys = range(1, count($array));

        return array_combine($keys, $array);
    }

    protected static function addEmptyMetaData($grid): array
    {
        $newGrid = [];

        foreach($grid as $rowKey => $row) {
            foreach($row as $columnKey => $item) {
                $item = [
                    'meta' => [],
                    'value' => $item,
                ];

                $newGrid[$rowKey][$columnKey] = $item;
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
