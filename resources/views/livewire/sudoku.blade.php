<div class="">
    @foreach($grid as $row)
        <div class="">
            @foreach($row as $item)
                <div>{{ $item ?? 'Empty' }}</div>
            @endforeach
        </div>
    @endforeach
</div>
