<?php
// Get the latitude and longitude of the starting point from the POST request
$start_lat = $_POST['start_lat'];
$start_lng = $_POST['start_lng'];

// Get the latitude and longitude of the destination from the POST request
$dest_lat = $_POST['dest_lat'];
$dest_lng = $_POST['dest_lng'];

// Define the OpenStreetMap API URL
$osm_url = "https://router.project-osrm.org/route/v1/driving/$start_lng,$start_lat;$dest_lng,$dest_lat?geometries=geojson";

// Send a request to the OpenStreetMap API and get the response
$osm_response = file_get_contents($osm_url);

// Parse the response as JSON
$osm_data = json_decode($osm_response);

// Extract the coordinates of the shortest path from the response
$shortest_path = $osm_data->routes[0]->geometry->coordinates;

// Convert the coordinates to an array of latitude and longitude objects
$latlngs = array();
foreach ($shortest_path as $coord) {
    $latlngs[] = array($coord[1], $coord[0]);
}

// Return the coordinates as JSON
echo json_encode($latlngs);
?>
