<?php

/**
 * @plugin   Leaflet Map
 * @version  1.0.7
 * @author   Grant Amaral <grant@lrio.com>
 * @link     https://lrio.com
 * @license  GPL-2.0-or-later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

class PlgSystemLeafletMap extends CMSPlugin {
    protected $autoloadLanguage = true;

    /** Ensure we only register assets once per request */
    private bool $assetsRegistered = false;

    /** Auto-increment container IDs */
    private int $mapContainerCounter = 0;

    public function onContentPrepare($context, &$article, &$params, $limitstart) {
        if (strpos($article->text ?? '', '{leafletmap') === false) {
            return;
        }

        // Load assets once
        $this->loadAssets();

        // Replace each {leafletmap ...} tag
        $article->text = preg_replace_callback('/{leafletmap(.*?)}/is', [$this, 'replaceMapTag'], $article->text);
    }

    private function loadAssets(): void {
        if ($this->assetsRegistered) {
            return;
        }

        $doc = \Joomla\CMS\Factory::getDocument();
        $wa  = $doc->getWebAssetManager();

        // CSS
        $wa->registerAndUseStyle('leaflet-css',        'media/plg_system_leafletmap/css/leaflet.css',            [], ['version' => 'auto']);
        $wa->registerAndUseStyle('leaflet-fs-css',     'media/plg_system_leafletmap/css/Control.FullScreen.css', [], ['version' => 'auto']);
        $wa->registerAndUseStyle('leaflet-custom-css', 'media/plg_system_leafletmap/css/leaflet_custom.css',     [], ['version' => 'auto']);

        // JS (order & deps matter)
        $wa->registerAndUseScript('leaflet-js',         'media/plg_system_leafletmap/js/leaflet.js',               [],                 ['defer' => true, 'version' => 'auto']);
        $wa->registerAndUseScript('leaflet-omni-js',    'media/plg_system_leafletmap/js/leaflet-omnivore.min.js',  ['leaflet-js'],     ['defer' => true, 'version' => 'auto']);
        $wa->registerAndUseScript('leaflet-fs-js',      'media/plg_system_leafletmap/js/Control.FullScreen.js',    ['leaflet-js'],     ['defer' => true, 'version' => 'auto']);
        $wa->registerAndUseScript('leaflet-helpers-js', 'media/plg_system_leafletmap/js/leaflet-helpers.js',       ['leaflet-js', 'leaflet-omni-js'], ['defer' => true, 'version' => 'auto']);

        $this->assetsRegistered = true;
    }


    private function replaceMapTag(array $matches): string {
        $params = $this->parseParams($matches[1]);

        // Start with plugin defaults
        $config = [
            'lat'            => $this->params->get('lat'),
            'lng'            => $this->params->get('lng'),
            'longitude'      => $this->params->get('longitude'), // kept if used in your XML
            'zoom'           => $this->params->get('zoom', 13),
            'kml_toggle'     => (bool) $this->params->get('kml_toggle', 0),
            'title_toggle'   => (bool) $this->params->get('title_toggle', 0),
            'zoom_wheel'     => (bool) $this->params->get('zoom_wheel', 1),
            'toggle_css'     => (bool) $this->params->get('toggle_css', 0),
            'marker_title'   => (string) $this->params->get('marker_title', ''),
            'marker_content' => (string) $this->params->get('marker_content', ''),
            'marker_icon'    => (string) $this->params->get('marker_icon', ''),
            'marker_custom'  => (string) $this->params->get('marker_custom', ''),
            'basemap'        => (string) $this->params->get('basemap', ''),
            'kml'            => (string) $this->params->get('kml', ''),
            'geojson'        => (string) $this->params->get('geojson', ''),
            'width'          => (string) $this->params->get('width', '100%'),
            'height'         => (string) $this->params->get('height', '300px'),
            'align'          => (string) $this->params->get('align', 'align-center'),
            'markers'        => '' // shortcode-only composite param
        ];

        // Override with shortcode params
        foreach ($params as $key => $value) {
            if (array_key_exists($key, $config)) {
                $config[$key] = $value;
            }
        }

        // Normalize
        $lat            = (float) $config['lat'];
        $lng            = (float) $config['lng'];
        $zoom           = (int)   $config['zoom'];
        $kml_toggle     = (bool)  $config['kml_toggle'];
        $title_toggle   = (bool)  $config['title_toggle'];
        $zoom_wheel     = (bool)  $config['zoom_wheel'];
        $toggle_css     = (bool)  $config['toggle_css'];
        $markerTitle    = trim((string) $config['marker_title']);
        $markerContent  = trim((string) $config['marker_content']);
        $marker_icon    = trim((string) $config['marker_icon']);
        $marker_custom  = trim((string) $config['marker_custom']);
        $kml            = trim((string) $config['kml']);
        $geojson        = trim((string) $config['geojson']);
        $width          = trim((string) $config['width']);
        $height         = trim((string) $config['height']);
        $align          = strtolower(trim((string) $config['align']));

        // Multiple markers from shortcode (lat,lng,title,content,icon | ...)
        $markers = $this->parseMultipleMarkers($params['markers'] ?? '');

        // Fallback to single marker if no KML/GeoJSON and no multi-markers provided
        if (empty($markers) && !$kml && !$geojson) {
            $markers[] = [
                'lat'     => $lat,
                'lng'     => $lng,
                'title'   => $markerTitle,
                'content' => $markerContent,
                'icon'    => $marker_icon
            ];
        }

        // Validate dimensions
        if (!preg_match('/^\d+(px|%)$/', $width)) {
            $width  = '100%';
        }
        if (!preg_match('/^\d+(px|%)$/', $height)) {
            $height = '300px';
        }

        // Validate alignment
        $validAligns = ['align-left', 'align-center', 'align-right'];
        if (!in_array($align, $validAligns, true)) {
            $align = 'align-center';
        }

        // Marker icon handling
        if ($marker_icon === 'custom' && $marker_custom !== '') {
            $marker_icon = trim($marker_custom);
        }

        if ($marker_icon !== '') {
            $isAbsolute =
                str_starts_with($marker_icon, 'http://') ||
                str_starts_with($marker_icon, 'https://') ||
                str_starts_with($marker_icon, '//') ||
                str_starts_with($marker_icon, '/') ||
                str_starts_with($marker_icon, 'data:');

            if (!$isAbsolute) {
                // Bare filename like "blue-marker.png"
                $marker_icon = Uri::root() . 'media/plg_system_leafletmap/images/markers/' . ltrim($marker_icon, '/');
            }
        }


        // Hide the little attribution flag if requested
        if ($toggle_css) {
            Factory::getDocument()->addStyleDeclaration('.leaflet-attribution-flag{display:none !important;}');
        }

        // Escape popup content for JS
        $markerContentJs = $this->escapeForJs($markerContent);

        // Unique container
        $mapContainerId = 'map' . $this->mapContainerCounter++;
        $mapContainerOptions = $this->getContainerOptions($params, 'map');
        $mapContainerOptions['style'] = 'width:' . $width . ';height:' . $height . ';';
        $mapContainerOptions['class'] = $align;

        // Build HTML
        $html  = '<div class="map-container"><div id="' . $mapContainerId . '" ' . $this->renderHtmlAttributes($mapContainerOptions) . '></div></div>';

        // Build JS
        $markersJson    = $this->markersToJSON($markers);
        $titleToggleJs  = $title_toggle ? 'true' : 'false';
        $hasKmlJs       = $kml   !== '' ? 'true' : 'false';
        $hasGeoJsonJs   = $geojson !== '' ? 'true' : 'false';
        $kmlUrlJs       = $this->escapeForJs($kml);
        $geoJsonUrlJs   = $this->escapeForJs($geojson);
        $markerTitleJs  = $this->escapeForJs($markerTitle);
        $markerIconJs   = $this->escapeForJs($marker_icon);
        // add these two:
        $baseMarkerDirJs = $this->escapeForJs(\Joomla\CMS\Uri\Uri::root() . 'media/plg_system_leafletmap/images/markers/');
        $shadowUrlJs     = $this->escapeForJs(\Joomla\CMS\Uri\Uri::root() . 'media/plg_system_leafletmap/images/marker-shadow.png');

        $showDefault       = (int) $this->params->get('show_default_marker', 1);
        $hasShortcodeMarks = !empty($markers);
        $hasKml            = ($kml !== '');
        $hasGeojson        = ($geojson !== '');
        $shouldAddDefault  = ($showDefault && !$hasShortcodeMarks && !$hasKml && !$hasGeojson);
        // BEFORE the heredoc 1.0.8
        $shouldAddDefaultJs = $shouldAddDefault ? 'true' : 'false';

        // Ensure plugin-level icon is a full URL if it’s a filename
        $resolvedIcon = trim($marker_icon);
        if ($resolvedIcon === 'custom' && !empty($config['marker_custom'])) {
            $resolvedIcon = trim($config['marker_custom']);
        }
        if (
            $resolvedIcon !== '' &&
            !preg_match('#^(?:https?:)?//#', $resolvedIcon) &&
            $resolvedIcon[0] !== '/' &&
            strpos($resolvedIcon, 'data:') !== 0
        ) {
            $resolvedIcon = \Joomla\CMS\Uri\Uri::root() . 'media/plg_system_leafletmap/images/markers/' . ltrim($resolvedIcon, '/');
        }

        $markerIconJs   = $this->escapeForJs($resolvedIcon);
        $markerTitleJs  = $this->escapeForJs($markerTitle);
        $markerContentJs = $this->escapeForJs($markerContent);
        $shadowUrlJs    = $this->escapeForJs(\Joomla\CMS\Uri\Uri::root() . 'media/plg_system_leafletmap/images/marker-shadow.png');

        $html .= <<<SCRIPT
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof initLeafletMap === 'undefined') {
        console.error('Leaflet helpers not loaded. Check media asset paths.');
        return;
    }

    const map = initLeafletMap("{$mapContainerId}", {$lat}, {$lng}, {$zoom}, {$zoom_wheel});

    // ---- Per-marker icon helpers ----
    const BASE_MARKER_DIR = "{$baseMarkerDirJs}";
    const SHADOW_URL      = "{$shadowUrlJs}";
    const GLOBAL_ICON     = "{$markerIconJs}"; // plugin-level icon URL (may be empty)

    function resolveIconUrl(raw) {
        if (!raw) return "";
        const r = String(raw).trim();
        // absolute (http/https), protocol-relative (//), site-absolute (/), or data: URIs → use as-is
        if (/^(?:https?:)?\\/\\//.test(r) || r.startsWith('/') || r.startsWith('data:')) return r;
        // bare filename → prepend your media markers dir
        return BASE_MARKER_DIR + r.replace(/^\\/+/, '');
    }

    // Multiple markers (supports per-marker icon via shortcode)
    const markers = {$markersJson};
    markers.forEach((m, i) => {
        const opt = { title: m.title || '' };

        // Prefer per-marker icon; fallback to plugin-level icon; else Leaflet default
        const iconUrl = m.icon ? resolveIconUrl(m.icon) : (GLOBAL_ICON || "");
        if (iconUrl) {
            opt.icon = L.icon({
                iconUrl,
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowUrl: SHADOW_URL
            });
        }

        const mrk = L.marker([m.lat, m.lng], opt).addTo(map);
        if (m.content) mrk.bindPopup(m.content);
        if (i === 0 && {$titleToggleJs} && m.content) mrk.openPopup();
    });

    // Fit bounds when multiple markers
    if (markers.length > 1) {
        const group = new L.featureGroup(markers.map(m => L.marker([m.lat, m.lng])));
        map.fitBounds(group.getBounds().pad(0.1));
    }

    // Default marker only when the map would otherwise be empty (controlled by settings)
    if (!markers.length && !{$hasKmlJs} && !{$hasGeoJsonJs} && {$shouldAddDefaultJs}) {
        const opt = {};
        if ("{$markerIconJs}") {
            opt.icon = L.icon({
                iconUrl: "{$markerIconJs}",
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowUrl: "{$shadowUrlJs}"
            });
        }
        const fallback = L.marker([{$lat}, {$lng}], opt).addTo(map);
        // Prefer content; fall back to title
        const popupHtml = "{$markerContentJs}" || "{$markerTitleJs}";
        if (popupHtml) fallback.bindPopup(popupHtml);
    }

    // KML/GeoJSON (external helpers)
    if ({$hasKmlJs})     { loadKml(map, "{$kmlUrlJs}", "Route Layer"); }
    if ({$hasGeoJsonJs}) { loadGeoJson(map, "{$geoJsonUrlJs}", "Schedule Layer"); }
});
</script>
SCRIPT;


        return $html;
    }

    private function getContainerOptions(array $params, string $type): array {
        $options = [];
        if (isset($params[$type . '_container'])) {
            $containerParams = $this->parseParams($params[$type . '_container']);
            foreach ($containerParams as $key => $value) {
                $options[$key] = $value;
            }
        }
        return $options;
    }

    private function renderHtmlAttributes(array $attributes): string {
        $out = '';
        foreach ($attributes as $key => $value) {
            $out .= htmlspecialchars($key, ENT_QUOTES) . '="' . htmlspecialchars((string) $value, ENT_QUOTES) . '" ';
        }
        return trim($out);
    }

    private function parseParams(string $paramsString): array {
        $params = [];
        preg_match_all('/(\w+)=(["\'])(.*?)\2/', $paramsString, $matches, PREG_SET_ORDER);
        foreach ($matches as $m) {
            $params[$m[1]] = $m[3];
        }
        return $params;
    }

    private function parseMultipleMarkers(string $markersString): array {
        $markers = [];
        if ($markersString === '') {
            return $markers;
        }
        foreach (explode('|', $markersString) as $group) {
            $parts = array_map('trim', explode(',', $group));
            if (count($parts) >= 2) {
                $markers[] = [
                    'lat'     => (float) ($parts[0] ?? 0),
                    'lng'     => (float) ($parts[1] ?? 0),
                    'title'   => (string) ($parts[2] ?? ''),
                    'content' => (string) ($parts[3] ?? ''),
                    'icon'    => (string) ($parts[4] ?? '')
                ];
            }
        }
        return $markers;
    }

    private function markersToJSON(array $markers): string {
        $processed = [];
        foreach ($markers as $m) {
            $content = (string) ($m['content'] ?? '');
            $content = str_replace(["\r\n", "\r", "\n"], '<br>', $content);
            $content = str_replace(['\\', '"'], ['\\\\', '\\"'], $content);
            $processed[] = [
                'lat'     => (float) ($m['lat'] ?? 0),
                'lng'     => (float) ($m['lng'] ?? 0),
                'title'   => (string) ($m['title'] ?? ''),
                'content' => $content,
                'icon'    => (string) ($m['icon'] ?? '')
            ];
        }
        return json_encode($processed, JSON_UNESCAPED_SLASHES);
    }

    private function escapeForJs(string $s): string {
        $s = str_replace(["\r\n", "\r", "\n"], ' ', $s);
        return str_replace(['\\', '"'], ['\\\\', '\\"'], $s);
    }
}
