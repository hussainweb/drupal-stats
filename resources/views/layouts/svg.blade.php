@extends('layouts.master')

@push('scripts')
@endpush

@section('content')
    <div class="content">
        <div class="page-header">
            <h1>{{ $title }}</h1>
        </div>

        @yield('presvgcontent')

        <div class="col-sm-12 svg-container">
        </div>

        @yield('svgcontent')
    </div>
@endsection
