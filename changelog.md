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

### Shortcode Overrides — Reference Table (with examples)

> All values should be quoted: `key="value"`

| Attribute        | Purpose                             | Values / format (with examples)                                                                                     | Notes                                                                 |
| ---------------- | ----------------------------------- | ------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------- |
| `lat`            | Map center latitude                 | Decimal string, e.g. `"43.66"`                                                                                      | Overrides plugin default                                              |
| `lng`            | Map center longitude                | Decimal string, e.g. `"-116.23"`                                                                                    | Overrides plugin default                                              |
| `zoom`           | Initial zoom                        | Integer string, e.g. `"13"`                                                                                         | —                                                                     |
| `width`          | Map width                           | `"100%"` or `"360px"`                                                                                               | Validated to px/%                                                     |
| `height`         | Map height                          | `"360px"` or `"50%"`                                                                                                | Validated to px/%                                                     |
| `align`          | Container alignment                 | `"align-left"` \| `"align-center"` \| `"align-right"` (e.g. `"align-center"`)                                       | Applied to container `class`                                          |
| `toggle_css`     | Hide Leaflet attribution flag       | `"1"` (hide) or `"0"` (show), e.g. `"1"`                                                                            | Adds CSS: `.leaflet-attribution-flag{display:none!important;}`        |
| `title_toggle`   | Auto-open first popup               | `"1"` or `"0"`, e.g. `"1"`                                                                                          | Only if marker has content                                            |
| `zoom_wheel`     | Mouse wheel zoom                    | `"1"` or `"0"`, e.g. `"0"`                                                                                          | —                                                                     |
| `marker_title`   | Default marker title                | Text, e.g. `"My Spot"`                                                                                              | Used by fallback/default marker                                       |
| `marker_content` | Default marker popup (HTML allowed) | HTML/text, e.g. `"Grant&lt;br/&gt;Line 2"`                                                                          | Newlines become `<br>`                                                |
| `marker_icon`    | Default marker icon                 | `""`, `"custom"`, or filename, e.g. `"blue-marker.png"`                                                             | Filenames resolve to `media/plg_system_leafletmap/images/markers/...` |
| `marker_custom`  | Custom icon URL                     | URL/path, e.g. `"/media/plg_system_leafletmap/images/markers/custom-pin.png"` or `"https://cdn/x.png"`              | Used when `marker_icon="custom"`                                      |
| `kml`            | KML layer URL                       | URL/path, e.g. `"/media/routes/river.kml"`                                                                          | Loaded via `omnivore.kml()`                                           |
| `geojson`        | GeoJSON layer URL                   | URL/path, e.g. `"/media/routes/river.geojson"`                                                                      | Loaded via `L.geoJSON()`                                              |
| `markers`        | Multiple markers                    | Pipe-separated list, e.g. `"43.66,-116.24,Home,Hello,blue-marker.png\|43.67,-116.25,Camp,Overnight,red-marker.png"` | Per-marker icon overrides default icon                                |
| `map_container`* | Extra HTML attrs for container      | Key/values, e.g. `"class=mybox data-x=1"`                                                                           | *Advanced; parsed as key/value pairs                                  |

**`markers` format**
