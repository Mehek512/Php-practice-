<?php
$host = 'localhost';
$db = 'phppractice';
$user = 'root';
$pass = '';

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if (isset($_GET['deleteid'])) {
    $id = intval($_GET['deleteid']); // Ensuring $id is treated as an integer

    // Debugging: print the received id
    echo "Received ID: $id<br>";

    // Delete query
    $delete_sql = "DELETE FROM models WHERE id=$id"; 
    echo "Executing query: $delete_sql<br>"; // Debugging: print the query
    $result = mysqli_query($conn, $delete_sql);

    if ($result) {
    
        header('Location: modal.php');
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request";
}

$conn->close();
?>
