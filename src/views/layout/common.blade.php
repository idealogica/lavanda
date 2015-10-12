<!DOCTYPE html>
<html>
    <head>
        <base href="{{ asset('/') }}">
        <title>@yield('title')</title>
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="vendor/lavanda/lavanda.css">
        <script src="https://code.jquery.com/jquery-2.1.4.min.js" type="text/javascript"></script>
        <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js" type="text/javascript"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js" type="text/javascript"></script>
        <script src="vendor/lavanda/lavanda.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="container">
            <nav class="navbar navbar-default">
                <img class="lavanda" src="vendor/lavanda/lavanda.png" alt="">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="{{ url('admin') }}">
                        {{ getSiteName() }}
                        <div>
                            {{ trans('lavanda::common.brand_cp') }}
                        </div>
                    </a>
                </div>
                <div class="collapse navbar-collapse" id="navbar">
                    <ul class="nav navbar-nav">
                        @foreach($menu as $item)
                        <li {!! !empty($item['selected']) ? 'class="active"' : '' !!}>
                            <a href="{{ $item['url'] }}">
                                {{ $item['title'] }} <sup>({{ $item['count'] }})</sup>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </nav>
            <h1>@yield('title')</h1>
            @if(session('msg'))
                <div class="alert alert-{{ session('msg-type') ?: 'success' }}">
                    {{ session('msg') }}
                </div>
            @endif
            @yield('content')
        </div>
    </body>
</html>