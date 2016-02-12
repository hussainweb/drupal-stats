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
<script src="{{ asset('js/d3-cloud.js') }}" charset="utf-8"></script>
<script>

    var width = 960,
            height = 500,
            cx = width / 2,
            cy = height / 2;

    var color = d3.scale.category20c();

    d3.json('{{ url('data/user-expertise') }}', function (error, data) {
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
                .text(function (d) { return d.expertise; })
                .font("Impact")
                .fontSize(function (d) { return logScale(d.count); })
                .rotate(function () { return (~~(Math.random() * 6) - 3) * 30; })
                .spiral('rectangular')
                .on('word', function (word) {
                    words.push(word);
                })
                .start();

        var svg = d3.select(".svg-container").append("svg")
                .attr("width", width)
                .attr("height", height)
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
                .text(function (d) { return d.expertise; });

        text.transition()
                .delay(function (d, i) { return i * 10; })
                .style('font-size', function (d) { return d.size + "px"; });

        text.append('title')
                .text(function (d) { return d.expertise + ": " + d.count; });
    });

</script>
@endpush

@section('svgcontent')
    <div class="panel panel-body">
        <h2>Areas of Expertise</h2>
        <p>This word cloud shows various areas of expertise of registered Drupal users all over the world. This information is taken and aggregated from all user profiles on d.o.</p>
        <p>The size of the word is proportional (on a logarithmic scale) to number of users who have marked it as their expertise. Hover over the word to see the count of users with that expertise. Since this is a browser based visualization, you may use the browser's find feature to find your favourite expertise.</p>
    </div>
@endsection
