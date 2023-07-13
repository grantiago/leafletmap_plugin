<?php
// code a simple joomla 4 plugin to show a leaflet map with markers in a joomla article
// the lat long and marker text as variables in the plugin call
// make the plugin call the leaflet api as a system plugin and the paramaters in the content.
// the plugin should include the js and css only when called on by the plugin on a page. it shold not be hardcoded in the head of the template for every page
// give an example of how to call this plugin from a joomla article
//  JFactory::getDocument()->addStyleSheet('/plugins/system/leafletmap/leaflet_custom.css');

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

    public function onContentPrepare($context, &$article, &$params, $limitstart)
    {
        // Check if the article content contains the plugin tag
        if (strpos($article->text, '{leafletmap') !== false) {
            // Load the necessary CSS and JS files
            $this->loadAssets();

            // Replace the plugin tag with the generated map HTML
            $article->text = preg_replace_callback('/{leafletmap(.*?)}/is', array($this, 'replaceMapTag'), $article->text);
        }
    }

    private function loadAssets()
    {
        // Load Leaflet CSS and JS files
        JFactory::getDocument()->addStyleSheet('/plugins/system/leafletmap/css/leaflet.css');
        JFactory::getDocument()->addStyleSheet('/plugins/system/leafletmap/css/leaflet_custom.css');
        JFactory::getDocument()->addStyleSheet('/plugins/system/leafletmap/css/Control.FullScreen.css');
        JFactory::getDocument()->addScript('/plugins/system/leafletmap/js/leaflet.js');
        JFactory::getDocument()->addScript('//api.tiles.mapbox.com/mapbox.js/plugins/leaflet-omnivore/v0.3.1/leaflet-omnivore.min.js');
        JFactory::getDocument()->addScript('/plugins/system/leafletmap/js/Control.FullScreen.js');
    }

    private function replaceMapTag($matches)
    {
        $params = $this->parseParams($matches[1]);

        // Get the plugin parameters from the backend
        $lat = $this->params->get('latitude', '0');
        $long = $this->params->get('longitude', '0');
        $markerTitle = $this->params->get('marker_title', 'Map Marker');
        $width = $this->params->get('width', '100%');
        $height = $this->params->get('height', '150px');
        $basemap = $this->params->get('basemap', 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
        $zoom = $this->params->get('zoom', '13');
        $kml_toggle = $this->params->get('kml_toggle', '0');
        $kml = $this->params->get('kml', '');
        var_dump($kml);

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
            $height = $params['basemap'];
        }
        if (isset($params['zoom'])) {
            $zoom = $params['zoom'];
        }
        if (isset($params['kml'])) {
            $kml = $params['kml'];
        }

        var_dump($kml);
        // Generate the map container ID with auto-increment
        $mapContainerId = 'leaflet-map' . $this->mapContainerCounter;
        $this->mapContainerCounter++;

        // Check if the map container has already been initialized
        if (in_array($mapContainerId, $this->initializedMapContainers)) {
            return '<div id="' . $mapContainerId . '"></div>';
        }

        // Set the flag to indicate the map container has been initialized
        $this->initializedMapContainers[] = $mapContainerId;

        // Get the map container options from the content call or use defaults
        $mapContainerOptions = $this->getContainerOptions($params, 'map');
        $mapContainerOptions['style'] = 'width: ' . $width . '; height: ' . $height . ';';

        // Generate the map HTML with the specified container options and initialization script
        $html = '<div id="' . $mapContainerId . '" ' . $this->renderHtmlAttributes($mapContainerOptions) . '></div>';
        $html .= '<script>
            document.addEventListener(\'DOMContentLoaded\', function() {
            var mapContainer_' . str_replace('-', '_', $mapContainerId) . ' = document.getElementById(\'' . $mapContainerId . '\');
            var osm = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {maxZoom: 19, attribution: "© OpenStreetMap" });
            const openTopoMap = L.tileLayer("https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png", {maxZoom: 19, attribution: "@OpenTopoMap" });

            var map_' . str_replace('-', '_', $mapContainerId) . ' = L.map(mapContainer_' . str_replace('-', '_', $mapContainerId) . ').setView([' . $lat . ', ' . $long . '], ' . $zoom . ');    
            L.tileLayer(\'' . $basemap . '\').addTo(map_' . str_replace('-', '_', $mapContainerId) . ');
            L.marker([' . $lat . ', ' . $long . ']).addTo(map_' . str_replace('-', '_', $mapContainerId) . ').bindPopup(\'' . Text::_($markerTitle) . '\').openPopup();

            var baseMaps = {
                "OpenStreetMap": osm,
                "OpenTopoMap": openTopoMap 
            };

            var layerControl = L.control.layers(baseMaps).addTo(map_' . str_replace('-', '_', $mapContainerId) . ');
            var kmlLayer = null;
            var bounds = null;
            var kmlUrl = "' . $kml . '";

            if (true && kmlUrl !== "") {
                kmlLayer = omnivore.kml(kmlUrl, null, L.geoJson(null, {
                    onEachFeature: function(feature, layer) {
                        if (layer instanceof L.Marker) {
                            layer.bindPopup(feature.properties.name);
                        }
                    }
                }));

                kmlLayer.on(\'ready\', function() {
                    bounds = kmlLayer.getBounds();
                    map_' . str_replace('-', '_', $mapContainerId) . '.fitBounds(bounds);
                });

                kmlLayer.addTo(map_' . str_replace('-', '_', $mapContainerId) . ');
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
            }).addTo(map_' . str_replace('-', '_', $mapContainerId) . ');

                map_' . str_replace('-', '_', $mapContainerId) . '.on(\'enterFullscreen\', function(){
                    if (kmlLayer && bounds) {
                        map_' . str_replace('-', '_', $mapContainerId) . '.fitBounds(bounds);
                    }
                });

                map_' . str_replace('-', '_', $mapContainerId) . '.on(\'exitFullscreen\', function(){
                    if (kmlLayer && bounds) {
                        map_' . str_replace('-', '_', $mapContainerId) . '.fitBounds(bounds);
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
