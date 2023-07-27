<?php

/**
 * @plugin         Leaflet Map
 * @version         1.0.5
 * 
 * @author          amaral <grant@lrio.com
 * @link            http://lrio.com
 * @copyright       Copyright © 2023 Amaral All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
defined('_JEXEC') or die;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Language\Text;

class PlgSystemLeafletMap extends CMSPlugin
    {
        // ga july 23 load the language files
        protected $autoloadLanguage = true;

        // Track initialized map containers
        private $initializedMapContainers = [];

        // Counter for auto-incrementing container IDs
        private $mapContainerCounter = 0;

        public function onContentPrepare($context, &$article, &$params, $limitstart) {
                // Check if the article content contains the plugin tag
                if (strpos($article->text, '{leafletmap') !== false) {
                    // Load the necessary CSS and JS files
                    $this->loadAssets();

                    // Replace the plugin tag with the generated map HTML
                    $article->text = preg_replace_callback('/{leafletmap(.*?)}/is', array($this, 'replaceMapTag'), $article->text);
                }
            }

        private function loadAssets() // Load Leaflet CSS and JS files
            {
                
                JFactory::getDocument()->addStyleSheet('/plugins/system/leafletmap/css/leaflet.css');
                JFactory::getDocument()->addStyleSheet('/plugins/system/leafletmap/css/Control.FullScreen.css');
                JFactory::getDocument()->addStyleSheet('/plugins/system/leafletmap/css/leaflet_custom.css');
                JFactory::getDocument()->addScript('/plugins/system/leafletmap/js/leaflet.js');
                JFactory::getDocument()->addScript('//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-omnivore/v0.3.1/leaflet-omnivore.min.js');
                JFactory::getDocument()->addScript('/plugins/system/leafletmap/js/Control.FullScreen.js');
            }        
        private function replaceMapTag($matches) {
            $params = $this->parseParams($matches[1]);
                // Get the plugin parameters from the backend
                $lat = $this->params->get('latitude', '');
                $long = $this->params->get('longitude', '');
                $markerTitle = $this->params->get('marker_title', '');
                $width = $this->params->get('div_width', '');
                $height = $this->params->get('div_height', '');
                $basemap = $this->params->get('basemap', '');
                $zoom = $this->params->get('zoom', '');
                $kml_toggle = $this->params->get('kml_toggle', '');
                $kml = $this->params->get('kml', '');
                $geojson = $this->params->get('geojson', '');
                $title_toggle = $this->params->get('title_toggle', '');
                $zoom_wheel = $this->params->get('zoom_wheel', '');
                $marker_icon = $this->params->get('marker_icon', '');
                $toggle_css = $this->params->get('toggle_css', 0);
            
            // Override the parameters with values from the content call, if available
                if (isset($params['lat'])) {
                    $lat = $params['lat'];
                }
                if (isset($params['long'])) {
                    $long = $params['long'];
                }
                if (isset($params['marker_title'])) {
                    $markerTitle = $params['marker_title'];
                }
                if (isset($params['width'])) {
                    $width = $params['width'];
                }
                if (isset($params['height'])) {
                    $height = $params['height'];
                }
                if (isset($params['basemap'])) {
                    $basemap = $params['basemap'];
                }
                if (isset($params['zoom'])) {
                    $zoom = $params['zoom'];
                }
                if (isset($params['kml'])) {
                    $kml = $params['kml'];
                }
                if (isset($params['geojson'])) {
                    $geojson = $params['geojson'];
                }
                if (isset($params['title_toggle'])) {
                    $title_toggle = $params['title_toggle'];
                }
                if (isset($params['zoom_wheel'])) {
                    $zoom_wheel = $params['zoom_wheel'];
                }

            // Generate the map container ID with auto-increment
            $mapContainerId = 'map' . $this->mapContainerCounter;
                $this->mapContainerCounter++;
                // Check if the map container has already been initialized
                if (in_array($mapContainerId, $this->initializedMapContainers)) {
                    return '<div id="' . $mapContainerId . '"></div>';
                    }

                // Set the flag to indicate the map container has been initialized
                $this->initializedMapContainers[] = $mapContainerId;
        $float = 'right';
        if ($float == 1) {
            $float_class = "float-left ";
        } 
        if ($float == 'right') {
            $float_class = "float-right ";
        } else {
            $float_class ="";
        }
        var_dump($float_class);
                // Get the map container options from the content call or use defaults
                    // $mapContainerOptions = $this->getContainerOptions($params, 'map');
                    $mapContainerOptions = $this->getContainerOptions($params, ' . $mapContainerId . ');
                    // $mapContainerOptions['style'] = 'width: ' . $width . '; height: ' . $height . '; float: ' . $float_style . ';';
                    $mapContainerOptions['style'] = 'width: ' . $width . '; height: ' . $height . '; ';
                    $mapContainerOptions['class'] = $float_class;

                    $markerPopupCode = 'L.marker([' . $lat . ', ' . $long . ']).addTo(map_' . $mapContainerId . ').bindPopup(\'' . Text::_($markerTitle) . '\')';
                if ($title_toggle == 0) {
                        $markerPopupCode .= '.openPopup()';
                    }
                    $markerPopupCode .= ';';
            // BaseLayer logic
                if ($basemap == 0) {
                    $basemap = "osm"; // default
                } elseif ($basemap == 1) {
                    $basemap = 'esriSat'; 
                } elseif ($basemap == 2) {
                    $basemap = 'esriTopo'; 
                } elseif ($basemap == 3) {
                    $basemap = 'openTopo';
                }
            // hide the flag 
                if ($toggle_css == 1) {
                    JFactory::getDocument()->addStyleSheet('/plugins/system/leafletmap/css/leaflet_flag.css');
                    }
               
            // Generate the map HTML with the specified container options and initialization script
                $html = '<div id="' . $mapContainerId . '" ' . $this->renderHtmlAttributes($mapContainerOptions) . '></div>';
                $html .= '<script>
                    document.addEventListener(\'DOMContentLoaded\', function() {
                        var mapContainer_' . $mapContainerId . ' = document.getElementById(\'' . $mapContainerId . '\');

                        // basemaps
                            const osm = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {maxZoom: 19, attribution: "© OpenStreetMap" });
                            const openTopo = L.tileLayer("https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png", {maxZoom: 19, attribution: "@OpenTopoMap" });
                            const esriSat = L.tileLayer("https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}", {attribution: "&copy; Esri" });
                            const esriTopo = L.tileLayer("https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}", {attribution: "&copy; Esri" });

                        var greenIcon = new L.Icon({
                            iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png",
                            shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
                            iconSize: [25, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34],
                            shadowSize: [41, 41]
                            });
                            greenIcon = "";

                        
                        var map_' . $mapContainerId . ';
                    
                        if (' . ($zoom_wheel ? 'false' : 'true') . ') {
                            map_' . $mapContainerId . ' = L.map(mapContainer_' . $mapContainerId . ', { dragging: !L.Browser.mobile, tap: !L.Browser.mobile, layers: ' . $basemap . ' }).setView([' . $lat . ', ' . $long . '], ' . $zoom . ');
                            } else {
                                map_' . $mapContainerId . ' = L.map(mapContainer_' . $mapContainerId . ', { scrollWheelZoom: false, dragging: !L.Browser.mobile, tap: !L.Browser.mobile, layers: ' . $basemap . ' }).setView([' . $lat . ', ' . $long . '], ' . $zoom . ');
                            }

                        var marker_' . $mapContainerId . ' = L.marker([' . $lat . ', ' . $long . ']).addTo(map_' . $mapContainerId . ').bindPopup(\'' . Text::_($markerTitle) . '\');';
                        // var marker_' . $mapContainerId . ' = L.marker([' . $lat . ', ' . $long . '], {icon: greenIcon}).addTo(map_' . $mapContainerId . ').bindPopup(\'' . Text::_($markerTitle) . '\');';

                        if ($title_toggle == 1) {
                            $html .= 'marker_' . $mapContainerId . '.openPopup();';
                        }

                    $html .= '
                        var baseMaps = {
                            "OSM": osm,
                            "Satellite": esriSat,
                            "Topo": esriTopo,
                            "Open Topo": openTopo
                        };

                        var layerControl = L.control.layers(baseMaps).addTo(map_' . $mapContainerId . ');

                        // this turns off the leaflet atttribution. ukrane flag issue
                        //map_' . $mapContainerId . '.attributionControl.setPrefix("");
                        
                        // geojson
                            var geojsonLayer = null;
                            var bounds = null;
                            var geojsonUrl = "' . $geojson . '";

                            if (true && geojsonUrl !== "") {
                                geojsonLayer = L.geoJSON(null, {
                                    pointToLayer: function(feature, latlng) {
                                        // Use the green icon for markers in the KML layer
                                        // return L.marker(latlng, { icon: greenIcon });
                                        return L.marker(latlng);
                                    },
                                    onEachFeature: function(feature, layer) {
                                        if (layer instanceof L.Marker) {
                                            layer.bindPopup(feature.properties.name);
                                        }
                                    }
                                });

                                fetch(geojsonUrl)
                                    .then(response => response.json())
                                    .then(data => {
                                        geojsonLayer.addData(data);
                                        bounds = geojsonLayer.getBounds();
                                        map_' . $mapContainerId . '.fitBounds(bounds);
                                    })
                                    .catch(error => console.error(\'Error fetching GeoJSON:\', error));

                                geojsonLayer.addTo(map_' . $mapContainerId . ');
                            }

                        // kml
                        var kmlLayer = null;
                        var bounds = null;
                        var kmlUrl = "' . $kml . '";
                        if (true && kmlUrl !== "") {
                            kmlLayer = omnivore.kml(kmlUrl, null, L.geoJson(null, {
                                pointToLayer: function(feature, latlng) {
                                // Use the green icon for markers in the KML layer
                                // return L.marker(latlng, { icon: greenIcon });
                                return L.marker(latlng);
                                },
                                onEachFeature: function(feature, layer) {
                                    if (layer instanceof L.Marker) {
                                        layer.bindPopup(feature.properties.name);
                                    }
                                }
                            }));

                            kmlLayer.on(\'ready\', function() {
                                bounds = kmlLayer.getBounds();
                                map_' . $mapContainerId . '.fitBounds(bounds);
                            });

                            kmlLayer.addTo(map_' . $mapContainerId . ');
                        }

                        // create a fullscreen button and add it to the map
                        L.control.fullscreen({
                            position: "topleft",
                            title: "Show me the fullscreen!",
                            titleCancel: "Exit fullscreen mode",
                            content: null,
                            forceSeparateButton: true,
                            forcePseudoFullscreen: true,
                            fullscreenElement: false
                        }).addTo(map_' . $mapContainerId . ');

                        map_' . $mapContainerId . '.on(\'enterFullscreen\', function(){
                            if (kmlLayer && bounds) {
                                map_' . $mapContainerId . '.fitBounds(bounds);
                            }
                        });

                        map_' . $mapContainerId . '.on(\'exitFullscreen\', function(){
                            if (kmlLayer && bounds) {
                                map_' . $mapContainerId . '.fitBounds(bounds);
                            }
                        });

                    });
                </script>';

                    return $html;
            }
        private function getContainerOptions($params, $type)
            {
                $options = [];

                // Check if container options are specified in the content call
                if (isset($params[$type . '_container'])) {
                    $containerParams = $this->parseParams($params[$type . '_container']);
                    foreach ($containerParams as $key => $value) {
                        $options[$key] = $value;
                    }
                }

                return $options;
            }


        private function getUniqueContainerId($containerOptions)
            {
                $defaultContainerId = 'leaflet-map';

                // If the default container id does not exist, return it
                if (!in_array('id', array_keys($containerOptions))) {
                    return $defaultContainerId;
                }

                // Check if the default container id exists in all articles on the page
                $existingIds = [];

                // Get all articles on the page
                $articles = JModelLegacy::getInstance('Articles', 'ContentModel')->getItems();

                // Loop through each article to collect existing container IDs
                foreach ($articles as $article) {
                    preg_match_all('/id=[\'"](.*?)[\'"]/i', $article->text, $matches);
                    if (!empty($matches[1])) {
                        $existingIds = array_merge($existingIds, $matches[1]);
                    }
                }

                // Append a number to the default container id to make it unique
                $counter = 1;
                $uniqueId = $defaultContainerId;
                while (in_array($uniqueId, $existingIds)) {
                    $uniqueId = $defaultContainerId . '-' . $counter;
                    $counter++;
                }

                return $uniqueId;
            }

        private function renderHtmlAttributes($attributes)
            {
                $output = '';
                foreach ($attributes as $key => $value) {
                    $output .= $key . '="' . $value . '" ';
                }
                return trim($output);
            }
                private function parseParams($paramsString)
            {
                $params = array();
                preg_match_all('/(\w+)=(["\'])(.*?)\2/', $paramsString, $matches, PREG_SET_ORDER);
                foreach ($matches as $match) {
                    $key = $match[1];
                    $value = $match[3];
                    $params[$key] = $value;
                }
                return $params;
            }
    }
