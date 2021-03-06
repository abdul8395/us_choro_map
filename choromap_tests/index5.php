<!DOCTYPE html>
<meta charset="utf-8">
<body>
<script src="//d3js.org/d3.v3.min.js"></script>
<script src="//d3js.org/topojson.v1.min.js"></script>
<script>

var width = 1260,
    height = 900;

var color = d3.scale.log()
    .range(["hsl(62,100%,90%)", "hsl(228,30%,20%)"])
    .interpolate(d3.interpolateHcl);

var path = d3.geo.path()
    .projection(null);

var svg = d3.select("body").append("svg")
    .attr("width", width)
    .attr("height", height);

d3.json("us_counties_albers.json", function(error, us) {
  if (error) throw error;

  var counties = topojson.feature(us, us.objects.counties).features;

  var densities = counties
      .map(function(d) { return d.properties.density = d.properties.pop / path.area(d); })
      .sort(function(a, b) { return a - b; });

  color.domain([d3.quantile(densities, .01), d3.quantile(densities, .99)]);

  svg.append("g")
      .attr("class", "counties")
    .selectAll("path")
      .data(counties)
    .enter().append("path")
      .style("fill", function(d) { return color(d.properties.density); })
      .attr("d", path);
});

</script>