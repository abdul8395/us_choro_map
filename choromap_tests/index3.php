<html>
    <head>
        <!-- <link rel="stylesheet" href="./style.css" /> -->
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.3/dist/leaflet.css" />
		<script src="https://unpkg.com/leaflet@1.0.3/dist/leaflet.js"></script>
        <script src="https://d3js.org/d3.v4.js"></script>
        <script src="https://unpkg.com/topojson-client@3"></script>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

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
        <div  id="map" style="width:100%; height: 100vh;"></div>
    </body>
</html>

<script>
    var counties_data=[];
    $(document).ready(function () {
    $.getJSON("/choromap/countydata.json", function(data) { 
        counties_data=data;
        loadgeom()
       
    });
    

    });
   
    var map
    var neighbors;
    var geocounties = {};
    var topoob = {};
    var geojson;


	var nomap =L.tileLayer('')

    var co = d3.scaleOrdinal(d3.schemeCategory20b);
    function loadgeom() {
        // console.log(counties_data)
        var url = './counties.topojson'
        var req = new XMLHttpRequest();
            req.open('GET', url, true);
            req.onreadystatechange = handler;
            req.send();
        
        function handler(){
            if(req.readyState === XMLHttpRequest.DONE){
                map = new L.Map('map',
                        {
                        center: new L.LatLng(38.9730753466975, -94.31103944778444),
                        zoom: 5,
                        zoomControl: false
                        });

                   

                var OpenStreetMap_BlackAndWhite = L.tileLayer('http://{s}.tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png', {
                    maxZoom: 18
                        //   attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                });

                // map.addLayer( OpenStreetMap_BlackAndWhite)//new L.StamenTileLayer(layer));
                // try and catch my json parsing of the responseText
                try {
                    topoob = JSON.parse(req.responseText)
                    neighbors = topojson.neighbors(topoob.objects.counties.geometries);
                    geocounties = topojson.feature(topoob, topoob.objects.counties)

                    geocounties.features = geocounties.features.map(function(fm,i){
                        var ret = fm;
                        ret.indie = i;
                        return ret
                    });
                    geojson = L.geoJson(geocounties, {style:style, onEachFeature: onEachFeature}).addTo(map); 
                }
                catch(e){
                    geojson = {};
                    // console.log(e)
                }
                function style(feat, i){
                    var i = feat.indie;
                    var coco = co(feat.color = d3.max(neighbors[i], function(n) {
                    return geocounties.features[n].color; }) + 1 | 0);
                    return {fillColor: coco,
                            fillOpacity: .9,
                            weight: .8
                            }
                }
                // get color depending on county value
                // function style(feature) {
                //     return {
                //         weight: 2,
                //         opacity: 1,
                //         color: 'white',
                //         dashArray: '3',
                //         fillOpacity: 0.7,
                //         fillColor: getColor(feature.properties.name)
                //     };
                // }
                // function getColor(d) {
                //     console.log(d)
                //     return d > 100 ? 'red' :
                //             d > 90  ? 'black' :
                //             d > 70  ? '#183C4C' :
                //             d > 50  ? '#2C6D8C' :
                //             d > 30   ? '#5A9FC6' :
                //             d > 20   ? '#AADDF6' :
                //             d > 10   ? '#D9F4FE' :
                //                         '#9C9A9A';
                // }

                function onEachFeature(feature, layer){
                    layer.on({
                        mouseover: highlightFeature,
                        mouseout: resetHighlight, 
                        click: popup
                    })
                }

                function highlightFeature(e){
                    var layer = e.target;
                    layer.setStyle({
                        weight: 4,
                        color: '#665',
                        fillColor : 'orange',
                        dashArray: '',
                        fillOpacity: .9})
                        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                            layer.bringToFront();
                        }
                        layer.bringToFront();

                    info.update(layer.feature.properties);
                }

                function resetHighlight(e){
                    geojson.resetStyle(e.target);
                    info.update();
                }

                // function zoomToFeature(e) {
                    // map.fitBounds(e.target.getBounds());
                // }
                function popup(e) {
                    var layer = e.target;
                    var props= layer.feature.properties
                            var str =  '<b>' + props.County_name + '</b><br /> Broadband_access: <b>' +props.Broadband_access+ '</b><br />Construction_deaths_T1: <b>' +props.Construction_deaths_T1+ '</b><br /> Mining_deaths_T1: <b>' +props.Mining_deaths_T1+ '</b><br /> Trade_deaths_T1: <b>' +props.Trade_deaths_T1+ '</b><br />Manufacturing_deaths_T1: <b>' +props.Manufacturing_deaths_T1+ '</b><br /> Total_deaths_2010_2014: <b>' +props.Total_deaths_2010_2014+'</b><br />'
                            layer.bindPopup(str);
                }

                var info = L.control();
                info.onAdd = function(map) {
                    this._div = L.DomUtil.create('div', 'info');
                    this.update();
                    return this._div;
                }

                info.update = function(props){
                    this._div.innerHTML = "<h4>County_opioid_mortality_rates</h4>" + (props ? 
                    '<b>' + props.County_name + '</b><br />'
                    // '<b>' + props.County_name + '</b><br /> Broadband_access: <b>' +props.Broadband_access+ '</b><br />Construction_deaths_T1: <b>' +props.Construction_deaths_T1+ '</b><br /> Mining_deaths_T1: <b>' +props.Mining_deaths_T1+ '</b><br /> Trade_deaths_T1: <b>' +props.Trade_deaths_T1+ '</b><br />Manufacturing_deaths_T1: <b>' +props.Manufacturing_deaths_T1+ '</b><br /> Total_deaths_2010_2014: <b>' +props.Total_deaths_2010_2014+'</b><br />'
                    : 'Hover over a County');
                }
                info.addTo(map);
            }
        }
        
        
    }



</script>
