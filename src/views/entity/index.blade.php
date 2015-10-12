@extends('lavanda::layout.common')

@section('title')
{{ $title }}
@endsection

@section('content')
<div class="list-panel clearfix">
    <div class="pull-left">{!! $items->render() !!}</div>
    @if($createAllowed)
        <div class="pull-right add-button">
            <a class="btn btn-default" href="{{ $getRoute('create') }}">
              <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Add new
            </a>
        </div>
    @endif
    @if($searchForm)
        <div class="pull-right search-form">{!! $searchForm->renderForm() !!}</div>
    @endif
</div>
<table class="table">
    <thead>
        <tr>
            @foreach($columns as $presentation)
                <th {!! $presentation->getParm('width') ? 'style="width: '.$presentation->getParm('width').';"' : '' !!}>
                    {{ $presentation->getTitle() }}
                </th>
            @endforeach
            <th class="sort" colspan="{{ 1 + $editAllowed + $destroyAllowed }}">
                {!! $sortDescriptor->renderSortSelect() !!}
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                @foreach($columns as $key => $presentation)
                    <td>
                        {!! $presentation->render(data_get($item, $key)) !!}
                    </td>
                @endforeach
                <td class="list-actions">
                    <a data-toggle="tooltip" data-placement="left" title="Show" class="btn btn-default" href="{{ $getRoute('show', ['id' => $item['id']]) }}">
                      <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                    </a>
                </td>
                @if($editAllowed)
                    <td class="list-actions">
                        <a data-toggle="tooltip" data-placement="left" title="Edit" class="btn btn-default" href="{{ $getRoute('edit', ['id' => $item['id']]) }}">
                          <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                        </a>
                    </td>
                @endif
                @if($destroyAllowed)
                    <td class="list-actions">
                        <a data-toggle="tooltip" data-placement="left" title="Delete" class="btn btn-default action-delete" data-token="{{ csrf_token() }}" href="{{ $getRoute('destroy', ['id' => $item['id']]) }}">
                          <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                        </a>
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
@if(!count($items))
    <div>No data to display!</div>
@endif
{!! $items->render() !!}
@endsection