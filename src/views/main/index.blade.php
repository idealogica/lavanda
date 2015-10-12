@extends('lavanda::layout.common')

@section('title')
{{ $title }}
@endsection

@section('content')
        <div class="row tiles">
        @foreach($tiles as $item)
            <div class="col-sm-{{ $item['columns'] }}">
                <a href="{{ $item['url'] }}">
                <div class="tile" style="background: {{ $item['color'] }};">
                    {{ $item['title'] }}
                    <div class="tile-badge-container">
                        <div>
                            <span class="badge">{{ $item['count'] }}</span>
                        </div>
                    </div>
                </div>
                </a>
            </div>
        @endforeach
        </div>
@endsection