<h1>Notes</h1>

{{-- dd($notes) --}}
<ul>
    @foreach($notes as $note)
        @if($note['is_dir'])
            <li><a href="/notes{{ $note['path'] }}">{{ $note['path'] }}</a></li>
        @else
            <li>{{ $note['path'] }}</li>
        @endif
    @endforeach
</ul>
