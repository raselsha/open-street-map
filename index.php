<?php
/**
 * Support doc
 * https://behind-the-code.blogspot.com/2022/07/openstreet-map-jquery-codeingiter-php.html
 * https://youtu.be/wwXXe6sa7Dk
 */

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="Control.FullScreen.css" />
    <link rel="stylesheet" href="leaflet.css" /> 
    <script src="leaflet.js"></script> 
    <link rel="stylesheet" href="autocomplete.min.css" /> 
    <script src="autocomplete.min.js"></script>
    <script src="Control.FullScreen.js"></script>
    <style type="text/css">
      .map {
        height: 100%;
        width: 100%;
      }
      .leaflet-touch .fullscreen-icon {
        background-position: 2px -9px;
      }
      .leaflet-touch .fullscreen-icon.leaflet-fullscreen-on {
        background-position: 2px -10px;
      }
    </style>
</head>
<body>
    <h4>Open Street Map API</h4>
    <div class="auto-search-wrapper loupe ">
      <input type="text" autocomplete="off" id="search" class="form-control" placeholder="enter the city name" value="Dhaka Bangladesh" />
    </div>
    <div style="max-width: 100%;height: 400px">
      <div id="map" class="map"></div>
    </div>

    <script type="text/javascript">
    new Autocomplete("search", {
        selectFirst: true,
        insertToInput: true,
        cache: true,
        howManyCharacters: 2,

        // onSearch
        onSearch: ({ currentValue }) => {
            const api = `https://nominatim.openstreetmap.org/search?format=geojson&limit=5&city=${encodeURI(currentValue)}`;
            return new Promise((resolve) => {
                fetch(api)
                    .then((response) => response.json())
                    .then((data) => resolve(data.features))
                    .catch((error) => console.error(error));
            });
        },

        // onResults
        onResults: ({ currentValue, matches, template }) => {
            const regex = new RegExp(currentValue, "gi");
            return matches.length === 0
                ? template(`<li>No results found: "${currentValue}"</li>`)
                : matches.map((element) => `
                    <li>
                        <p>${element.properties.display_name.replace(regex, (str) => `<b>${str}</b>`)}</p>
                    </li>`
                ).join("");
        },

        // onSubmit
        onSubmit: ({ object }) => {
            map.eachLayer((layer) => {
                if (!!layer.toGeoJSON) {
                    map.removeLayer(layer);
                }
            });

            const { display_name } = object.properties;
            const [lng, lat] = object.geometry.coordinates;
            
            const marker = L.marker([lat, lng], { title: display_name });
            marker.addTo(map).bindPopup(display_name);
            map.setView([lat, lng], 8);
        },

        // onSelectedItem
        onSelectedItem: ({ index, element, object }) => {
            console.log("onSelectedItem:", { index, element, object });
        },

        // noResults
        noResults: ({ currentValue, template }) => template(`<li>No results found: "${currentValue}"</li>`),
    });

    // ----------------------- MAP CONFIGURATION -----------------------
    const config = {
        minZoom: 4,
        maxZoom: 18,
    };

    const zoom = 3;
    const lat = 23.74530525512296;
    const lng = 90.39619445800783;

    const map = L.map("map", config).setView([lat, lng], zoom);
    const marker = L.marker([lat, lng], '');
    marker.addTo(map).bindPopup('Dhaka, Bangladesh');

    var fsControl = L.control.fullscreen();
    map.addControl(fsControl);

    // Fullscreen event listeners
    map.on('enterFullscreen', () => console.log('Enter Fullscreen'));
    map.on('exitFullscreen', () => console.log('Exit Fullscreen'));

    map.on('click', (e) => {
        alert(`Lat, Lon: ${e.latlng.lat}, ${e.latlng.lng}`);
    });

    // Load and display tile layer
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);
</script>

</body>
</html>