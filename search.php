<?php
// include 'includes/db_connect.php';
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Retrieve and sanitize form data
$checkin = $_POST['checkin'];
$checkout = $_POST['checkout'];

// Validate date inputs
if (empty($checkin) || empty($checkout)) {
    die("Please provide both check-in and check-out dates.");
}

// Convert to date format for SQL query
$checkin = $conn->real_escape_string($checkin);
$checkout = $conn->real_escape_string($checkout);

// Query to find available rooms
$sql = "
    SELECT * FROM tblrooms
    WHERE available_from <= '$checkin' AND available_to >= '$checkout'
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h1>Available Rooms</h1>";
    echo "<table border='1'>
            <tr>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Price</th>
                <th>Available From</th>
                <th>Available To</th>
            </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['room_number']) . "</td>
                <td>" . htmlspecialchars($row['room_type']) . "</td>
                <td>$" . htmlspecialchars($row['price']) . "</td>
                <td>" . htmlspecialchars($row['available_from']) . "</td>
                <td>" . htmlspecialchars($row['available_to']) . "</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "No rooms available for the selected dates.";
}

// Close the database connection
$conn->close();
?>
