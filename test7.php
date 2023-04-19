<?php
// Read the latitude and longitude values from the Leaflet map
$latArray = array();
$lngArray = array();
$shortestPathCoords = array();
$shortestDistance = 0;

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
function tsp($n, $latArray, $lngArray) {
    global $shortestDistance; // Declare $shortestDistance as global
    // The starting point is 0
    $path = array(0);
    $visited = array_fill(0, $n, false);
    $visited[0] = true;
    $minDist = PHP_INT_MAX;
    $shortestPath = array();
    tspRecursive($n, $latArray, $lngArray, $path, $visited, 0, 0, $minDist, $shortestPath);
    return $shortestPath;
}

function tspRecursive($n, $latArray, $lngArray, &$path, &$visited, $current, $distance, &$minDist, &$shortestPath) {
    if ($current == $n - 1) {
        // If all cities are visited, check if the distance is less than the minimum distance found so far
        $dist = $distance + distance($latArray[$path[$n-1]], $lngArray[$path[$n-1]], $latArray[0], $lngArray[0]);
        if ($dist < $minDist) {
            $minDist = $dist;
            if ($dist < $minDist) {
                $minDist = $dist;
                $shortestPath = $path;
                $shortestDistance = $minDist;
            }
            
        }
    } else {
        // If not all cities are visited, try to visit a new city
        for ($i = 1; $i < $n; $i++) {
            if (!$visited[$i]) {
                $visited[$i] = true;
                $path[$current+1] = $i;
                $dist = distance($latArray[$path[$current]], $lngArray[$path[$current]], $latArray[$i], $lngArray[$i]);
                tspRecursive($n, $latArray, $lngArray, $path, $visited, $current+1, $distance+$dist, $minDist, $shortestPath);
                $visited[$i] = false;
            }
        }
        
    }
}
// Calculate the shortest path and the minimum distance
$shortestPath = tsp(count($latArray), $latArray, $lngArray);

// Add the shortest path to the shortestPathCoords array
$shortestPathCoords = array();
foreach ($shortestPath as $city) {
    array_push($shortestPathCoords, array($latArray[$city], $lngArray[$city]));

}

?>
<p>Shortest Distance: <?php echo round($shortestDistance, 2); ?> km</p>


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
	</style>

</head>

<body>
	<div id="map" style="width:100%; height: 100vh"></div>
	<script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"></script>
	<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

<script>
   var map = L.map('map').setView([27.7172, 85.3240], 13);
   var fixedPoint = L.marker([27.7172, 85.3240]).addTo(map);
		mapLink = "<a href='http://openstreetmap.org'>OpenStreetMap</a>";
		L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', { attribution: 'Leaflet &copy; ' + mapLink + ', contribution', maxZoom: 18 }).addTo(map);

		var taxiIcon = L.icon({
			iconUrl: 'img/taxi.png',
			iconSize: [70, 70]
		})

		var marker = L.marker([27.7172, 85.3240], { icon: taxiIcon }).addTo(map);

		var waypoints = [];

		map.on('click', function (e) {
			console.log(e)
			var newMarker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map);
			waypoints.push(L.latLng(e.latlng.lat, e.latlng.lng));
			if (waypoints.length > 1) {
				L.Routing.control({
					waypoints: waypoints
				}).on('routesfound', function (e) {
					var routes = e.routes;
					console.log(routes);

					e.routes[0].coordinates.forEach(function (coord, index) {
						setTimeout(function () {
							marker.setLatLng([coord.lat, coord.lng]);
						}, 100 * index)
					})

				}).addTo(map);
			}
		});

        function displayShortestPath() {
  // Retrieve the map object and the shortestPathCoords array
  var map = L.map('map');
  var shortestPathCoords = <?php echo json_encode($shortestPathCoords); ?>;

  // Create a Leaflet polyline object to represent the shortest path
  var shortestPath = L.polyline(shortestPathCoords, {color: 'red'}).addTo(map);

  // Zoom the map to fit the shortest path
  map.fitBounds(shortestPath.getBounds());
  // Create a new layer for the shortest path
var shortestPathLayer = L.layerGroup();

// Add the shortest path to the layer
for (var i = 0; i < shortestPathCoords.length - 1; i++) {
  var startPoint = L.latLng(shortestPathCoords[i][0], shortestPathCoords[i][1]);
  var endPoint = L.latLng(shortestPathCoords[i+1][0], shortestPathCoords[i+1][1]);
  var pathLine = L.polyline([startPoint, endPoint], {color: 'red'}).addTo(shortestPathLayer);
}

// Add the layer to the map
shortestPathLayer.addTo(map);

}

	</script>
    <script>
	// Create a Leaflet map centered at the first point in the shortest path
    var map = L.map('map').setView([<?php echo $latArray[0]; ?>, <?php echo $lngArray[0]; ?>], 13);

// Add the OpenStreetMap base tile layer to the map
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19
}).addTo(map);

// Create a Leaflet polyline using the shortest path coordinates
var shortestPathCoords = <?php echo json_encode($shortestPathCoords); ?>;
var polyline = L.polyline(shortestPathCoords, {color: 'red'}).addTo(map);

// Fit the map bounds to the polyline
map.fitBounds(polyline.getBounds());
var control = L.Routing.control({
    waypoints: shortestPathCoords.map(function(coords) {
        return L.latLng(coords[0], coords[1]);
    }),
    routeWhileDragging: false,
    router: L.Routing.osrmv1({
        serviceUrl: 'http://router.project-osrm.org/route/v1'
    }),
    lineOptions: {
        addWaypoints: false
    }
}).addTo(map);

</script>
<button onclick="displayShortestPath()">Display Shortest Path</button>

</body>

</html>
