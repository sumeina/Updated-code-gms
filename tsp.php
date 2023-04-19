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
