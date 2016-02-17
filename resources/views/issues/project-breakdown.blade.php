@extends('layouts.svg')

@push('styles')
<style>
    .arc {
        stroke: #dddddd;
        stroke-width: 1px;
    }

    .arc:hover {
        stroke: #ffffff;
        stroke-width: 2px;
    }
    .project-name {
        font-family: 'Maven Pro';
        font-weight: 700;
        font-size: 24px;
        fill: #0678be;
    }

    .issue-tooltip rect {
        fill: #ffffff;
        opacity: 0.95;
        stroke-width: 1px;
        stroke: #333333;
        border-radius: 5px;
    }

    .issue-tooltip text {
        font-family: 'Maven Pro';
        font-weight: 700;
    }

    .issue-tooltip .issue-type-name {
        fill: #006600;
        font-size: 18px;
    }
</style>
@endpush

@push('scripts')
<script src="//d3js.org/d3.v3.js" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3-legend/1.8.0/d3-legend.js" charset="utf-8"></script>
<script>

    var margin = {top: 20, right: 200, bottom: 20, left: 20},
            width = 960 - margin.left - margin.right,
            height = 700 - margin.top - margin.bottom,
            radius = Math.min(width, height) / 2;

    var colorCategory = d3.scale.category10();
    var colorPriority = d3.scale.ordinal()
            .domain(['Minor', 'Normal', 'Major', 'Critical'])
            .range(['#d0d0d0', '#80d080', '#ff8080', '#b00000']);
    var colorStatus = d3.scale.category20b();

    var arcCategory = d3.svg.arc()
            .outerRadius(radius / 2 - 3)
            .innerRadius(radius / 4);
    var arcPriority = d3.svg.arc()
            .outerRadius(radius / 1.35 - 3)
            .innerRadius(radius / 2);
    var arcStatus = d3.svg.arc()
            .outerRadius(radius)
            .innerRadius(radius / 1.35);

    var labelArc = d3.svg.arc()
            .outerRadius(radius - 40)
            .innerRadius(radius - 40);

    var pie = d3.layout.pie()
            .sort(null)
            .value(function(d) { return d.count; });

    var svg = d3.select(".svg-container").append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

    var legendGroup = svg.append("g");
    var pieGroup = svg.append("g")
            .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

    var legendCategory = d3.legend.color()
            .shape("path", d3.svg.symbol().type("circle").size(100)())
            .shapePadding(10)
            .scale(colorCategory);
    var legendPriority = d3.legend.color()
            .shape("path", d3.svg.symbol().type("circle").size(100)())
            .shapePadding(10)
            .scale(colorPriority);
    var legendStatus = d3.legend.color()
            .shape("path", d3.svg.symbol().type("circle").size(100)())
            .shapePadding(10)
            .scale(colorStatus);
    var format = d3.format(",2d");

    d3.json('{{ url('data/issues/' . $project_name) }}', function (error, data) {
        if (error) {
            if (error.status == 404) {
                alert('The project was not found');
                window.location = '{{ url('viz/issues') }}';
            }
            throw error;
        }

        var projectName = '{{ $project_name }}';
        if (projectName == '') {
            projectName = 'All Projects';
        }

        pieGroup.append("text")
                .text(projectName)
                .attr('y', '-20')
                .attr("class", "project-name")
                .attr("text-anchor", "middle");

        pieGroup.append("text")
                .text(format(data.totalIssues) + " issues")
                .attr('y', '20')
                .attr("class", "project-issue-count")
                .attr("text-anchor", "middle");

        // Draw each pie donut.
        var gCategory = pieGroup.selectAll(".arc.category")
                .data(pie(data.category))
                .enter().append("g")
                .attr("class", "arc category");
        gCategory.append("path")
                .attr("d", arcCategory)
                .style("fill", function(d) { return colorCategory(d.data.text); });

        var gPriority = pieGroup.selectAll(".arc.priority")
                .data(pie(data.priority))
                .enter().append("g")
                .attr("class", "arc priority");
        gPriority.append("path")
                .attr("d", arcPriority)
                .style("fill", function(d) { return colorPriority(d.data.text); });

        var gStatus = pieGroup.selectAll(".arc.status")
                .data(pie(data.status))
                .enter().append("g")
                .attr("class", "arc status");
        gStatus.append("path")
                .attr("d", arcStatus)
                .style("fill", function(d) { return colorStatus(d.data.text); });


        // Draw each legend.
        legendGroup.append("g")
                .attr("class", "legendCategory")
                .attr("transform", "translate(" + (width + 20) + ",20)");
        legendGroup.select(".legendCategory")
                .call(legendCategory);

        legendGroup.append("g")
                .attr("class", "legendPriority")
                .attr("transform", "translate(" + (width + 20) + ",200)");
        legendGroup.select(".legendPriority")
                .call(legendPriority);

        legendGroup.append("g")
                .attr("class", "legendStatus")
                .attr("transform", "translate(" + (width + 20) + ",350)");
        legendGroup.select(".legendStatus")
                .call(legendStatus);

        // Draw the tooltip
        var tooltip = svg.append('g').attr('class', 'issue-tooltip');
        tooltip.append('rect');
        tooltip.append('text').attr('class', 'issue-type-name').attr('x', 10).attr('y', 25);
        tooltip.append('text').attr('class', 'issue-count').attr('x', 10).attr('y', 50);

        d3.selectAll(".arc").on('mouseover', function () {
            var pos = d3.mouse(svg.node()),
                    x = pos[0], y = pos[1],
                    d = d3.select(this).data()[0].data;

            var text = tooltip.select('.issue-type-name').text(d.fullText);
            tooltip.select('.issue-count').text(format(d.count) + " issues");

            var ttwidth = Math.max(20 + text[0][0].getComputedTextLength(), 100), ttheight = 60;

            // Detect if the tooltip would go outside the svg.
            if (x > width - ttwidth) { x -= ttwidth; }
            if (y > 500) { y -= ttheight; }

            tooltip.transition().attr('transform', 'translate(' + x + ',' + y + ')');
            tooltip.select('rect')
                    .transition()
                    .attr('width', ttwidth)
                    .attr('height', ttheight);
        });
    });

    function sendRefresh() {
        d3.json('{{ url('data/issues/refresh') }}', function (error, data) {
            var msg = data.message + " Refresh this page after some time to see updates.";
            alert(msg);
        });
    }

    function loadProject() {
        var url = '{{ url('viz/issues') }}';
        var projectName = document.getElementById('project-name').value;
        if (projectName != '') {
            url += '/' + projectName;
        }
        window.location = url;
    }

</script>
@endpush

@section('presvgcontent')
    <div class="col-sm-12">
        <label>Project <input type="text" name="project" id="project-name" placeholder="drupal" value="{{ $project_name }}" onchange="loadProject();"></label>
        <button class="btn btn-success" onclick="loadProject();">Submit</button>
        {{--<button class="btn btn-warning" onclick="sendRefresh();">Refresh Data</button>--}}
    </div>
@endsection

@section('svgcontent')
    <div class="panel panel-body">
        <h2>Project Issues Breakdown</h2>
        <p>This pie chart shows a break-down of all issues across all projects or for a specific project. The inner-most donut shows the category of the issue. The middle one shows the priority for each of those categories and the last one shows the status for each of the issues under that category and priority.</p>
        <p>To change the project, simply type the drupal.org project machine name and 'Submit' and the page will reload with data for that project.</p>
    </div>
@endsection
