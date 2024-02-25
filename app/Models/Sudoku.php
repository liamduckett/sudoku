<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Livewire\Wireable;

class Sudoku implements Wireable
{
    /** @param array<Row> $grid */
    public function __construct(public array $grid) {}

    /** @param array<array<?int>> $grid */
    public static function setUp(array $grid): self
    {
        $grid = self::addEmptyMetaData($grid);
        return new self($grid);
    }

    /** @return array<array<Tile>> */
    public function toArray(): array
    {
        return array_map(
            fn(Row $row) => $row->tiles,
            $this->grid,
        );
    }

    /** @return array<Tile> */
    public function row(Tile $tile): array
    {
        return $this->grid[$tile->row]->tiles;
    }

    /** @return array<Tile> */
    public function column(Tile $tile): array
    {
        return array_column($this->toArray(), $tile->column);
    }

    /** @return array<Tile> */
    public function section(Tile $tile): array
    {
        // maps:
        //  0,1,2 => 0
        //  3,4,5 => 1
        //  6,7,8 => 2
        $blockRow = (int) floor($tile->row / 3);
        $blockColumn = (int) floor($tile->column / 3);

        // get the rows between in the block
        // maps:
        //  0 => 0,1,2
        //  1 => 3,4,5
        //  2 => 6,7,8
        $rows = array_slice($this->toArray(), $blockRow * 3, 3);

        return array_merge([], ...[
            array_column($rows, $blockColumn * 3),
            array_column($rows, $blockColumn * 3 + 1),
            array_column($rows, $blockColumn * 3 + 2),
        ]);
    }

    /** @return array<Candidate> */
    public function canBePlayedAt(Tile $tile): array
    {
        $nearby = collect([
            ...$this->row($tile),
            ...$this->column($tile),
            ...$this->section($tile),
        ]);

        /** @var array<int> $unplayable */
        $unplayable = $nearby
            ->filter(fn(Tile $tile) => $tile->value !== null)
            ->map(fn(Tile $tile) => $tile->value)
            ->toArray();

        /** @var array<int> $playable */
        $playable = collect([1, 2, 3, 4, 5, 6, 7, 8, 9])
            ->diff($unplayable)
            ->values()
            ->toArray();

        return array_map(
            fn(int $value) => new Candidate($value, unique: false),
            $playable,
        );
    }

    /** @return array<int> */
    public function checkForUniqueCandidates(Row $row): array
    {
        $emptyRowTileCandidates = collect($row->tiles)
            ->filter(fn(Tile $tile) => $tile->value === null)
            ->flatMap(fn(Tile $tile) => $tile->candidates)
            ->map(fn (Candidate $candidate) => $candidate->value)
            ->countBy()
            ->toArray();

        return array_keys($emptyRowTileCandidates, 1);
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
            foreach($row->tiles as $tile) {
                $tile->candidates = [];
            }
        }
    }

    public function playSoleCandidates(): void
    {
        $emptyTiles = $this->emptyTiles();

        foreach($emptyTiles as $tile) {
            if($tile->hasSoleCandidate()) {
                $tile->value = $tile->candidates[0]->value;
            }
        }
    }

    public function playUniqueCandidates(): void
    {
        // I need to loop through <something> and determine if it has a unique candidate
        // I think this should be the tile.
        // we do this by annotating each candidate in each empty tile?
        // first step is to make $tile->candidates an array of Candidates
    }

    public function addMetaData(): void
    {
        $emptyTiles = $this->emptyTiles();

        foreach($emptyTiles as $tile) {
            $tile->candidates = $this->canBePlayedAt($tile);
        }

        foreach($this->grid as $row) {
            $row->uniqueCandidates = $this->checkForUniqueCandidates($row);
        }
    }

    /**
     * @param array<array<?int>> $grid
     * @return array<Row>
     */
    protected static function addEmptyMetaData(array $grid): array
    {
        $newGrid = [];

        foreach($grid as $rowKey => $row) {
            $newRow = [];

            foreach($row as $columnKey => $item) {
                $tile = new Tile(
                    row: $rowKey,
                    column: $columnKey,
                    value: $item,
                    candidates: [],
                );

                $newRow[] = $tile;
            }

            $newRow = new Row(
                blockRow: (int) floor($rowKey / 3),
                blockColumn: (int) floor($rowKey / 3),
                tiles: $newRow,
                uniqueCandidates: [],
            );

            $newGrid[] = $newRow;
        }

        return $newGrid;
    }

    /** @return Collection<int, Tile> */
    protected function emptyTiles(): Collection
    {
        // Map over each row, only returning the empty tiles
        return collect($this->grid)
            ->flatMap(
                fn(Row $row) => array_filter($row->tiles, fn(Tile $tile) => $tile->value === null)
            );
    }

    /** @return array<Row> */
    public function toLivewire(): array
    {
        return $this->grid;
    }

    /** @param array<Row> $value */
    public static function fromLivewire(mixed $value): self
    {
        return new self($value);
    }
}
