<?php

namespace App\Models;

class Sudoku
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
}
