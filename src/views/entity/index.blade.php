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
              <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> {{ trans('lavanda::common.add_item') }}
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
            <?php $cnt = 0; ?>
            @foreach($columns as $presentation)
                <th class="{{ $cnt === 3 ? 'hidden-xs' : '' }} {{ $cnt > 3 ? 'hidden-xs hidden-sm' : '' }}" {!! $presentation->getParm('width') ? 'style="width: '.$presentation->getParm('width').';"' : '' !!}>
                    {{ $presentation->getTitle() }}
                </th>
                <?php $cnt++; ?>
            @endforeach
            <th class="sort">
                {!! $sortDescriptor->renderSortSelect() !!}
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                <?php $cnt = 0; ?>
                @foreach($columns as $key => $presentation)
                    <td class="{{ $cnt === 3 ? 'hidden-xs' : '' }} {{ $cnt > 3 ? 'hidden-xs hidden-sm' : '' }}">
                        {!! $presentation->render(data_get($item, $key)) !!}
                    </td>
                    <?php $cnt++; ?>
                @endforeach
                <td class="list-actions">
                    <a data-toggle="tooltip" data-placement="left" title="{{ trans('lavanda::common.show_item') }}" class="btn btn-default" href="{{ $getRoute('show', ['id' => $item['id']]) }}">
                      <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                    </a>
                @if($editAllowed)
                    <a data-toggle="tooltip" data-placement="left" title="{{ trans('lavanda::common.edit_item') }}" class="hidden-xs btn btn-default" href="{{ $getRoute('edit', ['id' => $item['id']]) }}">
                      <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                    </a>
                @endif
                @if($destroyAllowed)
                    <a data-toggle="tooltip" data-placement="left" title="{{ trans('lavanda::common.delete_item') }}" class="hidden-xs btn btn-default action-delete" data-token="{{ csrf_token() }}" href="{{ $getRoute('destroy', ['id' => $item['id']]) }}">
                      <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                    </a>
                @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@if(!count($items))
    <div>{{ trans('lavanda::common.no_data') }}</div>
@endif
{!! $items->render() !!}
@endsection