@extends('layouts.master')

@section('content')
    <div class="content">
        <div class="page-header">
            <h1>{{ $title }}</h1>
        </div>

        <div class="col-sm-12">
            {!! $page_content  !!}
        </div>
    </div>
@endsection
