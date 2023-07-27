<?php
/**
 * Leaflet Map Plugin
 * Version: 1.0.5
 * Author: amaral <grant@lrio.com>
 * Website: http://lrio.com
 * Copyright © 2023 Amaral All Rights Reserved
 * License: GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html
 * Reference: https://github.com/jakecoll/mysql-to-geojson/blob/master/mysql-to-geojson.php
 */

// Include the PDO library
require_once '/www/rivers/includes/pdo.php';

// Define the SQL query
$sql = "SELECT * FROM rivers.idaho
        WHERE basin LIKE '%clear%'
        AND lat != 0
        ORDER BY name";

// Execute the SQL query
$result = $pdo->query($sql);
if (!$result) {
    echo 'An SQL error occurred.' . PHP_EOL;
    exit;
}

// Initialize the GeoJSON array
$geojson = [
    'type' => 'FeatureCollection',
    'features' => []
];

// Loop through the query results and build the GeoJSON array
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $properties = $row;
    unset($properties['lat']);
    unset($properties['lng']);
    $feature = [
        'type' => 'Feature',
        'geometry' => [
            'type' => 'Point',
            'coordinates' => [
                $row['lng'],
                $row['lat']
            ]
        ],
        'properties' => $properties
    ];
    $geojson['features'][] = $feature;
}

// Output the GeoJSON data
header('Content-type: application/json');
echo json_encode($geojson, JSON_PRETTY_PRINT);

// Close the PDO connection
$pdo = null;
