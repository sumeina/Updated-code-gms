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
    <title>Admin Dashboard</title>
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
</head>
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
        }
        /* Content styles */
        h1 {
            margin-top: 50px;
        }
        #map {
            height: 500px;
        }
    </style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js" integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM=" crossorigin=""></script>

    <!-- Side dashboard -->
    <div class="sidenav">
    <br>
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

    <div class="main">
        <h1>Map</h1>
       
        ///

        ////
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
		mapLink = "<a href='http://openstreetmap.org'>OpenStreetMap</a>";
		L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', { attribution: 'Leaflet &copy; ' + mapLink + ', contribution', maxZoom: 18 }).addTo(map);

		var truckIcon = L.icon({
			iconUrl: 'truck.jpg',
			iconSize: [25, 20]
		})

		var marker = L.marker([27.7172, 85.3240], { icon: truckIcon }).addTo(map);

		map.on('click', function (e) {
			console.log(e)
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
						marker.setLatLng([coord.lat, coord.lng]);
					}, 100 * index)
				})

			}).addTo(map);
		});


	</script>


</html>
<head>