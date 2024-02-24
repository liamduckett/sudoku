<div>
    <style>
        div:nth-child(9n+3),
        div:nth-child(9n+6)
        {
            border-right-color: black;
        }

        div:nth-child(9n+4),
        div:nth-child(9n+7)
        {
            border-left-color: black;
        }

        div:nth-child(n+19):nth-child(-n+27),
        div:nth-child(n+46):nth-child(-n+54)
        {
            border-bottom-color: black;
        }

        div:nth-child(n+28):nth-child(-n+36),
        div:nth-child(n+55):nth-child(-n+63)
        {
            border-top-color: black;
        }
    </style>

    <button wire:click="advance">
        Advance
    </button>

    <div class="w-[40.5rem] h-[40.5rem] mx-auto grid grid-cols-9 grid-rows-9 my-10 border-2 border-black text-2xl">
        @foreach($grid as $rowKey => $row)
            @foreach($row as $columnKey => $item)
                @php
                    $tile = new \App\Models\Tile($rowKey, $columnKey);
                @endphp

                <div class="row-span-1 flex justify-center items-center border-[1px] border-gray-400">

                    @if($item['value'])
                        <div>
                            {{ $item['value'] }}
                        </div>
                    @else
                        <div class="text-sm text-blue-700 font-semibold">
                            {{ implode(', ', $item['meta'] ?? []) }}
                        </div>
                    @endif
                </div>
            @endforeach
        @endforeach
    </div>
</div>
