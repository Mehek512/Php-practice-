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

$id = isset($_GET['id']) ? intval($_GET['id']) : null; // Sanitize and validate ID
$type = isset($_GET['type']) ? $_GET['type'] : null; // Validate type

if ($id !== null && $type !== null) {
    if ($type == 'item') {
        $sql = "DELETE FROM items WHERE id=?";
    } 

    if (isset($sql)) {
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $id); // "i" indicates integer type for parameter binding

            if ($stmt->execute()) {
                header('location:item.php');
            } else {
                echo "Error deleting record: " . $stmt->error;
            }
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }
}

// Close connection
$conn->close();
?>
