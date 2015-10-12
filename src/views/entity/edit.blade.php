@extends('lavanda::layout.common')

@section('title')
{{ $title }}
@endsection

@section('content')
{!! $form->renderForm() !!}
@endsection