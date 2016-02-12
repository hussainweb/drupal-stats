@extends('layouts.svg')

@push('scripts')
    <script src="//d3js.org/d3.v3.js" charset="utf-8"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3-legend/1.8.0/d3-legend.js" charset="utf-8"></script>
    <script>
        var diameter = 760,
            legendWidth = 200,
            format = d3.format(",d"),
            color = d3.scale.ordinal().range(["#d9d9d9", "#11fd84", "#ffce9f", "#fec4ff", "#b7ffc0", "#8bfe01", "#e4f7fd", "#dade67", "#acfe70", "#0bffd9", "#ffd32f", "#fecad0", "#07ff49", "#fdfeb5", "#96e6d6", "#b7e815", "#60f1a0", "#cfdcba", "#fed173", "#94e2fe", "#d0d4ff", "#b1e783", "#f1fd59", "#68f35a", "#62fff2", "#7aecc3", "#7ffd88", "#ffe3ce", "#bee655", "#c3e19b", "#94ee3a", "#e6dc18", "#ffe7fd", "#ceffdd", "#e8fd98", "#feea5c", "#ecd68f"]);

        var bubble = d3.layout.pack()
                .sort(null)
                .size([diameter, diameter])
                .padding(1.5);

        var svgRoot = d3.select('.svg-container').append('svg')
                .attr('width', diameter + legendWidth)
                .attr('height', diameter)
                .attr('class', 'bubble');

        svg = svgRoot.append('g').attr('transform', 'translate(' + legendWidth + ', 0)');

        var legendOrdinal = d3.legend.color()
                //d3 symbol creates a path-string, for example
                //"M0,-8.059274488676564L9.306048591020996,
                //8.059274488676564 -9.306048591020996,8.059274488676564Z"
                .shape("path", d3.svg.symbol().type("circle").size(100)())
                .shapePadding(10)
                .scale(color);

        function graph(order) {
            svg.selectAll('.node').remove();
            d3.json('{{ url('data/module-downloads') }}', function (error, data) {
                if (order != 'random') {
                    data.sort(function(a, b) {
                        return parseFloat(b.value) - parseFloat(a.value);
                    });
                }

                var node = svg.selectAll(".node")
                        .data(bubble.nodes({ children: data })
                                .filter(function (d) { return !d.children; }))
                        .enter().append("g")
                        .attr("class", "node")
                        .attr("transform", function (d) { return "translate(" + d.x + "," + d.y + ")"; });

                node.append("title")
                        .text(function (d) { return d.title + ": " + format(d.value) + "\nCategory: " + d.category; });

                node.append("circle")
                        .attr("r", 0)
                        .attr("fill", "#ffffff")
                        .transition()
                        .duration(function (d, i) { return i * 20; })
                        .attr("r", function (d) { return d.r; })
                        .style("fill", function (d) { return color(d.category); });

                node.append("text")
                        .attr("dy", ".3em")
                        .style("text-anchor", "middle")
                        .style("font-size", function (d) { return (d.r / 3) + "px"; })
                        .text(function (d) { return d.r > 12 ? d.machine_name.substring(0, d.r / 3) : ''; });

                svgRoot.append("g")
                        .attr("class", "legendOrdinal")
                        .attr("transform", "translate(20,20)");

                svgRoot.select(".legendOrdinal")
                        .call(legendOrdinal);

            });
        }
        graph('ordered');
    </script>
@endpush

@section('svgcontent')
    <div class="col-sm-12">
        <fieldset>
            <button class="btn btn-default" onclick="graph('random')">Randomize</button>
            <button class="btn btn-success" onclick="graph()">Reset</button>
        </fieldset>
    </div>
    <div class="panel panel-body">
        <h2>Module Downloads Bubble Chart</h2>
        <p>This chart shows the top 200 downloaded modules from drupal.org. The color indicates the category and the size of the bubble indicates the number of downloads.</p>
    </div>
@endsection
