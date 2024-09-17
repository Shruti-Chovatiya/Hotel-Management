<?php
session_start();
// Database connection
$host = 'localhost';
$user = 'root'; // Replace with your MySQL username
$password = ''; // Replace with your MySQL password
$dbname = 'hotel'; // Replace with your database name

// Create a connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form inputs
    $guest_name = mysqli_real_escape_string($conn, $_POST['name']);
    $guest_phone = mysqli_real_escape_string($conn, $_POST['mobile']);
    $guest_email = mysqli_real_escape_string($conn, $_POST['email']);
    $check_in_date = mysqli_real_escape_string($conn, $_POST['checkin']);
    $check_out_date = mysqli_real_escape_string($conn, $_POST['checkout']);
    $num_adults = (int) mysqli_real_escape_string($conn, $_POST['adults']);
    $num_kids = (int) mysqli_real_escape_string($conn, $_POST['kids']);
    $room_type = mysqli_real_escape_string($conn, $_POST['room']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment-method']);
    // $deposit = (float) mysqli_real_escape_string($conn, $_POST['deposit']);
    // $booking_status = (int) mysqli_real_escape_string($conn, $_POST['status']);

    // Set prices and calculate deposit
    $room_prices = [
        'single-bed' => 2500,  // Price for single-bed room
        'queen-size' => 3000,  // Price for queen-size room
        'king-size' => 5000    // Price for king-size room
    ];

    // Calculate number of nights for the stay
    $checkin = new DateTime($check_in_date);
    $checkout = new DateTime($check_out_date);
    $interval = $checkin->diff($checkout);
    $num_nights = $interval->days;

    // Calculate total price based on the selected room type
    if (array_key_exists($room_type, $room_prices)) {
        // $price_per_night = $room_prices[$room_type];
        $total_price = $room_prices[$room_type];
        $calculated_deposit = $total_price * 0.25; // 25% of the total price
    } else {
        echo "Invalid room type.";
        exit();
    }

    $_SESSION['price'] = $total_price;
    $_SESSION['deposit'] = $calculated_deposit;

    // If deposit is not provided or incorrect, use calculated deposit
    if (empty($deposit) || $deposit != $calculated_deposit) {
        $deposit = $calculated_deposit;
    }

    // Insert booking data into the bookings table
    $sql = "INSERT INTO tblbookings (guest_name, guest_phone, guest_email, check_in_date, check_out_date, num_adults, num_children, room_type, payment_method, total_price, deposit, booking_status) 
            VALUES ('$guest_name', '$guest_phone', '$guest_email', '$check_in_date', '$check_out_date', '$num_adults', '$num_kids', '$room_type', '$payment_method', '$total_price', '$deposit', 'Pending')";

    // Execute the query and check for success
    if ($conn->query($sql) === TRUE) {
        echo "Booking successful! Total Price: $$total_price, Deposit: $$deposit";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the connection
$conn->close();
?>