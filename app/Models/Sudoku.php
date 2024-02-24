<?php

namespace App\Models;

use Illuminate\Support\Collection;
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
        $nearby = collect([
            ...$this->row($tile),
            ...$this->column($tile),
            ...$this->section($tile),
        ]);

        $unplayable = $nearby
            ->filter(fn(Tile $tile) => $tile->value !== null)
            ->map(fn(Tile $tile) => $tile->value)
            ->toArray();

        return collect([1, 2, 3, 4, 5, 6, 7, 8, 9])
            ->diff($unplayable)
            ->values()
            ->toArray();
    }

    public function hasMetaData(): bool
    {
        $firstEmptyTile = $this->emptyTiles()->first();

        return $firstEmptyTile?->candidates !== [];
    }

    public function playDefiniteCandidates(): void
    {
        $this->playSoleCandidates();
        $this->playUniqueCandidates();

        foreach($this->grid as $row) {
            foreach($row as $tile) {
                $tile->candidates = [];
            }
        }
    }

    public function playSoleCandidates(): void
    {
        $emptyTiles = $this->emptyTiles();

        foreach($emptyTiles as $tile) {
            if($tile->hasSoleCandidate()) {
                $tile->value = $tile->candidates[0];
            }
        }
    }

    public function playUniqueCandidates(): void
    {
        $emptyTiles = $this->emptyTiles();

        foreach($emptyTiles as $tile) {
            // check if a unique candidate in row,
            //   later I'll need to do column or section...

            $emptyRowTiles = array_filter(
                $this->row($tile),
                fn(Tile $tile) => $tile->value === null,
            );

            $emptyRowTileCandidates = array_map(
                fn(Tile $tile) => $tile->candidates,
                $emptyRowTiles,
            );

            $hey = array_merge([], ...$emptyRowTileCandidates);

            if(min(array_count_values($hey)) === 1) {
                dd("there is a unique candidate!");
            }
        }
    }

    public function addMetaData(): void
    {
        $emptyTiles = $this->emptyTiles();

        foreach($emptyTiles as $tile) {
            $tile->candidates = $this->canBePlayedAt($tile);
        }
    }

    /** @return array<array<Tile>> */
    protected static function addEmptyMetaData($grid): array
    {
        $newGrid = [];

        foreach($grid as $rowKey => $row) {
            foreach($row as $columnKey => $item) {
                $tile = new Tile(
                    row: $rowKey,
                    column: $columnKey,
                    value: $item,
                    candidates: [],
                );

                $newGrid[$rowKey][$columnKey] = $tile;
            }
        }

        return $newGrid;
    }

    /** @return Collection<Tile> */
    protected function emptyTiles(): Collection
    {
        // Map over each row, only returning the empty tiles
        return collect($this->grid)
            ->flatMap(
                fn(array $row) => array_filter($row, fn(Tile $tile) => $tile->value === null)
            );
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
