# change log

## [1.0.8] — 2025-09-06

- added default marker toggle
- cleaned the media dir of old unused marker icon dirs. marker icons now in images/markers for the leaflet.css
- added a change log url in the xml
- removed settings option for multiple default markers

## [1.0.7] — 2025-09-06

- Added **per-marker icon** support via shortcode (`markers="lat,lng,title,popup,icon.png"`), with fallback to plugin-level icon and Leaflet default.
- Fixed asset paths: load CSS/JS from `media/plg_system_leafletmap/...` via Web Asset Manager.
- Standardized media layout:
  - Stock Leaflet icons moved to `media/plg_system_leafletmap/css/images/` (matches `leaflet.css`).
  - Custom markers resolved under `media/plg_system_leafletmap/images/markers/`.
- Introduced robust icon URL resolver (supports absolute URLs, protocol-relative, site-absolute, `data:` URIs, or bare filenames).
- Resolved heredoc issues: replaced `<?= ... ?>` inside heredocs with interpolated variables; prevents “Unexpected token '<'” and script text rendering.
- Minor: ensured marker shadow path, retained `fitBounds` for multiple markers, small cleanup.

## v1.0.6

- 2025-04-27
- [ ] Maintenance and security improvements, with new customizable marker popups.
- [ ] Added new marker_content parameter for rich popup content (supports basic HTML like ```<b>, <i>, <br>```).
- [ ] Separated marker title (hover tooltip) from marker popup (click content).
- [ ] Improved JavaScript output by safely encoding popup content using json_encode().
- [ ] Corrected asset loading for Leaflet Omnivore plugin to use local copy instead of external Mapbox CDN.
- [ ] Tightened Content Security Policy (CSP) headers to improve security while allowing map tiles, local scripts, and marker popups.
- ✅ Reusable loadKml() and loadGeoJson()
- ✅ Proper shared color sequencing
- ✅ Popup helpers
- ✅ Automatic fitBounds()
- ✅ Per-map, self-contained LayerControl (base + overlays)
- ✅ Only injecting once, even with multiple {leafletmap} on a page
- ✅ clean, modular JS

## v 1.0.5

- checksum in joomla update
- joomla update
- mobile css
- custom marker
- fixed the map height not passing
- added two finger scroll line 159 & 162 this may eliminate the need for disabling scroll_wheel zoom
- removed some debugging var_dump
- moved geojson.php to ./includes
- added license comment to geojson.php for JED
- css to toggle the flag

## v 1.0.4 - 2023-07-18

- skipped the version numbering from 0.0.3 to 1.0.4
- fixed the joomla update system
  - error at xml file:  <updateservers> <server type="plugin" -> corrected: <server type=extension ...
- fixed the default basemap
- set the tag release system in github
- added geojson option w/ fitBounds

## v 0.0.3

- added full screen plugin js
- ➕ joomla update system
- 🗸 added zoom wheel toggle
- added kml layer
- omnivore plugin js and css
- fit bounds on kml layer
- changed the container name from leaflet-map to map to remove all the instances of str_replace
- added toggle setting to open the marker title on load
- added esri satellite basemap

## v 0.0.2

- added full screen plugin
- all the css and js are in the plugin dir
- the language files are installed in the admin/language/ll-LL dirs automatically
- added the full screen js and css
- moved the leaflet images dir to css/images
- added fieldset groups for description and settings
- changed the names of the language files replacing the en-GB. etc prefix with plg_system_xxx
- added a field to select the basemap in the xml. not implemented in the php yet.
- added z index to custom css to keep the menu infront of map

## v 0.0.1

`initial release`
