<?php

namespace App\Livewire;

use App\Models\Sudoku;
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
        return view('livewire.game', [
            'grid' => $this->sudoku->grid,
        ]);
    }

    public function advance(): void
    {
        dd($this->sudoku->grid);

        // TODO: need somewhere to add this metadata first

        //foreach($this->sudoku->grid as $rowKey => $row) {
        //    foreach($row as $columnKey => $item) {
        //        // only do this when tile is null
        //        $tile = new Tile($rowKey, $columnKey);
        //
        //        dd($this->sudoku->canBePlayedAt($tile));
        //    }
        //}
    }
}
