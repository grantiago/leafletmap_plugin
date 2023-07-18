## change log  <span style="font-size:small;">July 2023</span>

Leaflet Map Plugin

### v 1.0.4 - <span style="font-size:small;">2023-07-18</span>

- skipped the version numbering from 0.0.3 to 1.0.4
- fixed the joomla update system
  - error at xml file:  <updateservers> <server type="plugin" -> corrected: <server type=extension ...
- fixed the default basemap
- set the tag release system in github
-

### v 0.0.3 added full screen plugin - <span style="font-size:small;">2023-07-17</span>

- ➕ joomla update system
- 🗸 added zoom wheel toggle
- added kml layer
- omnivore plugin js and css
- fit bounds on kml layer
- changed the container name from leaflet-map to map to remove all the instances of str_replace
- added toggle setting to open the marker title on load
- added esri satellite base map

### v 0.0.2 added full screen plugin

- all the css and js are in the plugin dir
- the language files are installed in the admin/language/ll-LL dirs automatically
- added the full screen js and css
- moved the leaflet images dir to css/images
- added fieldset groups for description and settings
- changed the names of the language files replacing the en-GB. etc prefix with plg_system_xxx
- added a field to select the basemap in the xml. not implemented in the php yet.
- added z index to custom css to keep the menu infront of map

### v 0.0.1 <span style="font-size:small;">2023-07-12</span>

initial release.
