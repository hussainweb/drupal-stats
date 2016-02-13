@extends('layouts.svg')

@push('styles')
<style>

    .country {
        stroke-width: 0.5px;
        stroke-linejoin: round;
    }

    .country:hover {
        stroke: #000;
        stroke-width: 3px;
    }

    .graticule {
        fill: none;
        stroke: #000;
        stroke-opacity: .3;
        stroke-width: .5px;
    }

    .graticule.outline {
        stroke: #333;
        stroke-opacity: 1;
        stroke-width: 1.5px;
    }

</style>
@endpush

@push('scripts')
<script src="//d3js.org/d3.v3.js" charset="utf-8"></script>
<script src="//d3js.org/d3.geo.projection.v0.min.js"></script>
<script>

    var width = 960,
            height = 600;

    var color = d3.scale.log()
            .range(['#ffffff', '#0678be']);

    var projection = d3.geo.kavrayskiy7().scale(175),
            graticule = d3.geo.graticule();

    var path = d3.geo.path()
            .projection(projection);

    var svgRoot = d3.select('.svg-container').append('svg')
            .attr('width', width)
            .attr('height', height)
            .attr('class', 'world');

    var svg = svgRoot.append("g")
            .attr('transform', 'translate(0,30)');

    svg.append("path")
            .datum(graticule)
            .attr("class", "graticule")
            .attr("d", path);

    svg.append("path")
            .datum(graticule.outline)
            .attr("class", "graticule outline")
            .attr("d", path);

    d3.json('{{ asset('geojson/countries.geo.json') }}', function (error, world) {
        if (error) {
            throw error;
        }

        svg.append("path")
                .datum(world)
                .attr("d", path)
                .attr("fill", "#ffffff")
                .attr("stroke", "#000000");

        var overlay = svg.append("g")
                .attr("transform", "translate(0,0)");

        var features = [];
        world.features.forEach(function (feature) {
            features[feature.id] = feature;
        });

        d3.json('{{ url('data/user-countries') }}', function (error, data) {
            color.domain([1, d3.max(data, function (d) { return d.count; })]);
            var format = d3.format(",2d");
            var country = overlay.selectAll("path")
                    .data(data)
                    .enter().append("path")
                    .attr("d", function (d) {
                        if (d.country == 'BMU') { return null; }
                        return path(features[d.country]);
                    })
                    .attr("fill", function (d) { return color(d.count); })
                    .attr("class", function (d) { return "country " + d.country; });

            country.append("title")
                    .text(function (d) { return d.countryName + ": " + format(d.count); });
        });
    });

</script>
@endpush

@section('svgcontent')
    <div class="panel panel-body">
        <h2>Drupalers around the world</h2>
        <p>These are all the Drupalers around the world. Darker shade of blue indicates more Drupalers in that country. This information is taken and aggregated from all user profiles on d.o.</p>
    </div>
@endsection
