@extends('statamic::layout')

@section('content')
    <ui-header title="{{ $title }}">
        <a href="{{ cp_route('aardvark-seo.redirects.export') }}" class="btn">{{ __('Export CSV') }}</a>
        <ui-button @click="$refs.importInput.click()" text="{{ __('Import CSV') }}"></ui-button>
        <ui-button href="{{ cp_route('aardvark-seo.redirects.create') }}" variant="primary" text="{{ __('aardvark-seo::redirects.actions.create') }}"></ui-button>
    </ui-header>

    <form method="POST" action="{{ cp_route('aardvark-seo.redirects.import') }}" enctype="multipart/form-data" style="display: none;">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <input ref="importInput" type="file" name="file" accept=".csv" @change="$event.target.form.submit()" />
    </form>

    @if(session('success'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-950 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-200">
            {{ session('error') }}
        </div>
    @endif

    <aardvark-redirects-listing
        request-url="{{ cp_route('aardvark-seo.redirects.index') }}"
        :initial-columns='@json($columns)'
    ></aardvark-redirects-listing>
@stop
