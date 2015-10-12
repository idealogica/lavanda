@extends('lavanda::layout.common')

@section('title')
{{ $title }}
@endsection

@section('content')
<table class="table">
    <tbody>
        @foreach($rows as $key => $presentation)
            <tr>
                <th>{{ $presentation->getTitle() }}</th>
                <td>{!! $presentation->render(data_get($item, $key)) !!}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="item-actions">
    <a class="btn btn-default pull-left" href="{{ $getRoute('edit', $item['id']) }}" role="button">
        <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
    </a>
    <a class="btn btn-default pull-left action-delete" data-token="{{ csrf_token() }}" href="{{ $getRoute('destroy', $item['id']) }}" role="button">
        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Delete
    </a>
</div>
@endsection