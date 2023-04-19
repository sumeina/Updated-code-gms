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


	</script>


</body>

</html>
