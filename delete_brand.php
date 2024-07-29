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

// Handle deletion
$id = $_GET['deleteid'];

if ($id) {
    // First, delete dependent records in items
    $delete_items_sql = "DELETE FROM items WHERE brand_id=$id";
    $delete_items_result = mysqli_query($conn, $delete_items_sql);

    if ($delete_items_result === FALSE) {
        die("Error deleting dependent items: " . mysqli_error($conn));
    }

    // Now, delete the brand
    $delete_brand_sql = "DELETE FROM brands WHERE id=$id";
    $delete_brand_result = mysqli_query($conn, $delete_brand_sql);

    if ($delete_brand_result) {
    
        header('location:brand.php');
    } else {
        die("Error deleting brand: " . mysqli_error($conn));
    }
} else {
    echo "No ID specified for deletion.";
}
?>
