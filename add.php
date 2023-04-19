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
	<button onclick="solveTsp()">Solve TSP</button>

	<script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>
	<script src="https://raw.githubusercontent.com/google/or-tools/stable/js/or-tools_js.js"></script>
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

		var tspSolver = new ORTools.TspSolver();
		var distanceMatrix;
        var polyline = null;

		function solveTsp() {
    // get the coordinates of the markers on the map
    var markerList = [];
    map.eachLayer(function(layer) {
        if (layer instanceof L.Marker) {
            markerList.push(layer.getLatLng());
        }
    });

    // create the distance matrix using the coordinates
    var distanceMatrix = [];
    for (var i = 0; i < markerList.length; i++) {
        distanceMatrix[i] = [];
        for (var j = 0; j < markerList.length; j++) {
            if (i === j) {
                distanceMatrix[i][j] = 0;
            } else {
                var distance = markerList[i].distanceTo(markerList[j]);
                distanceMatrix[i][j] = distance;
                distanceMatrix[j][i] = distance;
            }
        }
    }

    // create the routing model and set the solver parameters
    var tsp = {
        distance_matrix: distanceMatrix,
        num_routes: 1,
        start_and_end: [0],
        optimize: true
    };
    var solver = new window.google.orTools.ConstraintSolver();
    var solutionCallback = function() {};
    var parameters = new window.google.orTools.SimpleSolverParameters();
    parameters.num_threads = 1;

    // solve the TSP and display the route on the map
    solver.solve(tsp, parameters, solutionCallback);
    var route = tsp.solution[0];
    var routeLatLngs = [];
    for (var i = 0; i < route.length; i++) {
        routeLatLngs.push(markerList[route[i]]);
    }
    var routePolyline = L.polyline(routeLatLngs, {color: 'red'}).addTo(map);
    map.fitBounds(routePolyline.getBounds());
}
if (tsp.solution) {
        // Remove the existing polyline, if any
        if (polyline) {
            polyline.removeFrom(map);
        }

        // Create a new polyline for the route
        var path = [];
        for (var i = 0; i < route.length; i++) {
            path.push(markers[route[i]].getLatLng());
        }
        polyline = L.polyline(path, {
            color: 'red',
            weight: 3,
            opacity: 0.5
        }).addTo(map);
    }
			
	</script>
</body>
</html>
