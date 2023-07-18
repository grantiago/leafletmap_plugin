## leaflet system plugin

- Supports multiple maps on one page in multiple articles.
- Override defaults in plugin hook
- usage: `{leafletmap lat="44.66" lng="-116.23" marker_title="grant house"}`
  - or: `{leafletmap}`
- multi language: en-GB, es_ES
- select basemaps: osm, opentopo
- display kml files
- toggle zoom with scroll wheel
- toggle title open/close on load
- override all settings in the hook
- set the map height and width

### toDo

- [x] marker state onload
- [x] toggle scroll wheel zoom
- [ ] geojson feature -- duplicate kml layer
- [x] add checksum to update.xml on github
- [ ] map alignment on page
- [ ] fix the language files
- [ ] re-upload all files to github
- [ ] clone it here

[changelog.md](./changelog.md)

### notes

[Joomla Deploying Update Sever](https://docs.joomla.org/Deploying_an_Update_Server)

[Adding_an_update_server](https://docs.joomla.org/Special:MyLanguage/J3.x:Developing_an_MVC_Component/Adding_an_update_server)

### updating

- any changes zip them in a file.
- create an new tag - upload on github.
- add a desc.
- upload the zip.
- modify the update.xml to the new version, url and checksum
- get the checksum locally -> sha256sum <filename>
- add it to update.xml
