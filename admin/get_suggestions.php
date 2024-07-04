<?php
include 'config.php';

$q = $_REQUEST["q"];

$sql = "SELECT station_id, station_name FROM tbl_station WHERE station_id LIKE '%" . $q . "%' OR station_name LIKE '%" . $q . "%'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<p onclick=\"selectSuggestion('" . $row['station_id'] . "')\">" . $row['station_id'] . " - " . $row['station_name'] . "</p>";
    }
} else {
    echo "<p>No Station ID found</p>";
}
$conn->close();
