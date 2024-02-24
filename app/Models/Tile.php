<?php

namespace App\Models;

class Tile
{
    public function __construct(
        public int $row,
        public int $column,
    ) {}
}
