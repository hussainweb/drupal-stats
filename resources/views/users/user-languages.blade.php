@extends('layouts.svg')

@push('styles')
<style>
    .axis line {
        fill: none;
        stroke: black;
    }

    .tick line {
        opacity: 0.2;
    }
</style>
@endpush

@push('scripts')
<script src="//d3js.org/d3.v3.js" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3-legend/1.8.0/d3-legend.js" charset="utf-8"></script>
<script src="{{ asset('js/d3-cloud.js') }}" charset="utf-8"></script>
<script>

    var width = 960,
            height = 500,
            cx = width / 2,
            cy = height / 2;

    var color = d3.scale.category20c();

    d3.json('{{ url('data/user-languages') }}', function (error, data) {
        if (error) {
            throw error;
        }

        var logScale = d3.scale.log()
                .domain([1, d3.max(data, function (d) { return d.count; })])
                .range([8, 60]);

        var words = [];
        d3.layout.cloud()
                .size([width, height])
                .words(data)
                .text(function (d) { return d.language; })
                .font("Impact")
                .fontSize(function (d) { return logScale(d.count); })
                .rotate(function () { return (~~(Math.random() * 6) - 3) * 30; })
                .spiral('rectangular')
                .on('word', function (word) {
                    words.push(word);
                })
                .start();

        var svg = d3.select(".svg-container").append("svg")
                .attr("width", width + margin.left + margin.right)
                .attr("height", height + margin.top + margin.bottom)
                .append("g")
                .attr("transform", "translate(" + cx + "," + cy + ")");

        var text = svg.selectAll('text')
                .data(words)
                .enter().append('text')
                .style('font-family', "Impact")
                .style('font-size', '1px')
                .style('fill', function (d, i) { return color(i); })
                .attr('text-anchor', 'middle')
                .attr('transform', function (d) {
                    return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
                })
                .text(function (d) { return d.language; });

        text.transition()
                .delay(function (d, i) { return i * 10; })
                .style('font-size', function (d) { return d.size + "px"; });

        text.append('title')
                .text(function (d) { return d.language + ": " + d.count; });
    });

</script>
@endpush

@section('presvgcontent')
    {{--<div class="col-sm-12">--}}
        {{--<label><input type="radio" name="mode" value="grouped"> Grouped</label>--}}
        {{--<label><input type="radio" name="mode" value="stacked" checked> Stacked</label>--}}
        {{--<button class="btn btn-warning" onclick="sendRefresh();">Refresh Data</button>--}}
    {{--</div>--}}
@endsection

@section('svgcontent')
    <div class="panel panel-body">
        <h2>Spoken Languages</h2>
        <p>This chart shows all the CI jobs that have been running on drupal.org modern test infrastructure. The branch field has been introduced recently and hence most of the results fall under an empty branch.</p>
        <p>The graph is on an exaggerated exponential scale to show smaller values. Since only few of these jobs can be running at any time, the results are very skewed towards completed and error test runs which makes it necessary for such an exaggerated scale.</p>
    </div>
@endsection
