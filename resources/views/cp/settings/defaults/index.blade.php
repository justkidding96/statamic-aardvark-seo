@extends('statamic::layout')

@section('content')
    <ui-header title="{{ $title }}"></ui-header>

    <aardvark-defaults-listing
        request-url="{{ cp_route('aardvark-seo.defaults.index') }}"
        :initial-columns='@json($columns)'
    ></aardvark-defaults-listing>
@stop
