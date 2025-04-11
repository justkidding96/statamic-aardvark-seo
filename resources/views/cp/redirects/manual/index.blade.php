@extends('statamic::layout')

@section('content')
    <breadcrumbs :crumbs='@json($crumbs)'></breadcrumbs>

    <div class="flex items-center mb-6">
        <h1 class="flex-1">{{ $title }}</h1>
        <div>
            <a href="{{ cp_route('aardvark-seo.redirects.manual-redirects.create') }}" class="btn-primary">{{ __('aardvark-seo::redirects.actions.create') }}</a>
        </div>
    </div>


    <aardvark-manual-redirects-listing
        :initial-columns='@json($columns)'
        :initial-items='@json($items)'
        site="default"
        create-url='{{ cp_route('aardvark-seo.redirects.manual-redirects.create') }}'
        bulk-actions-url='{{ cp_route('aardvark-seo.redirects.manual-redirects.actions') }}'
        request-url='{{ cp_route('aardvark-seo.redirects.manual-redirects.index') }}'
    ></aardvark-manual-redirects-listing>
@stop
