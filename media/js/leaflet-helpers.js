// media/plg_system_leafletmap/js/leaflet-helpers.js
(function () {
  let globalFeatureIndex = 0;
  const colorList = [
    '#FF5733', '#33FF57', '#3357FF', '#F333FF', '#FF33A8',
    '#33FFF5', '#A8FF33', '#FFC300', '#DAF7A6', '#581845'
  ];

  function createPopupFromFeature(feature) {
    let popup = '';
    if (feature && feature.properties) {
      if (feature.properties.name) {
        popup += '<strong>' + feature.properties.name + '</strong><br>';
      }
      if (feature.properties.description) {
        popup += feature.properties.description;
      }
    }
    return popup;
  }

  function updateLayerControl(map) {
    const baseMaps = {
      "OSM": L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", { maxZoom: 19 }),
      "Satellite": L.tileLayer("https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}", { attribution: "&copy; Esri" }),
      "Topo": L.tileLayer("https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}", { attribution: "&copy; Esri" }),
      "Open Topo": L.tileLayer("https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png", { maxZoom: 17, attribution: "@OpenTopoMap" })
    };

    if (!map._customLayerGroups) {
      map._customLayerGroups = {};
    }
    if (map._customLayerControl) {
      map.removeControl(map._customLayerControl);
    }
    map._customLayerControl = L.control.layers(baseMaps, map._customLayerGroups, {
      collapsed: false,
      position: 'topright'
    }).addTo(map);
  }

  // Expose helpers

  window.initLeafletMap = function (containerId, lat, lng, zoom, zoomWheel) {
    const container = document.getElementById(containerId);

    const osm      = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", { maxZoom: 19 });
    const esriSat  = L.tileLayer("https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}", { attribution: "&copy; Esri" });
    const esriTopo = L.tileLayer("https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}", { attribution: "&copy; Esri" });
    const openTopo = L.tileLayer("https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png", { maxZoom: 17, attribution: "@OpenTopoMap" });

    const baseMaps = { "OSM": osm, "Satellite": esriSat, "Topo": esriTopo, "Open Topo": openTopo };

    const map = L.map(container, {
      scrollWheelZoom: !!zoomWheel,
      dragging: !L.Browser.mobile,
      tap: !L.Browser.mobile,
      layers: [osm]
    }).setView([lat, lng], zoom);

    map._baseMaps = baseMaps;
    map._customLayerGroups = {};
    map._customLayerControl = L.control.layers(baseMaps, {}, {
      collapsed: false,
      position: 'topright'
    }).addTo(map);

    return map;
  };

  window.loadKml = function (map, url, name = 'KML Layer') {
    if (typeof omnivore === 'undefined' || !omnivore.kml) {
      console.error('leaflet-omnivore not loaded; cannot load KML.');
      return;
    }
    omnivore.kml(url, null, L.geoJson(null, {
      onEachFeature: function (feature, layer) {
        const popup = createPopupFromFeature(feature);
        if (popup) layer.bindPopup(popup);
      },
      style: function () {
        const color = colorList[globalFeatureIndex % colorList.length];
        globalFeatureIndex++;
        return { color, weight: 2, fillOpacity: 0.4 };
      },
      pointToLayer: function (feature, latlng) {
        return L.marker(latlng);
      }
    }))
    .on('ready', function () {
      try { map.fitBounds(this.getBounds()); } catch (e) {}
      map._customLayerGroups = map._customLayerGroups || {};
      map._customLayerGroups[name] = this;
      updateLayerControl(map);
    })
    .addTo(map);
  };

  window.loadGeoJson = function (map, url, name = 'GeoJSON Layer') {
    fetch(url)
      .then(r => r.json())
      .then(data => {
        const layer = L.geoJSON(data, {
          onEachFeature: function (feature, lyr) {
            const popup = createPopupFromFeature(feature);
            if (popup) lyr.bindPopup(popup);
          },
          style: function () {
            const color = colorList[globalFeatureIndex % colorList.length];
            globalFeatureIndex++;
            return { color, weight: 2, fillOpacity: 0.4 };
          },
          pointToLayer: function (feature, latlng) {
            return L.marker(latlng);
          }
        }).addTo(map);

        try { map.fitBounds(layer.getBounds()); } catch (e) {}
        map._customLayerGroups = map._customLayerGroups || {};
        map._customLayerGroups[name] = layer;
        updateLayerControl(map);
      })
      .catch(err => console.error('Error loading GeoJSON:', err));
  };

})();
