<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>D3 World Map</title>
    <style>
      path {
        stroke: white;
        stroke-width: 0.5px;
        fill: black;
      }
    </style>
     <script src="https://d3js.org/d3.v4.js"></script>
        <script src="https://unpkg.com/topojson-client@3"></script>
        <script src="https://cdn.jsdelivr.net/npm/d3-array@3"></script>


  </head>
  <body>
    <script type="text/javascript">
      var width = 900;
      var height = 600;

      var projection = d3.geo.mercator(x, y);
      
      var svg = d3.select("body").append("svg")
          .attr("width", width)
          .attr("height", height);
      var path = d3.geo.path()
          .projection(projection);
      var g = svg.append("g");
      
      d3.json("us_counties_albers", function(error, topology) {
          g.selectAll("path")
            .data(topojson.object(topology, topology.objects.countries)
                .geometries)
          .enter()
            .append("path")
            .attr("d", path)
      });

        function mercator(x, y) {
            return [x, Math.log(Math.tan(Math.PI / 4 + y / 2))];
        }
    </script>
  </body>
</html>