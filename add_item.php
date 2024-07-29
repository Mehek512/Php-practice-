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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $amount = $_POST['amount'];
    $brand_id = $_POST['brand_id'];
    $model_id = $_POST['model_id'];

    $insert_sql = "INSERT INTO items (name, amount, brand_id, model_id) VALUES ('$name','$amount', '$brand_id','$model_id')";
    $result = mysqli_query($conn, $insert_sql);
    if($result){
       header('location:item.php');
    }
    else{
        die(mysqli_error($conn));
    }
}

// Fetch brands
$brands_sql = "SELECT id, name FROM brands";
$brands_result = $conn->query($brands_sql);
$brands = [];
while ($row = $brands_result->fetch_assoc()) {
    $brands[] = $row;
}

// Fetch models
$models_sql = "SELECT id, name, brand_id FROM models";
$models_result = $conn->query($models_sql);
$models = [];
while ($row = $models_result->fetch_assoc()) {
    $models[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Add Items</title>

<!-- ================= Favicon ================== -->
<!-- Standard -->
<link rel="shortcut icon" href="http://placehold.it/64.png/000/fff">

<!-- Styles -->
<link href="assets/css/lib/font-awesome.min.css" rel="stylesheet">
<link href="assets/css/lib/menubar/sidebar.css" rel="stylesheet">
<link href="assets/css/lib/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/lib/helper.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
<script>
    function updateModelOptions(brandId) {
        let models = <?php echo json_encode($models); ?>;
        let modelSelect = document.getElementById('itemModel');
        modelSelect.innerHTML = '<option value="">Select Model</option>';
        models.forEach(model => {
            if (model.brand_id == brandId) {
                let option = document.createElement('option');
                option.value = model.id;
                option.text = model.name;
                modelSelect.appendChild(option);
            }
        });
    }
</script>
</head>
<body>

<div class="sidebar sidebar-hide-to-small sidebar-shrink sidebar-gestures">
    <div class="nano">
        <div class="nano-content">
            <div class="logo"><span>Practice</span></div>
            <ul>
                <li><a href="item.php">Items</a></li>
                <li><a href="brand.php">Brands</a></li>
                <li><a href="modal.php">Models</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="card">
                <h4 class="card-title">
                    Add New Item
                </h4>
            </div>
            <form method="POST" class="form-group">
                <label for="itemName" class="form-group">Name:</label><br>
                <input type="text" id="itemName" name="name" required class="form-control"> <br>
                <label for="itemAmount" class="form-group">Amount:</label><br>
                <input type="number" id="itemAmount" name="amount" required class="form-control"><br>
                <label for="itemBrand" class="form-group">Brand:</label><br>
                <select id="itemBrand" name="brand_id" class="form-control" required onchange="updateModelOptions(this.value)">
                    <option value="">Select Brand</option>
                    <?php foreach ($brands as $brand): ?>
                    <option value="<?= $brand['id'] ?>"><?= $brand['name'] ?></option>
                    <?php endforeach; ?>
                </select><br>
                <label for="itemModel">Model:</label><br>
                <select id="itemModel" name="model_id" class="form-control">
                    <option value="">Select Model</option>
                    <?php foreach ($models as $model): ?>
                    <option value="<?= $model['id'] ?>"><?= $model['name'] ?></option>
                    <?php endforeach; ?>
                </select><br><br>
                <input  type="submit" value="Save" class="btn btn-primary " name="submit"> 
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
