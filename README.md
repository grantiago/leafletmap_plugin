# leafletmap_plugin
plugin for displaying leaflet maps in joomla 4

leaflet system plugin. 

Supports multiple maps on one page in multiple articles.

Override defaults in plugin hook

usage: `{leafletmap lat="44.66" lng="-116.23" marker_title="grant house"}`

or: `{leafletmap}`

multi language: en-GB, es_ES

select basemaps: osm, opentopo

## toDo

- [x] marker state onload
- [x] toggle scroll wheel zoom
- [ ] geojson feature
- [ ] add checksum to update.xml

## v 0.0.3 added full screen plugin

- added kml layer
- omnivore plugin js and css
- fit bounds on kml layer
- changed the container name from leaflet-map to map to remove all the instances of str_replace
- + joomla update system

## v 0.0.2 added full screen plugin

- all the css and js are in the plugin dir
- the language files are installed in the admin/language/ll-LL dirs automatically
- added the full screen js and css
- moved the leaflet images dir to css/images
- added fieldset groups for description and settings
- changed the names of the language files replacing the en-GB. etc prefix with plg_system_xxx
- added a field to select the basemap in the xml. not implemented in the php yet.
- added z index to custom css to keep the menu infront of map

## v 0.0.1

initial release.
