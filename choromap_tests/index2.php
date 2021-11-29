<html>
    <head>
        <!-- <link rel="stylesheet" href="./style.css" /> -->
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.3/dist/leaflet.css" />
		<script src="https://unpkg.com/leaflet@1.0.3/dist/leaflet.js"></script>
    <script src="https://d3js.org/d3.v4.js"></script>
    <script src="https://unpkg.com/topojson-client@3"></script>
    <style>
        /* #map {
        width:100%;
        height: 100vh;
        } */
        .info {
            padding: 6px 8px;
            font: 14px/16px Arial, Helvetica, sans-serif;
            background: white;
            background: rgba(255,255,255,0.8);
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            border-radius: 5px;
        }
        .info h4 {
            margin: 0 0 5px;
            color: #777;
        }
    </style>

    </head>
	<body>

        <!--
         Need to add some styling to my map and figure out if I'm going to change all that jam up there in the script. just wanted to kind of start the quickstart tutorial.
         -->
        <div  id="map" style="width:100%; height: 97vh;"></div>

<script>
        
    var map
    var god;
    var neighbors;
    var geoschools = {};
    var geojson;
    var co = d3.scaleOrdinal(d3.schemeCategory20b);
    window.onload = function () {
        var req = new XMLHttpRequest();
        var url = './counties.topojson'

            req.open('GET', url, true);
            req.onreadystatechange = handler;
            req.send();
        var topoob = {};
        geoschools = {};
        function handler(){
            if(req.readyState === XMLHttpRequest.DONE){
                map = new L.Map('map',
                        {
                        center: new L.LatLng(38.9730753466975, -94.31103944778444),
                        zoom: 5
                        });

                var OpenStreetMap_BlackAndWhite = L.tileLayer('http://{s}.tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png', {
                    maxZoom: 18
                        //   attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                });

                map.addLayer( OpenStreetMap_BlackAndWhite)//new L.StamenTileLayer(layer));
                // try and catch my json parsing of the responseText
                try {
                    topoob = JSON.parse(req.responseText)
                    neighbors = topojson.neighbors(topoob.objects.counties.geometries);
                    geoschools = topojson.feature(topoob, topoob.objects.counties)


                    geoschools.features = geoschools.features.map(function(fm,i){
                        var ret = fm;
                        ret.indie = i;
                        return ret
                        });
                    geojson = L.geoJson(geoschools, {style:style, onEachFeature: onEachFeature})
                                .addTo(map);
                    console.log('neigh', neighbors)
                }
                catch(e){
                    geojson = {};
                    console.log(e)
                }
                console.log(geoschools)

                function highlightFeature(e){
                    var layer = e.target;
                    layer.setStyle({
                        weight: 5,
                        color: '#665',
                        fillColor : 'orange',
                        dashArray: '',
                        fillOpacity: .7})
                        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                            layer.bringToFront();
                        }
                    info.update(layer.feature.properties);
                }

                function resetHighlight(e){
                    geojson.resetStyle(e.target);
                    info.update();
                }

                function zoomToFeature(e) {
                    map.fitBounds(e.target.getBounds());
                }

                function onEachFeature(feature, layer){
                    layer.on({
                        mouseover: highlightFeature,
                        mouseout: resetHighlight, 
                        click: zoomToFeature
                    })
                }
                var info = L.control();
                info.onAdd = function(map) {
                    this._div = L.DomUtil.create('div', 'info');
                    this.update();
                    return this._div;
                }

                info.update = function(props){
                    this._div.innerHTML = "<h4>County_opioid_mortality_rates</h4>" + (props ? 
                    '<b>' + props.name +' County' + '</b><br />' +props.name+ ' Broadband Access'+ '</b><br />' +props.name+ ' Mortality rate 2010-2014'+ '</b><br />' +props.name+ ' Total deaths 2014-2019'
                    : 'Hover over a state');
                }
                info.addTo(map);
            }
        }
        
        function style(feat, i){
            var i = feat.indie;
            var coco = co(feat.color = d3.max(neighbors[i], function(n) {
            return geoschools.features[n].color; }) + 1 | 0);

            return {fillColor: coco,
                    fillOpacity: .8,
                    weight: .8}
        }
    }
</script>
	</body>
</html>