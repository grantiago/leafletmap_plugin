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

| Setting                     | Options |
|--------------------------|---------|
| Latitude                 | 43.66
| Longitude                | -116.23
| Zoom                     | 1-15
| Marker Title             | Map Marker
| Icon Color / URL         | blue, red `/custom/URL`
| Basemap                  | Open Street Map|
|                          | ESRI Satellite
|                          | ESRI Topo
|                          | Open Topo
| Open marker title on load| Yes/No
| Map Container Width      |% or px
| Map Container Height     |% or px
| Enable the KML feature   |/en the future
| Disable the Zoom Wheel   | Yes/No
| URL to the KML file      | `/kml/staircase.kml`
| URL to the geoJson file  | set this in the hook.
| Display the flag. (CSS)  | Yes/No

## Links

[changelog.md](./changelog.md)

[Releases/Downloads](https://github.com/grantiago/leafletmap_plugin/tags)
