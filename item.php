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

$sort_column = $_GET['sort'] ?? 'id';
$sort_order = $_GET['order'] ?? 'ASC';
$page = $_GET['page'] ?? 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

$sql = "SELECT items.id, items.name, items.amount, brands.name AS brand, models.name AS model, items.date_added 
        FROM items
        LEFT JOIN brands ON items.brand_id = brands.id
        LEFT JOIN models ON items.model_id = models.id
        ORDER BY $sort_column $sort_order
        LIMIT $offset, $items_per_page";
$result = $conn->query($sql);

$total_items_sql = "SELECT COUNT(*) as total FROM items";
$total_items_result = $conn->query($total_items_sql);
$total_items = $total_items_result->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);

$brands_sql = "SELECT id, name FROM brands";
$brands_result = $conn->query($brands_sql);
$brands = [];
while ($row = $brands_result->fetch_assoc()) {
    $brands[] = $row;
}

$models_sql = "SELECT id, name, brand_id FROM models";
$models_result = $conn->query($models_sql);
$models = [];
while ($row = $models_result->fetch_assoc()) {
    $models[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Items</title>

    <link href="assets/css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="assets/css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="assets/css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/lib/helper.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function sortTable(column) {
            let currentUrl = window.location.href;
            let url = new URL(currentUrl);
            let search_params = url.searchParams;
            let order = search_params.get('order') === 'ASC' ? 'DESC' : 'ASC';
            search_params.set('sort', column);
            search_params.set('order', order);
            window.location.href = url.toString();
        }

        function showPopover(action, id) {
            let modal = document.getElementById('itemModal');
            let form = document.getElementById('itemForm');
            let modalTitle = document.getElementById('modalTitle');
            if (action === 'add') {
                form.reset();
                form.action = 'add_edit_item.php';
                modalTitle.textContent = 'Add New Item';
                document.getElementById('item_id').value = '';
            } else if (action === 'edit') {
                form.action = 'add_edit_item.php';
                modalTitle.textContent = 'Edit Item';
                // Fetch the item details and populate the form
                fetch('get_item.php?id=' + id)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('item_id').value = data.id;
                        document.getElementById('itemName').value = data.name;
                        document.getElementById('itemAmount').value = data.amount;
                        document.getElementById('itemBrand').value = data.brand_id;
                        updateModelOptions(data.brand_id, data.model_id);
                    });
            }
            modal.style.display = 'block';
        }

        function closeModal() {
            document.getElementById('itemModal').style.display = 'none';
        }

        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this item?")) {
                window.location.href = `delete.php?type=item&id=${id}`;
            }
        }

        function updateModelOptions(brandId, selectedModelId = null) {
            let models = <?php echo json_encode($models); ?>;
            let modelSelect = document.getElementById('itemModel');
            modelSelect.innerHTML = '<option value="">Select Model</option>';
            models.forEach(model => {
                if (model.brand_id == brandId) {
                    let option = document.createElement('option');
                    option.value = model.id;
                    option.text = model.name;
                    if (selectedModelId && model.id == selectedModelId) {
                        option.selected = true;
                    }
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
    <!-- /# sidebar -->

    <div class="content-wrap">
        <div class="main">
            <div class="container-fluid">
                <div class="card">
                    <h4 class="card-title">Items</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th onclick="sortTable('id')">ID</th>
                                <th onclick="sortTable('name')">Name</th>
                                <th onclick="sortTable('amount')">Amount</th>
                                <th onclick="sortTable('brand')">Brand</th>
                                
                                <th onclick="sortTable('model')">Model</th>
                                <th onclick="sortTable('date_added')">Date Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['name'] ?></td>
                                <td><?= $row['amount'] ?></td>
                                <td><?= $row['brand'] ?></td>
                                <td><?= $row['model'] ?></td>
                                <td><?= $row['date_added'] ?></td>
                                <td>
                                    <button><a href="edit_item.php? updateid='<?= $row['id'] ?>'">Edit</a></button>
                                    <button onclick="confirmDelete(<?= $row['id'] ?>)">Delete</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
              
                <button onclick="redirectToPage()" class="btn btn-primary m-3">Add New Item</button>


<script>
function redirectToPage() {
    window.location.href = 'add_item.php'; // Replace with the URL of the page you want to redirect to
}
</script>
            </div>
            <div >
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?= $i ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
        </div>
    </div>

    <!-- <div id="itemModal" style="display:none;">
        <div>
            <div>
                <span id="modalTitle">Add New Item</span>
                <button onclick="closeModal()">Close</button>
            </div>
            <form id="itemForm" method="POST" action="add_edit_item.php">
                <input type="hidden" id="item_id" name="item_id">
                <label for="itemName">Name:</label><br>
                <input type="text" id="itemName" name="name" required><br>
                <label for="itemAmount">Amount:</label><br>
                <input type="number" id="itemAmount" name="amount" required><br>
                <label for="itemBrand">Brand:</label><br>
                <select id="itemBrand" name="brand_id" class="form-control" required onchange="updateModelOptions(this.value)">
                    <option value="">Select Brand</option>
                    <?php foreach ($brands as $brand): ?>
                    <option value="<?= $brand['id'] ?>"><?= $brand['name'] ?></option>
                    <?php endforeach; ?>
                </select><br>
                <label for="itemModel">Model:</label><br>
                <select id="itemModel" name="model_id" class="form-control" required>
                    <option value="">Select Model</option>
                </select><br><br>
                <input type="submit" value="Save">
            </form>
        </div>
    </div> -->

    <script src="assets/js/lib/jquery.min.js"></script>
    <script src="assets/js/lib/jquery.nanoscroller.min.js"></script>
    <script src="assets/js/lib/menubar/sidebar.js"></script>
    <script src="assets/js/lib/preloader/pace.min.js"></script>
    <script src="assets/js/scripts.js"></script>
</body>
</html>
