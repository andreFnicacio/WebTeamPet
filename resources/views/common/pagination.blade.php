<div class="search-pagination">
    <ul class="pagination">
        @foreach($pagination['before'] as $before)
            <li>
                <a href="{{ $route }}{!! $before['query'] !!}"> {{$before['number']}} </a>
            </li>
        @endforeach
        <li class="active disabled">
            <a href="#"> {{$pagination['page']}} </a>
        </li>
        @foreach($pagination['after'] as $after)
            <li>
                <a href="{{ $route }}{!! $after['query'] !!}"> {{$after['number']}} </a>
            </li>
        @endforeach
    </ul>
</div>