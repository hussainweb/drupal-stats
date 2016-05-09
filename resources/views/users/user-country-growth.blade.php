@extends('charts.growth-chart')

@section('svgcontent')
    <div class="panel panel-body">
        <h2>Users Growth by Country</h2>
        <p>This chart shows the growth in users on drupal.org since the beginning. There seems to be no data before 2001 where it immediately starts with 2000+ users. The rest of the chart should be fairly accurate.</p>
        <p>This chart is accurate for all the users that are present and not blocked on the system at the time of first complete crawl. This means that the chart does not show users that were created and subsequently deleted/blocked as the API would not return any information about it. This could be a significant number which can be guessed by user id.</p>
    </div>
@endsection
