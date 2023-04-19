<?php
// Get the coordinates from the GET parameters
$lat = $_GET['lat'];
$lng = $_GET['lng'];

// Perform the TSP algorithm calculation using the passed coordinates
// Replace this with your actual TSP algorithm implementation
$tsp_coordinates = [
    ['lat' => 27.7172, 'lng' => 85.3240],
    ['lat' => $lat, 'lng' => $lng]
];

// Return the TSP algorithm output as JSON data
echo json_encode($tsp_coordinates);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Geolocation</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />

    <style>
        body {
            margin: 0;
            padding: 0;


        }

        html {
            overflow-y: scroll;
        }
    </style>

</head>

<body>
    <div id="map" style="width:99%; height: 100vh"></div>
    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>


    <script>

        var map = L.map('map').setView([27.7172, 85.3240], 13);
        mapLink = "<a href='http://openstreetmap.org'>OpenStreetMap</a>";
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', { attribution: 'Leaflet &copy; ' + mapLink + ', contribution', maxZoom: 18 }).addTo(map);

        var taxiIcon = L.icon({
            iconUrl: 'img/taxi.png',
            iconSize: [70, 70]
        })

        var marker = L.marker([27.7172, 85.3240], { icon: taxiIcon }).addTo(map);

        map.on('click', function (e) {
            console.log(e)
            var newMarker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map);
            // Get the latitude and longitude of the clicked point
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;
            console.log("Clicked point: " + lat + ", " + lng);
            // Create a marker for the clicked point
            var newMarker = L.marker([lat, lng]).addTo(map);

            L.Routing.control({
                waypoints: [
                    L.latLng(27.7172, 85.3240),
                    L.latLng(e.latlng.lat, e.latlng.lng)
                ]
            }).on('routesfound', function (e) {
                var routes = e.routes;
                console.log(routes);

                e.routes[0].coordinates.forEach(function (coord, index) {
                    setTimeout(function () {
                        marker.setLatLng([coord.lat, coord.lng]);
                    }, 100 * index)
                });

                // Send AJAX request to PHP script
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        console.log(this.responseText);
                        // Parse the response from the PHP script
                        var response = JSON.parse(this.responseText);
                        // Do something with the response, for example, update the marker locations based on the TSP algorithm output
                        response.forEach(function(coord, index) {
                            setTimeout(function() {
                                marker.setLatLng([coord.lat, coord.lng]);
                            }, 100 * index);
                        });
                    }
                };
                xhttp.open("GET", "calculate-tsp.php?lat=" + lat + "&lng=" + lng, true);
                xhttp.send();
            }).addTo(map);
        });


    </script>


</body>

</html>
