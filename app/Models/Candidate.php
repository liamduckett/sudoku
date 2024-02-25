<?php

namespace App\Models;

use Livewire\Wireable;

class Candidate implements Wireable
{
    public function __construct(
        public int $value,
        public bool $unique,
    ) {}

    /** @return array{value: int, unique: bool} */
    public function toLivewire(): array
    {
        return [
            'value' => $this->value,
            'unique' => $this->unique,
        ];
    }

    /** @param array{value: int, unique: bool} $value */
    public static function fromLivewire(mixed $value): Candidate
    {
        return new self(
            $value['value'],
            $value['unique'],
        );
    }
}
