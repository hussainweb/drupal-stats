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

    .value-legend rect {
        fill: #ffffff;
        opacity: 0.6;
        stroke-width: 1px;
        stroke: #333333;
        border-radius: 5px;
    }

    .value-legend text {
        font-family: sans-serif;
        font-size: 16px;
        font-weight: bold;
    }

    .value-legend-circle {
        fill: #ffffff;
        stroke-width: 2px;
        stroke: #000000;
        opacity: 0.6;
    }

    rect.pane {
        cursor: move;
        fill: none;
        pointer-events: all;
    }
</style>
@endpush

@push('scripts')
<script src="//d3js.org/d3.v3.js" charset="utf-8"></script>
<script>

    var raw_data;
    var margin = {top: 20, right: 20, bottom: 30, left: 50},
            width = 960 - margin.left - margin.right,
            height = 500 - margin.top - margin.bottom;

    var dataTypes = {!! json_encode(array_keys($data_types)) !!};
    var keys = {!! json_encode($default_data_types) !!};
    var dateFormat = d3.time.format("%Y-%m-%d"),
            longDateFormat = d3.time.format("%e %b, %Y"),
            numberFormat = d3.format("{{ $y_number_format or ",2d" }}");
    var xScale = d3.time.scale()
            .range([0, width]);
    var yScale = d3.scale.linear()
            .range([height, 0]);
    var color = d3.scale.category10()
            .domain(dataTypes);
    var textMap = d3.scale.ordinal()
            .domain(dataTypes)
            .range({!! json_encode(array_values($data_types)) !!});

    var xAxis = d3.svg.axis()
            .scale(xScale)
            .innerTickSize(-height)
            .outerTickSize(0)
            .tickPadding(6)
            .orient("bottom");
    var yAxis = d3.svg.axis()
            .scale(yScale)
            .innerTickSize(-width)
            .outerTickSize(0)
            .tickPadding(6)
            .orient("left")
            .tickFormat(numberFormat);

    var area = d3.svg.area()
            .x(function(d) { return xScale(d.date); })
            .y0(function(d) { return yScale(d.y0); })
            .y1(function(d) { return yScale(d.y0 + d.y); });

    var stack = d3.layout.stack()
            .values(function(d) {
                return d.values;
            });

    var svg = d3.select(".svg-container").append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    var zoom = d3.behavior.zoom()
            .on("zoom", draw);

    d3.json('{{ $data_url }}', function (error, data) {
        if (error) {
            throw error;
        }

        raw_data = data;
        drawAreaChart();
    });

    var drawAreaChart = function () {
        svg.selectAll("*").remove();

        // We need this rectangle to receive mouse events from anywhere in the group.
        svg.append("rect").attr("x", 0).attr("y", 0).attr("width", width).attr("height", height)
                .attr("fill", "transparent");

        // Sort all the dates in different data types and combine.
        var dates = [];
        keys.map(function (name) {
            Object.keys(raw_data[name]).forEach(function (date) {
                dates[date] = 1;
            });
        });
        dates = Object.keys(dates);
        dates.sort();

        // Fill all the values with missing dates.
        var sum = 0;
        var graph_data = [];
        keys.map(function (name) {
            var lastcount = 0;
            var entry = [];

            entry.name = name;
            entry.values = [];
            dates.map(function (date) {
                if (raw_data[name].hasOwnProperty(date)) {
                    lastcount = raw_data[name][date];
                }

                entry.values.push({
                    date: dateFormat.parse(date),
                    y: lastcount
                });
            });

            graph_data.push(entry);
            sum += lastcount;
        });

        var data = stack(graph_data);

        xScale.domain(d3.extent(dates, function (d) { return dateFormat.parse(d); }));
        yScale.domain([0, sum]);
        zoom.x(xScale);

        svg.append("g")
                .attr("class", "x axis")
                .attr("transform", "translate(0," + height + ")")
                .append('rect').attr('x', 0).attr('y', 0).attr('width', width).attr('height', 2);

        svg.append("g")
                .attr("class", "y axis")
                .append('rect').attr('x', 0).attr('y', 0).attr('width', 2).attr('height', height);

        var data_point = svg.selectAll(".point")
                .data(data)
                .enter().append("g")
                .attr("class", "point");

        data_point.append("path")
                .attr("class", function (d) { return d.name + " area"; })
                .style("fill", function(d) { return color(d.name); });

        // Show the arc line.
        svg.append('rect')
                .attr("class", "value-selector")
                .attr("x", 0).attr("y", 0).attr("width", 1).attr("height", height);

        var values_legend = svg.append('g')
                .attr("class", "value-legend")
                .attr("transform", "translate(20, 20)");
        values_legend.append("rect")
                .attr("x", 0).attr("y", 0).attr("width", 200).attr("height", function () { return keys.length * 20 + 50; });
        values_legend.append("text")
                .attr("class", "date")
                .attr("x", 10)
                .attr("y", 20);
        values_legend.selectAll(".value-legend-text")
                .data(keys)
                .enter().append("text")
                .attr("class", function (name) { return name + " value-legend-text"; })
                .attr("x", 10)
                .attr("y", function (d, i) {
                    return (i + 2) * 20 + 10;
                })
                .style("fill", function (name) {
                    return color(name);
                })
                .text(function (d) { return d.text; });
        svg.selectAll(".value-legend-circle")
                .data(keys)
                .enter().append("circle")
                .attr("class", function (name) { return name + " value-legend-circle"; });

        svg.append("rect")
                .attr("class", "pane")
                .attr("width", width)
                .attr("height", height)
                .call(zoom);

        draw();

        svg.on('mousemove', function () {
            var pos = d3.mouse(svg.node()),
                    x = pos[0], y = pos[1],
                    date = xScale.invert(x),
                    dateFormatted = dateFormat(date);

            d3.select(".value-selector").attr("x", x);
            d3.select(".value-legend .date").text("Date: " + longDateFormat(date));

            var value0 = 0;
            keys.map(function (name) {
                var value = 0, check_date = new Date(date.getTime()), check_date_formatted = dateFormatted;

                if (raw_data[name].hasOwnProperty(check_date_formatted)) {
                    value = raw_data[name][check_date_formatted];
                }
                else {
                    // We don't have the value we want for this day. Go back in the
                    // array to see if we can find one.
                    var selected_date_formatted = check_date_formatted;
                    while (check_date.getFullYear() > 2000) {
                        check_date.setDate(check_date.getDate() - 1);
                        check_date_formatted = dateFormat(check_date);
                        if (raw_data[name].hasOwnProperty(check_date_formatted)) {
                            value = raw_data[name][check_date_formatted];
                            break;
                        }
                    }
                    // Save it so that the next lookup is marginally faster.
                    raw_data[name][selected_date_formatted] = value;
                }
                value0 += value;
                d3.select(".value-legend-text." + name).text(textMap(name) + ": " + numberFormat(value));
                d3.select(".value-legend-circle." + name)
                        .attr("cx", x)
                        .attr("cy", yScale(value0))
                        .attr("r", 3);
            });
        });
    };

    function draw() {
        svg.select("g.x.axis").call(xAxis);
        svg.select("g.y.axis").call(yAxis);

        // TODO: Make this more robust.
        keys.map(function (name) {
            svg.select("path.area." + name).attr("d", stack).attr("d", function(d) { return area(d.values); })
        });
    }

    function selectDataTypes() {
        keys.length = 0;
        var input = d3.selectAll("input.data-type:checked");
        // The d3 select returns a nested array.
        input[0].forEach(function (elem) {
            keys.push(elem.getAttribute('name'));
        });
        drawAreaChart();
    }

</script>
@endpush

@section('presvgcontent')
    @if (!empty($show_filter))
        <div class="col-sm-12">
            @foreach ($data_types as $data_label => $data_type)
                <label><input class="data-type" onclick="selectDataTypes()" type="checkbox" name="{{ $data_label }}" checked> {{ $data_type }}</label>
            @endforeach
        </div>
    @endif
@endsection
