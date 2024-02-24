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

    <div class="w-[40.5rem] mx-auto mt-10">
        <x-button wire:click="advance">
            Advance
        </x-button>

        <div class="h-[40.5rem] grid grid-cols-9 grid-rows-9 my-10 border-2 border-black text-2xl">
            @foreach($grid as $rowKey => $row)
                @foreach($row as $columnKey => $item)
                    <div class="row-span-1 flex justify-center items-center border-[1px] border-gray-400">

                        @if($item->value)
                            <div>
                                {{ $item->value }}
                            </div>
                        @else
                            @php
                                $solo = count($item->meta) === 1;
                            @endphp

                            <div class="text-sm {{ $solo ? 'text-red-700' : 'text-blue-700' }} font-semibold">
                                {{ implode(', ', $item->meta) }}
                            </div>
                        @endif
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
</div>
