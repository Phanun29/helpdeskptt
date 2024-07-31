<?php
include "config.php";

if (isset($_POST['station_id'])) {
    $station_id = $_POST['station_id'];

    // Escape the input to prevent SQL injection
    $station_id = $conn->real_escape_string($station_id);

    $query = "SELECT station_name, station_type, province FROM tbl_station WHERE station_id LIKE '%$station_id%'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(array('success' => true, 'station_name' => $row['station_name'], 'station_type' => $row['station_type'], 'province' => $row['province']));
    } else {
        echo json_encode(array('success' => false));
    }
}
