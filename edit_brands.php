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
$id = $_GET['updateid'];
$sql = "SELECT * FROM brands WHERE id=$id";
$result = mysqli_query($conn, $sql);

// Check if the query was successful
if (!$result) {
    die("Error fetching brand: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);

$name = $row['name'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];

    // Update SQL query
    $update_sql = "UPDATE brands SET name='$name' WHERE id=$id"; 
    $result = mysqli_query($conn, $update_sql);
    if ($result) {
      
        header('location:brand.php');
    } else {
        die(mysqli_error($conn));
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit Brand</title>

<!-- Styles -->
<link href="assets/css/lib/font-awesome.min.css" rel="stylesheet">
<link href="assets/css/lib/menubar/sidebar.css" rel="stylesheet">
<link href="assets/css/lib/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/lib/helper.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="sidebar sidebar-hide-to-small sidebar-shrink sidebar-gestures">
    <div class="nano">
        <div class="nano-content">
            <div class="logo"><span>Practice</span></div>
            <ul>
                <li><a href="item.php">Items</a></li>
                <li><a href="brand.php">Brands</a></li>
                <li><a href="model.php">Modal</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="card">
                <h4 class="card-title">
                    Edit Brand
                </h4>
            </div>
            <form method="POST" class="form-group">
                <label for="brandName" class="form-group">Name:</label><br>
                <input type="text" id="brandName" name="name" required class="form-control" value="<?php echo $name ?>"><br><br>
                <input type="submit" value="Update" class="btn btn-primary" name="submit"> 
            </form>
        </div>
    </div>
</div>

<script src="assets/js/lib/jquery.min.js"></script>
<script src="assets/js/lib/jquery.nanoscroller.min.js"></script>
<script src="assets/js/lib/menubar/sidebar.js"></script>
<script src="assets/js/lib/preloader/pace.min.js"></script>
<script src="assets/js/scripts.js"></script>
</body>
</html>
