<?php
// start a session to store user data
session_start();

// check if the admin is not logged in
if (!isset($_SESSION["name"])) {
    // if the admin is not logged in, redirect to the login page
    header("Location: admindashboard.php");

    exit(); // exit the script to prevent further execution
}

// set the value of $name variable
$name = $_SESSION["name"];
?>
<?php

function getDistance($lat1, $lon1, $lat2, $lon2) {
    // function to calculate distance between two latitude and longitude points using Haversine formula
    $R = 6371; // Earth's radius in km
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $lat1 = deg2rad($lat1);
    $lat2 = deg2rad($lat2);

    $a = sin($dLat / 2) * sin($dLat / 2) + sin($dLon / 2) * sin($dLon / 2) * cos($lat1) * cos($lat2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $R * $c; // distance in km
}

// Array of street names and their corresponding addresses in Kathmandu
$street_addresses = array(
    "Kathmandu Durbar Square" => "Kathmandu Durbar Square, Kathmandu, Nepal",
    "Swayambhunath Stupa" => "Swayambhunath Stupa, Kathmandu, Nepal",
    "Pashupatinath Temple" => "Pashupatinath Temple, Kathmandu, Nepal",
    "Boudhanath Stupa" => "Boudhanath Stupa, Kathmandu, Nepal"
);

// Retrieve latitude and longitude coordinates for each street address using Google Maps API
$lat_long = array();
foreach ($street_addresses as $street => $address) {
    $address = str_replace(" ", "+", $address);
    $json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?key=YOUR_API_KEY&address=$address");
    $json = json_decode($json);
    $lat = $json->results[0]->geometry->location->lat;
    $lng = $json->results[0]->geometry->location->lng;
    $lat_long[$street] = array($lat, $lng);
}

// Solve TSP problem using nearest neighbor algorithm
$visited = array_keys($lat_long);
$start = array_shift($visited);
$current = $start;
$total_distance = 0;
$path = array($start);
while (!empty($visited)) {
    $min_distance = INF;
    foreach ($visited as $next) {
        $distance = getDistance($lat_long[$current][0], $lat_long[$current][1], $lat_long[$next][0], $lat_long[$next][1]);
        if ($distance < $min_distance) {
            $min_distance = $distance;
            $closest = $next;
        }
    }
    $current = $closest;
    $total_distance += $min_distance;
    $path[] = $closest;
    $key = array_search($closest, $visited);
    unset($visited[$key]);
}
$total_distance += getDistance($lat_long[$current][0], $lat_long[$current][1], $lat_long[$start][0], $lat_long[$start][1]);
$path[] = $start;

// Print shortest path and total distance
echo "Shortest Path: " . implode(" -> ", $path) . "<br>";
echo "Total Distance: "
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Traveling Salesman Problem with Leaflet</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://raw.githubusercontent.com/google/or-tools/stable/js/or-tools_js.js"></script>

    <style>
      /* Navbar styles */
.navbar {
    background-color: #333;
    overflow: hidden;
    height: 70px ;
}

.navbar a {
    float: left;
    color: #f2f2f2;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
    font-size: 17px;
}

.navbar a.active {
    background-color: #4CAF50;
    color: white;
}

.navbar-right {
    float: right;
}

/* New styles for centering welcome message */
.navbar-center {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
}

.navbar-center p {
    display: inline-block;
    margin: 0;
    color: #f2f2f2;
    font-size: 17px;
    padding: 14px 16px;
    text-align: center;
}

.navbar-right a {
    color: #f2f2f2;
    text-align: center;
    padding: 14px 16px;
    text-decoration: none;
    font-size: 17px;
}

    </style>

    <!-- Navbar -->
    <div class="navbar">
    <div class="navbar-left">
        <a class="active" href="#">Admin Dashboard</a>
    </div>
    <div class="navbar-center">
        <p>Welcome, <?php echo $name; ?>!</p>
    </div>
    <div class="navbar-right">
        <a href="logout.php">Logout</a>
    </div>
</div>

    <style>
        
        /* Side dashboard styles */
        .sidenav {
            height: 100%;
            width: 200px;
            position: fixed;
            z-index: 1;
            top: 80px;
            left: 0;
            background-color: #111;
            overflow-x: hidden;
            padding-top: 30px;
        }
        .sidenav a {
            padding: 6px 8px 6px 16px;
            text-decoration: none;
            font-size: 20px;
            color: #818181;
            display: block;
            padding-top: 20px;
        }
        .sidenav a:hover {
            color: #f1f1f1;
            padding-top: 20px;
        }
        .main {
            margin-left: 200px; /* Same as the width of the sidenav */
            padding: 0px 10px;
            width: calc(100% - 220px); /* 100% of the available width minus the width of the sidenav plus a small margin */

        }
        /* Content styles */
        h1 {
            margin-top: 50px;
        }
       
    </style>
    <!-- Side dashboard -->
    
    <div class="sidenav">
  <br>
  <a href="admindashboard.php"><i class="fas fa-map-marker-alt"></i> Map</a><br>
  <a href="add.php"><i class="fas fa-plus-circle"></i> Add/Remove Points</a><br>
  <a href="a.php"><i class="fas fa-users"></i> User Management</a><br>
  <a href="#"><i class="fas fa-chart-bar"></i> Analytics</a><br>
  <a href="#"><i class="fas fa-bell"></i> Notifications</a><br>
  <a href="#"><i class="fas fa-cog"></i> Settings</a><br>
  <a href="#"><i class="fas fa-question-circle"></i> Help and Support</a>
</div>

    </div>

    
	<style>
		html, body{
			height: 100%;
			margin: 0;
			padding: 0;
		}
        #map {
        position: absolute;
        top: 80px;
        right: 0;
        bottom: 0;
        left: 220px; /* Same as the width of the sidenav plus a small margin */
    }
        
	</style>

<body>
	<div id="map"></div>
	

	<script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>
	
	<script>
		var map = L.map('map').setView([27.7172, 85.3240], 13);

		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 19,
			attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
		}).addTo(map);

		var markers = [];

		function addMarker(location) {
			var marker = L.marker(location).addTo(map);
			markers.push(marker);
		}

		map.on('click', function(e) {
			addMarker(e.latlng);
		});


        

    
			
	</script>
</body>
</html>
