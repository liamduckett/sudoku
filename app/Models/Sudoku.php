<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Livewire\Wireable;

class Sudoku implements Wireable
{
    /** @param array<array<Tile>> $grid */
    public function __construct(public array $grid) {}

    /** @param array<array<?int>> $grid */
    public static function setUp(array $grid): self
    {
        $grid = self::addEmptyMetaData($grid);
        return new self($grid);
    }

    /** @return array<Row> */
    public function rows(): array
    {
        return array_map(
            fn(array $row) => new Row($row),
            $this->grid,
        );
    }

    /** @return array<Column> */
    public function columns(): array
    {
        return array_map(
            fn(Tile $tile) => new Column($this->column($tile)),
            $this->grid[0],
        );
    }

    /** @return array<Block> */
    public function blocks(): array
    {
        // need to get the tiles to map over here?
        // get the first, fourth and seventh Tile
        // from the first fourth and seventh row

        $relevantTiles = [
            $this->grid[0][0],
            $this->grid[3][0],
            $this->grid[6][0],
            $this->grid[0][3],
            $this->grid[3][3],
            $this->grid[6][3],
            $this->grid[0][6],
            $this->grid[3][6],
            $this->grid[6][6],
        ];

        return array_map(
            fn(Tile $tile) => new Block($this->block($tile)),
            $relevantTiles,
        );
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
    public function block(Tile $tile): array
    {
        // maps:
        //  0,1,2 => 0
        //  3,4,5 => 1
        //  6,7,8 => 2
        // TODO: use new blockRow logic on tile
        $blockRow = (int) floor($tile->row / 3);
        $blockColumn = (int) floor($tile->column / 3);

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

    public function checkForSoleCandidates(Tile $tile): void
    {
        $nearby = collect([
            ...$this->row($tile),
            ...$this->column($tile),
            ...$this->block($tile),
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

        $tile->candidates = array_map(
            fn(int $value) => new Candidate($value, unique: false),
            $playable,
        );
    }

    public function checkForUniqueCandidates(Row|Column|Block $area): void
    {
        // get the unique candidates for the passed area
        $emptyAreaTileCandidates = collect($area->tiles)
            ->filter(fn(Tile $tile) => $tile->value === null)
            ->flatMap(fn(Tile $tile) => $tile->candidates)
            ->map(fn (Candidate $candidate) => $candidate->value)
            ->countBy()
            ->toArray();

        $uniqueCandidates = array_keys($emptyAreaTileCandidates, 1);

        // loop over each (empty) tile in the row
        foreach($area->tiles as $tile) {
            //  loop over each candidate in this tile
            foreach($tile->candidates as $candidate) {
                //  if its value is in the unique candidates set it to true
                $candidate->unique = $candidate->unique || in_array($candidate->value, $uniqueCandidates);
            }
        }
    }

    public function checkForBlockInteractions(Block $block): void
    {
        // for each number, check if they appear in a line!

        // 1. get a list of all placed tiles
        $candidates = collect($block->tiles)
            ->flatMap(fn(Tile $tile) => $tile->candidates)
            ->map(fn(Candidate $candidate) => $candidate->value)
            ->unique()
            ->sort();

        // 2. loop over missing values and
        foreach($candidates as $candidate) {
            $appearances = [];

            foreach($block->tiles as $tile) {
                $candidateValues = array_map(
                    fn(Candidate $candidate) => $candidate->value,
                    $tile->candidates,
                );

                if(in_array($candidate, $candidateValues)) {
                    $appearances[] = $tile;
                }
            }

            // 3. check if all appearances are in a line (row / column)
            if($this->tilesAreInSingleRow($appearances)) {
                // 4. if so then, rule it out for the rest of the row
                $this->removeCandidateFromRow($candidate, $appearances[0]);
            }
        }
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

        $emptyTiles = $this->emptyTiles();

        foreach($emptyTiles as $tile) {
            if($tile->hasUniqueCandidate()) {
                // @phpstan-ignore-next-line: doesnt return null due to above check
                $tile->value = $tile->uniqueCandidate()->value;
            }
        }
    }

    public function addMetaData(): void
    {
        $emptyTiles = $this->emptyTiles();

        foreach($emptyTiles as $tile) {
            $this->checkForSoleCandidates($tile);
        }

        foreach($this->rows() as $row) {
            $this->checkForUniqueCandidates($row);
        }

        foreach($this->columns() as $column) {
            $this->checkForUniqueCandidates($column);
        }

        // I don't think it's possible for a candidate to be unique by block alone
        // I think a candidate unique by block, will always also be unique by row OR column
        foreach($this->blocks() as $block) {
            $this->checkForUniqueCandidates($block);
        }

        foreach($this->blocks() as $block) {
            $this->checkForBlockInteractions($block);
        }
    }

    /**
     * @param array<array<?int>> $grid
     * @return array<array<Tile>>
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
                fn(array $row) => array_filter($row, fn(Tile $tile) => $tile->value === null)
            );
    }

    /** @param array<Tile> $tiles */
    protected function tilesAreInSingleRow(array $tiles): bool
    {
        $rows = array_map(
            fn(Tile $tile) => $tile->row,
            $tiles,
        );

        $rows = array_unique($rows);

        return count($rows) === 1;
    }

    /** @param array<Tile> $tiles */
    protected function tilesAreInSingleColumn(array $tiles): bool
    {
        $columns = array_map(
            fn(Tile $tile) => $tile->column,
            $tiles,
        );

        $columns = array_unique($columns);

        return count($columns) === 1;
    }

    /** @return array<array<Tile>> */
    public function toLivewire(): array
    {
        return $this->grid;
    }

    /** @param array<array<Tile>> $value */
    public static function fromLivewire(mixed $value): self
    {
        return new self($value);
    }

    private function removeCandidateFromRow(int $candidateValue, Tile $boss): void
    {
        // 1. get the row for this tile
        $tiles = $this->row($boss);

        // 2. get the tiles excluding this block
        $relevantTiles = array_filter(
            $tiles,
            fn(Tile $tile) => $tile->blockColumn() !== $boss->blockColumn(),
        );

        // 3. loop through them and remove this candidate if applicable
        foreach ($relevantTiles as $tile) {
            $candidates = array_filter(
                $tile->candidates,
                fn(Candidate $candidate) => $candidate->value !== $candidateValue,
            );

            $tile->candidates = array_values($candidates);
        }
    }
}
