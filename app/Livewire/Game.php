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
        // easy
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

        // medium
        $grid = [
            [null, null, null, 4, null, 7, 5, null, null],
            [null, 7, null, null, null, 5, null, null, 2],
            [5, 4, null, 6, null, null, null, null, 7],
            [null, null, null, null, 6, null, 2, 1, null,],
            [6, 2, null, 5, 9, 8, 7, null, null],
            [null, 9, null, null, null, null, 6, null, null],
            [null, 5, null, null, 7, 1, 9, null, null],
            [null, null, null, null, null, 9, 8, null, null],
            [null, null, 8, null, null, 6, null, null, 3],
        ];

        // hard
        //$grid = [
        //    [null, null, null, null, null, null, null, null, null],
        //    [8, null, null, null, null, 7, 2, null, null],
        //    [null, null, 5, null, null, 1, null, 3, null],
        //    [null, 5, null, null, 1, 9, 8, null, null],
        //    [null, null, 6, null, null, null, 9, null, null],
        //    [null, null, 3, null, null, 2, 1, null, null],
        //    [null, 6, null, 7, null, null, null, 2, null],
        //    [null, null, 7, null, null, 5, null, 4, null],
        //    [null, null, null, 9, 6, null, null, null, null],
        //];

        // empty
        //$grid = [
        //    [null, null, null, null, null, null, null, null, null],
        //    [null, null, null, null, null, null, null, null, null],
        //    [null, null, null, null, null, null, null, null, null],
        //    [null, null, null, null, null, null, null, null, null],
        //    [null, null, null, null, null, null, null, null, null],
        //    [null, null, null, null, null, null, null, null, null],
        //    [null, null, null, null, null, null, null, null, null],
        //    [null, null, null, null, null, null, null, null, null],
        //    [null, null, null, null, null, null, null, null, null],
        //];

        // unique candidate test
        //$grid = [
        //    [null, null, 6, null, null, null, null, null, null],
        //    [null, null, null, null, null, null, null, 5, 4],
        //    [null, null, null, null, 5, 4, null, null, null],
        //    [null, null, null, null, null, null, null, null, null],
        //    [null, null, null, null, null, null, null, null, null],
        //    [null, null, null, null, null, null, null, null, null],
        //    [null, null, null, null, null, null, null, null, null],
        //    [null, null, null, null, null, null, null, null, null],
        //    [5, 4, null, null, null, null, null, null, null],
        //];

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
        match($this->sudoku->hasMetaData()) {
            true => $this->sudoku->playDefiniteCandidates(),
            false => $this->sudoku->addMetaData(),
        };
    }
}
