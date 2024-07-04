<?php
include "config.php";

if (isset($_POST['station_id'])) {
    $station_id = $_POST['station_id'];

    $stmt = $conn->prepare("SELECT station_name, station_type FROM tbl_station WHERE station_id = ?");
    $stmt->bind_param("i", $station_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(array('success' => true, 'station_name' => $row['station_name'], 'station_type' => $row['station_type']));
    } else {
        echo json_encode(array('success' => false));
    }
}
