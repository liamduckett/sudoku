<?php

namespace App\Livewire;

use App\Models\Sudoku;
use App\Models\Tile;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Game extends Component
{
    public Sudoku $sudoku;

    public function mount(): void
    {
        $grid = [
            [null, null, 4, null, null, 7, 8, 3, null],
            [null, 1, null, 9, 3, null, 7, null, 5],
            [3, 5, 7, null, null, 4, 1, null, null],
            [null, 3, null, null, null, 2, 9, 8, 4],
            [null, null, null, null, 8, 1, null, null, null],
            [null, 2, null, null, null, null, 6, null, null],
            [4, 7, 3, 2, null, 8, null, null, 1],
            [2, 6, null, 1, null, null, null, 7, 8],
            [5, null, 1, 6, null, null, 4, null, null],
        ];

        $this->sudoku = Sudoku::setUp($grid);
    }

    public function render(): View
    {
        $tile = new Tile(0, 0);

        return view('livewire.game', [
            'grid' => $this->sudoku->grid,
        ]);
    }

    public function advance(): void
    {
        match($this->sudoku->hasMetaData()) {
            true => $this->sudoku->fillChoicelessTiles(),
            false => $this->sudoku->addMetaData(),
        };
    }
}
