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

// Define the cities and their coordinates
$cities = array(
    "Kathmandu Durbar Square" => array(27.7045, 85.3076),
    "Swayambhunath Stupa" => array(27.7146, 85.2896),
    "Pashupatinath Temple" => array(27.7109, 85.3483),
    "Boudhanath Stupa" => array(27.7214, 85.3624),
    "Thamel" => array(27.7154, 85.3098),
    "Lalitpur" => array(27.6586, 85.3262),
    "Patan" => array(27.6766, 85.3206),
    "Nagarkot" => array(27.7042, 85.5249),
    // "location" => array(lat, long),
);

// Define a function to calculate the distance between two cities
function distance($city1, $city2, $cities) {
    $lat1 = $cities[$city1][0];
    $lon1 = $cities[$city1][1];
    $lat2 = $cities[$city2][0];
    $lon2 = $cities[$city2][1];
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    return ($miles * 1.609344);
}

// Define a function to calculate the shortest distance between cities

function tsp($cities) {
  $numCities = count($cities);
  $allCities = array_keys($cities);
  $memo = array();

  function helper($start, $visited, $cities, &$memo) {
      if (count($visited) == count($cities)) {
          return array(array($start), 0);
      }
      $visited[] = $start;
      sort($visited);
      $visitedStr = implode(',', $visited);

      if (isset($memo[$visitedStr][$start])) {
          return $memo[$visitedStr][$start];
      }

      $distances = array();
      foreach ($cities as $city => $coords) {
          if (!in_array($city, $visited)) {
              $distances[$city] = distance($start, $city, $cities);
          }
      }

      $min = INF;
      $minPath = array();

      foreach ($distances as $city => $dist) {
          $newPath = helper($city, $visited, $cities, $memo);
          $newDist = $dist + $newPath[1];
          if ($newDist < $min) {
              $min = $newDist;
              $minPath = array_merge(array($start), $newPath[0]);
          }
      }

      $memo[$visitedStr][$start] = array($minPath, $min);
      return array($minPath, $min);
  }
  
  $shortestPath = helper($allCities[0], array(), $cities, $memo)[0];
  array_push($shortestPath, $allCities[0]);
  return $shortestPath;
}

// Call the TSP function to get the shortest path
$shortestPath = tsp($cities);

// Output the shortest path
echo "Shortest path: " . implode(" -> ", $shortestPath);


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Traveling Salesman Problem with Leaflet</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.css" />
</head>
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

		 // Step 1: Collect data
     var locations = [
            { name: "Kathmandu Durbar Square", lat: 27.7045, lon: 85.3076 },
            { name: "Swayambhunath Stupa", lat: 27.7146, lon: 85.2896 },
            { name: "Pashupatinath Temple", lat: 27.7109, lon: 85.3483 },
            { name: "Boudhanath Stupa", lat: 27.7214, lon: 85.3624 },
            { name: "Thamel", lat: 27.7154, lon: 85.3098 }
        ];
         // Step 2: Calculate distance matrix
         var distanceMatrix = [];
        for (var i = 0; i < locations.length; i++) {
            var row = [];
            for (var j = 0; j < locations.length; j++) {
                row[j] = haversine(locations[i], locations[j]);
            }
            distanceMatrix[i] = row;
        }
// Step 3: Generate permutations
var perms = permutations(locations.map(function(loc, index) { return index; }));

// Step 4: Find the shortest route
var shortestRoute = null;
var shortestDistance = Infinity;
perms.forEach(function(perm) {
    var distance = 0;
    for (var i = 0; i < perm.length - 1; i++) {
        distance += distanceMatrix[perm[i]][perm[i+1]];
    }
    // add the distance from the last location back to the starting location
    distance += distanceMatrix[perm[perm.length-1]][perm[0]];

    if (distance < shortestDistance) {
        shortestRoute = perm;
        shortestDistance = distance;
    }
});
// Step 5: Display the shortest route on the map
var map = L.map('map').setView([27.7, 85.3], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 19,
			attribution: 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
		}).addTo(map);
    
    
   
	</script>
</body>
</html>