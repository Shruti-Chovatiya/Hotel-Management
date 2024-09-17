<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hotel";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize form inputs
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check if user exists
    $sql = "SELECT * FROM tblusers WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, start session and redirect to dashboard
            session_start();
            $_SESSION['username'] = $username;
            echo "Login successful! Welcome, " . htmlspecialchars($username);
            // Redirect to a dashboard or another page
            header('Location: index.html');
        } else {
            echo "Incorrect password. <a href='login.html'>Try again</a>";
        }
    } else {
        echo "No user found with that username. <a href='login.html'>Try again</a>";
    }
}

// Close connection
$conn->close();
?>
