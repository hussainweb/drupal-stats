@extends('layouts.svg')

@push('scripts')
<script src="//d3js.org/d3.v3.js" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3-legend/1.8.0/d3-legend.js" charset="utf-8"></script>
<script>

    var margin = {top: 40, right: 50, bottom: 20, left: 50},
            width = 960 - margin.left - margin.right,
            height = 500 - margin.top - margin.bottom;

    var color = d3.scale.category20();

    var legendOrdinal = d3.legend.color()
            //d3 symbol creates a path-string, for example
            //"M0,-8.059274488676564L9.306048591020996,
            //8.059274488676564 -9.306048591020996,8.059274488676564Z"
            .shape("path", d3.svg.symbol().type("circle").size(100)())
            .shapePadding(10)
            .scale(color);

    d3.json('/data/ci-jobs', function (error, data) {
        if (error) {
            throw error;
        }

        var xScale = d3.scale.ordinal()
                .domain(data.branches)
                .rangeRoundBands([0, width], .08);

        var stack = d3.layout.stack()
                .x(function (d) { return xScale(d.branch); })
                .y(function (d) { return d.count; })
                .out(function (d, y0, y) {
                    d.x = xScale(d.branch);
                    d.y = y;
                    d.y0 = y0;
                });

        var layers = stack(data.data),
                yGroupMax = d3.max(layers, function(layer) { return d3.max(layer, function (d) { return d.y; }); }),
                yStackMax = d3.max(layers, function(layer) { return d3.max(layer, function (d) { return d.y0 + d.y; }); });

        var yScale = d3.scale.pow().exponent(0.2)
                .domain([0, yStackMax])
                .range([height, 0]);

        var xAxis = d3.svg.axis()
                .scale(xScale)
                .tickSize(0)
                .tickPadding(6)
                .orient("bottom");

        var yAxis = d3.svg.axis()
                .scale(yScale)
                .tickSize(0)
                .tickPadding(6)
                .orient("left")
                .ticks(8, ',f');

        var svg = d3.select(".svg-container").append("svg")
                .attr("width", width + margin.left + margin.right)
                .attr("height", height + margin.top + margin.bottom)
                .append("g")
                .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

        var layer = svg.selectAll(".layer")
                .data(layers)
                .enter().append("g")
                .attr("class", "layer")
                .style("fill", function (d, i) { return color(d[i].status); });

        var rect = layer.selectAll("rect")
                .data(function (d) { return d; })
                .enter().append("rect")
                .attr("x", function (d) { return d.x; })
                .attr("y", height)
                .attr("width", xScale.rangeBand())
                .attr("height", 0);

        rect.append('title')
                .text(function (d) { return d.status + ": " + d.count; });

        rect.transition()
                .delay(function (d, i) { return i * 10; })
                .attr("y", function (d) { return yScale(d.y0 + d.y); })
                .attr("height", function (d) { return yScale(d.y0) - yScale(d.y0 + d.y); });

        svg.append("g")
                .attr("class", "x axis")
                .attr("transform", "translate(0," + height + ")")
                .call(xAxis)
                .append('rect').attr('x', 0).attr('y', 0).attr('width', width).attr('height', 2);

        svg.append("g")
                .attr("class", "y axis")
                .attr("transform", "translate(0, 0)")
                .call(yAxis)
                .append('rect').attr('x', 0).attr('y', 0).attr('width', 2).attr('height', height);

        d3.selectAll("input").on("change", change);

        var timeout = setTimeout(function() {
            d3.select("input[value=\"grouped\"]").property("checked", true).each(change);
        }, 2000);

        function change() {
            clearTimeout(timeout);
            if (this.value === "grouped") transitionGrouped();
            else transitionStacked();
        }

        function transitionGrouped() {
            yScale.domain([0, yGroupMax]);

            rect.transition()
                    .duration(500)
                    .delay(function (d, i) { return i * 10; })
                    .attr("x", function (d, i, j) { return d.x + xScale.rangeBand() / data.status.length * j; })
                    .attr("width", xScale.rangeBand() / data.status.length)
                    .transition()
                    .attr("y", function (d) { return yScale(d.y); })
                    .attr("height", function (d) { return height - yScale(d.y); });
        }

        function transitionStacked() {
            yScale.domain([0, yStackMax]);

            rect.transition()
                    .duration(500)
                    .delay(function (d, i) { return i * 10; })
                    .attr("y", function (d) { return yScale(d.y0 + d.y); })
                    .attr("height", function (d) { return yScale(d.y0) - yScale(d.y0 + d.y); })
                    .transition()
                    .attr("x", function (d) { return d.x; })
                    .attr("width", xScale.rangeBand());
        }

        svg.append("g")
                .attr("class", "legendOrdinal")
                .attr("transform", "translate(" + (width - margin.right) + ",20)");

        svg.select(".legendOrdinal")
                .call(legendOrdinal);
    });

</script>
@endpush

@section('presvgcontent')
    <div class="col-sm-12">
        <form>
            <fieldset>
                <label><input type="radio" name="mode" value="grouped"> Grouped</label>
                <label><input type="radio" name="mode" value="stacked" checked> Stacked</label>
                <button class="btn btn-warning" onclick="sendRefresh();">Refresh Data</button>
            </fieldset>
        </form>
    </div>
@endsection

@section('svgcontent')
    <div class="panel panel-body">
        <h2>Overall CI jobs statistics</h2>
        <p>This chart shows all the CI jobs that have been running on drupal.org modern test infrastructure. The branch field hss been introduced recently and hence most of the results fall under an empty branch.</p>
        <p>The graph is on an exaggerated exponential scale to show smaller values. Since only few of these jobs can be running at any time, the results are very skewed towards completed and error test runs which makes it necessary for such an exaggerated scale.</p>
    </div>
@endsection
