var map = L.map('map', {
  'center': [39.095963, -96.503906],
  'zoom': 5,
  'layers': [
    L.tileLayer('http://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}.png',{
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, &copy; <a href="http://cartodb.com/attributions">CartoDB</a>'
    })
  ]
});

var url = 'https://rawgit.com/mbostock/topojson/master/examples/us-10m.json';

var geojsonLayer = new L.GeoJSON(null, {
  style: getStyle,
}).addTo(map);

$.getJSON(url, function(data) {
  var collection = topojson.feature(data, data.objects.counties);
  collection.features.forEach(function(feature){
    feature.properties.metric = Math.floor((Math.random() * 10) + 1);
  });
  geojsonLayer.addData(collection);
});

function getStyle(feature) {
  return {
    weight: 1,
    color: 'black',
    fill: feature.properties.metric != 5,
    fillColor: feature.properties.metric == 5 ? 'yellow' : 'red',
    fillOpacity: feature.properties.metric / 10
  };
}