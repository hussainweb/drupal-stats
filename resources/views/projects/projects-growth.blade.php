@extends('charts.growth-chart')

@section('svgcontent')
    <div class="panel panel-body">
        <h2>Projects Growth</h2>
        <p>This chart shows the growth in projects (modules, themes, etc...) on drupal.org since the beginning. Number of projects of types like theme engines and core is too small to be seen when compared to a large number of regular themes and modules. To see them better, disable 'Modules' and/or 'Themes' using the checkboxes above.</p>
        <p>This chart is accurate for all the nodes that are present on the system at the time of first complete crawl. This means that the chart does not show projects that were created and subsequently deleted as the API would not return any information about it. This is not significant as the projects are probably never deleted unless it is spam or other exceptional circumstances.</p>
    </div>
@endsection
