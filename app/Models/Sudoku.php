<?php

namespace App\Models;

use Livewire\Wireable;

class Sudoku implements Wireable
{
    public array $grid;

    public function __construct(array $grid)
    {
        // I think 3x3 grid is easier to work with when arrays start at 1
        $this->grid = $this->recursivelyIndexFromOne($grid);
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

        $section = [];
        $section[] = array_column($rows, $blockColumn * 3 - 2);
        $section[] = array_column($rows, $blockColumn * 3 - 1);
        $section[] = array_column($rows, $blockColumn * 3);

        return $section;
    }

    protected function recursivelyIndexFromOne(array $array): array
    {
        foreach($array as $key => $item) {
            if(is_array($item)) {
                $array[$key] = $this->recursivelyIndexFromOne($item);
            }
        }

        $keys = range(1, count($array));

        return array_combine($keys, $array);
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
