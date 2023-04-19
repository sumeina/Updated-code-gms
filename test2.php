<?php
// Read the latitude and longitude values from the Leaflet map
$latArray = array();
$lngArray = array();
$shortestPathCoords = array();
$shortestPath = array();

if (isset($_POST['latLngArray'])) {
    $latLngArray = json_decode($_POST['latLngArray'], true) ?? array();
    if (!empty($latLngArray)) {
        foreach ($latLngArray as $latLng) {
            if (!empty($latLng) && is_array($latLng) && count($latLng) > 0) {
                $lat = isset($latLng[0]) && !empty($latLng[0]) ? $latLng[0] : null;
                $lng = isset($latLng[1]) && !empty($latLng[1]) ? $latLng[1] : null;
                if (!is_null($lat)) {
                    array_push($latArray, $lat);
                }     
                if (!is_null($lng)) {
                    array_push($lngArray, $lng);
                }     
            }   
        }
    }
}

// Define a function to calculate the distance between two points
function distance($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 6371; // km
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $distance = $earthRadius * $c;
    return $distance;
}

// Define a function to calculate the total distance of a path
function pathDistance($path, $latArray, $lngArray) {
    $distance = 0;
    for ($i = 0; $i < count($path)-1; $i++) {
        if (isset($latArray[$path[$i]]) && isset($lngArray[$path[$i]]) && isset($latArray[$path[$i+1]]) && isset($lngArray[$path[$i+1]])) {
            $distance += distance($latArray[$path[$i]], $lngArray[$path[$i]], $latArray[$path[$i+1]], $lngArray[$path[$i+1]]);
        }
    }
    return $distance;
}
// Define a function to calculate the shortest path using the TSP algorithm

if (count($latArray) > 0 && count($lngArray) > 0) {
    $shortestPath = tsp(count($latArray), $latArray, $lngArray);
    // Display the shortest path
    echo "Your shortest path is: ";
    foreach ($shortestPath as $index) {
        if (isset($latArray[$index]) && isset($lngArray[$index])) {
            echo "(".$latArray[$index].", ".$lngArray[$index].") ";
        }
    }
    $distance = pathDistance($shortestPath, $latArray, $lngArray);
    echo "with a total distance of ".$distance." km.";
} else {
    echo "Please select at least one point on the map.";
}
foreach ($shortestPath as $index) {
    if (isset($latArray[$index]) && isset($lngArray[$index])) {
        echo "(".$latArray[$index].", ".$lngArray[$index].") ";
        array_push($shortestPathCoords, array($latArray[$index], $lngArray[$index]));
    }
}

// Display the shortest path


?>
<script>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Leaflet Map with Marker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <style>
        #map {
            height: 100%;
            width:100%;
        }
    </style>
</head>
<body>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

    <script>
        var map = L.map('map').setView([27.7172, 85.3240], 13);

        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: 'Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
            maxZoom: 18
        }).addTo(map);

        var truckIcon = L.icon({
            iconUrl: 'truck.jpg',
            iconSize: [25, 20]
        });

        var marker = L.marker([27.7172, 85.3240], { icon: truckIcon }).addTo(map);

        map.on('click', function (e) {
            console.log(e);
            var lat = e.latlng.lat;
            var lon = e.latlng.lng;
            console.log("Lat: "+ lat + "Long: "+ lon);
            var newMarker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map);
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
                newMarker.setLatLng(coord);
            }, index * 1000);
        });
    

            }).addTo(map);
        });
    

</script>

<script>
    function showShortestPath() {
        // Display the shortest path
        var shortestPath = <?php echo json_encode($shortestPathCoords); ?>;
        var polyline = L.polyline(shortestPath, {color: 'red'}).addTo(map);
        map.fitBounds(polyline.getBounds());
    }
</script>

<button id="shortestPathButton" onclick="showShortestPath()">Click to view the shortest distance</button>

</body>
</html>
