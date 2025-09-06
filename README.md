# leaflet system plugin

## Description

The LeafletMap system plugin supports <strong>multiple maps on one page in multiple articles.</strong> The plugin renders both KML and geoJson. Choose between Base Layers from Open Street Maps, ESRI, OpenTopoMap.

The Leaflet JS and css are loaded once in the header when required. The map initialization script is loaded in the content. This allows for more than one map, in one or more articles, on one page. A blog Layout on the home page would be an example. A category list would be another use.

## Usage

- `{leafletmap lat="44.66" lng="-116.23" marker_title="grant house"}` <em>overrides in the hook</em>
- `{leafletmap}` <em>defaults from the admin settings</em>

## Features

- Supports multiple maps on one page in multiple articles.
- Override defaults in plugin hook
- multi language: en-GB, es_ES
- select basemaps: osm, opentopo, ESRI Sat, ESRI Topo
- display kml files
- display geojson files
- toggle zoom with scroll wheel
- toggle title open/close on load
- override all settings in the hook
- set the map height and width
- responisive

## Settings

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

## Links

[changelog.md](./changelog.md)

[Releases/Downloads](https://github.com/grantiago/leafletmap_plugin/tags)
