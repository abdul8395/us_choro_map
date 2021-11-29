var proj = d3.geo.albersUsa()
  // .translate([0, 0])
  .scale(.5);
// ...projection code......
var AlbersProjection = {
  project: function(latLng) {
    var point = proj([latLng.lng, latLng.lat]);
    return point ? 
        new L.Point(point[0], point[1]) : 
	    	new L.Point(0, 0);
  },
  unproject: function(point) {
    var latLng = proj.invert([point.x, point.y]);
    return new L.LatLng(latLng[1], latLng[0]);
  }
}

var AlbersCRS = L.extend({}, L.CRS, {
  projection: AlbersProjection,
  transformation: new L.Transformation(1, 0, 1, 0),
  infinite: true
});

// map making
var center = [37.8, -96];
var map = new L.Map('map', {
    crs: AlbersCRS,
    zoomControl: false,
    attributionControl: false
})
  .setView(center, 3);
  map.options.minZoom = 3;
  map.options.maxZoom = 10;
  // var nomap = L.tileLayer('', {
  //                 attribution: '&copy; <a href="http://census.gov/">Develped By Markpu</a>'
  //               }).addTo(map)





var layer1;
var state_m_rates;
var county_m_rates;
var Mental_health_facilities= L.markerClusterGroup();
var Substance_abuse_facilities = L.markerClusterGroup();

// first albers layer
d3.json("js/states.json", function(error, data) {
  if (error) return console.error(error);
  layer1 = L.geoJson(data, {
  		style: {
        color:'black',
        weight: 2,
        opacity: 0.9,
      }
	  })
    .addTo(map);
  
  // map.fitBounds(layer.getBounds());
});

// state layer
d3.json("js/state_m_rates.json", function(error, data) {
  if (error) return console.error(error);
  state_m_rates = L.geoJson(data, {
                style: style,
                onEachFeature: onEachFeature
              })

    function getColor(d) {
      if(d.Broadband_access){
        return d.Broadband_access > 100 ? '#black' :
        d.Broadband_access > 99  ? '#E51E8C' :
        d.Broadband_access > 95  ? '#FF3769' :
        d.Broadband_access > 90   ? '#FF5F5F' :
        d.Broadband_access > 85   ? '#FFA739' :
        d.Broadband_access > 20   ? '#F4FF9B' :
              '#848484';
      }else{
        '#848484';
      }
      
    }
  
    function style(feature) {
      return {
        weight: 1,
        opacity: 0.5,
        color: '#848484',
        dashArray: '3',
        fillOpacity: 0.8,
        fillColor: getColor(feature.properties)
      };
    }

    function onEachFeature(feature, layer){
      layer.on({
          mouseover: highlightFeature,
          mouseout: resetHighlight, 
          click: zoomToFeature
      })
      var str;
      layer.bindPopup(str);
    }

    function highlightFeature(e) {
      var layer = e.target;
  
      layer.setStyle({
        weight: 2,
        opacity:0.9,
        color: 'orange',
        dashArray: '',
        fillOpacity: 0.1
      });
      if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
        layer.bringToFront();
      }
      // info.update(layer.feature.properties);
      // popuphover(layer.feature.properties);
    }
  
    function resetHighlight(e) {
      state_m_rates.resetStyle(e.target);
      // info.update();
    }
  
    function zoomToFeature(e) {
      var layer = e.target;
      var props= layer.feature.properties
      if(props.State){
         str =  '<b>' + props.State + '</b><br /> Broadband_access: <b>' +props.Broadband_access+ '</b><br />Construction_deaths_T1: <b>' +props.Construction_deaths_T1+ '</b><br /> Mining_deaths_T1: <b>' +props.Mining_deaths_T1+ '</b><br /> Trade_deaths_T1: <b>' +props.Trade_deaths_T1+ '</b><br />Manufacturing_deaths_T1: <b>' +props.Manufacturing_deaths_T1+ '</b><br /> Total_deaths_2010_2014: <b>' +props.Total_deaths_2010_2014+'</b><br />Mortality_rate_2014_2019: <b>' +props.Mortality_rate_2014_2019+ '</b><br /> Mortality_rate_change: <b>' +props.Mortality_rate_change+ '</b><br />Pop1: <b>' +props.Pop1+ '</b><br /> Change_in_total_deaths: <b>' +props.Change_in_total_deaths+'</b><br />'
              layer.bindPopup(str);
      }else{
         str =  '<b>' + props.State + '</b><br /> NO Data available: <b>'
              layer.bindPopup(str);
      }
      // map.fitBounds(e.target.getBounds());
    }

    // var info = L.control();
    //     info.onAdd = function(map) {
    //         this._div = L.DomUtil.create('div', 'info');
    //         this.update();
    //         return this._div;
    //     }

    // info.update = function(props){
    //       this._div.innerHTML = "<h4>State Information</h4>" + (props ? 
    //           'State Name: <b>' + props.State + '</b><br /> Click On State To Get More Information <b>'
    //         : 'Hover over a State');
    // }
    // info.addTo(map);

});

d3.json("js/county_opioid_geojson.json", function(error, data) {
    if (error) return console.error(error);
      county_m_rates = L.geoJson(data, {
                  style: style,
                  onEachFeature: onEachFeature
                }).addTo(map);

      function getColor(d) {
        if(d.Broadband_access){
          return d.Broadband_access > 100 ? '#black' :
          // d.Broadband_access > 99  ? '#E51E8C' :
          // d.Broadband_access > 80  ? '#FF3769' :
          // d.Broadband_access > 60   ? '#FF5F5F' :
          // d.Broadband_access > 40   ? '#FFA739' :
          // d.Broadband_access > 20   ? '#F4FF9B' :
          d.Broadband_access > 99  ? '#183C4C' :
          d.Broadband_access > 80  ? '#2C6D8C' :
          d.Broadband_access > 60   ? '#5A9FC6' :
          d.Broadband_access > 40   ? '#AADDF6' :
          d.Broadband_access > 20   ? '#D9F4FE' :
                '#848484';
        }else{
          '#848484';
        }
        
      }
    
      function style(feature) {
        return {
          weight: 1,
          opacity: 0.5,
          color: '#848484',
          dashArray: '3',
          fillOpacity: 0.8,
          fillColor: getColor(feature.properties)
        };
      }
      function onEachFeature(feature, layer){
        layer.on({
            mouseover: highlightFeature,
            mouseout: resetHighlight, 
            click: zoomToFeature
        })
        var str;
        layer.bindPopup(str);
      }
      function highlightFeature(e) {
        var layer = e.target;
    
        layer.setStyle({
          weight: 2,
          opacity:0.9,
          color: 'orange',
          dashArray: '',
          fillOpacity: 0.7
        });
        // cmarker.setStyle({
        //   radius: 5,
        //   fillColor: '#ff7800',
        //   color: '#ff7800',
        //   weight: 5,
        //   opacity: 0.9
        // })
    
        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
          layer.bringToFront();
        }
        info.update(layer.feature.properties);
        // popuphover(layer.feature.properties);
      }
    
      function resetHighlight(e) {
        county_m_rates.resetStyle(e.target);
        // info.update();
      }
    
      function zoomToFeature(e) {
        var layer = e.target;
        var props= layer.feature.properties
        if(props.County_name){
           str =  '<b>' + props.County_name + '</b><br /> Broadband_access: <b>' +props.Broadband_access+ '</b><br />Construction_deaths_T1: <b>' +props.Construction_deaths_T1+ '</b><br /> Mining_deaths_T1: <b>' +props.Mining_deaths_T1+ '</b><br /> Trade_deaths_T1: <b>' +props.Trade_deaths_T1+ '</b><br />Manufacturing_deaths_T1: <b>' +props.Manufacturing_deaths_T1+ '</b><br /> Total_deaths_2010_2014: <b>' +props.Total_deaths_2010_2014+'</b><br />'
                layer.bindPopup(str);
        }else{
           str =  '<b>' + props.NAME + '</b><br /> NO Data available: <b>'
                layer.bindPopup(str);
        }
        // map.fitBounds(e.target.getBounds());
      }

      var info = L.control();
          info.onAdd = function(map) {
              this._div = L.DomUtil.create('div', 'info');
              this.update();
              return this._div;
          }

      info.update = function(props){
            this._div.innerHTML = "<h4>County Information</h4>" + (props ? 
                'County Name: <b>' + props.NAME + '</b><br /> Click On County To Get More Information <b>'
              : 'Hover over a County');
      }
      info.addTo(map);
      map.removeLayer(state_m_rates)
      map.addLayer(state_m_rates)
      map.addLayer(Substance_abuse_facilities);
      map.addLayer(Mental_health_facilities);

  });

// ......mental health markers.......
  for (let i=0; i<mentalhealth.length; i++) {
    var mmarker= L.circleMarker([
      mentalhealth[i].Latitude, 
      mentalhealth[i].Longitude
    ], {
      "radius": 5,
      "color": "#F26822",
      "weight": 5,

    })
    str =  'Name: <b>' + mentalhealth[i].Name + '</b><br /> State: <b>' +mentalhealth[i].State+ '</b><br />City: <b>' +mentalhealth[i].City+ '</b><br /> Street: <b>' +mentalhealth[i].Street+ '</b><br /> ZIPCode: <b>' +mentalhealth[i].ZipCode+'</b><br /> Phone: <b>'+mentalhealth[i].Phone+'</b><br />'
    mmarker.bindPopup(str);
    Mental_health_facilities.addLayer(mmarker)
  }
 
 


// ......Substance_abuse_facilities markers.......

for (let i=0; i<Substance_abuse_facilities_arr.length; i++) {
  var smarker= L.circleMarker([
    Substance_abuse_facilities_arr[i].Latitude, 
    Substance_abuse_facilities_arr[i].Longitude
  ], {
    "radius": 5,
    "color": "#E51E8C",
    "weight": 5,

  })
  str =  'Name: <b>' + Substance_abuse_facilities_arr[i].Name + '</b><br /> State: <b>' +Substance_abuse_facilities_arr[i].State+ '</b><br />City: <b>' +Substance_abuse_facilities_arr[i].City+ '</b><br /> Street: <b>' +Substance_abuse_facilities_arr[i].Street+ '</b><br /> ZIPCode: <b>' +Substance_abuse_facilities_arr[i].ZIP+'</b><br /> Phone: <b>'+Substance_abuse_facilities_arr[i].Phone+'</b><br />'
  smarker.bindPopup(str);
  Substance_abuse_facilities.addLayer(smarker)
}










  //-----------add remove layers code----------  
  function addRemoveLayer(name){
      if(name=='state_m_rates'){
              var ckb = $("#state_m_rates").is(':checked');
              if(ckb==true){
                  map.addLayer(state_m_rates)
              }else{
                   map.removeLayer(state_m_rates)
              }
          }
      if(name=='county_m_rates'){
              var ckb = $("#county_m_rates").is(':checked');
              if(ckb==true){
                  map.addLayer(county_m_rates)
              }else{
                   map.removeLayer(county_m_rates)
              }
          }
  
      if(name=='mhf'){
          var ckb = $("#mhf").is(':checked');
          if(ckb==true){
          
              map.addLayer(Mental_health_facilities)
          }else{
              map.removeLayer(Mental_health_facilities)
          }
      }
      if(name=='saf'){
          var ckb = $("#saf").is(':checked');
          if(ckb==true){
          
              map.addLayer(Substance_abuse_facilities)
          }else{
              map.removeLayer(Substance_abuse_facilities)
          }
      }
  }