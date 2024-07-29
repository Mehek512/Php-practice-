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

$brands_sql = "SELECT brands.id, brands.name, 
              (SELECT COUNT(*) FROM items WHERE items.brand_id = brands.id) AS items_count, 
              (SELECT COUNT(*) FROM models WHERE models.brand_id = brands.id) AS models_count
              FROM brands
              ORDER BY $sort_column $sort_order
              LIMIT $offset, $items_per_page";
$brands_result = $conn->query($brands_sql);

if (!$brands_result) {
    die("Query failed: " . $conn->error);
}

$total_brands_sql = "SELECT COUNT(*) as total FROM brands";
$total_brands_result = $conn->query($total_brands_sql);
$total_brands = $total_brands_result->fetch_assoc()['total'];
$total_pages = ceil($total_brands / $items_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Brands</title>
    <link href="assets/css/lib/font-awesome.min.css" rel="stylesheet">
    <link href="assets/css/lib/menubar/sidebar.css" rel="stylesheet">
    <link href="assets/css/lib/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/lib/helper.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
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
            let modal = document.getElementById('brandModal');
            let form = document.getElementById('brandForm');
            let modalTitle = document.getElementById('modalTitle');
            if (action === 'add') {
                form.reset();
                form.action = 'add_edit_brand.php';
                modalTitle.textContent = 'Add New Brand';
            } else if (action === 'edit') {
                form.action = 'add_edit_brand.php?id=' + id;
                modalTitle.textContent = 'Edit Brand';
                // Fetch the brand details and populate the form
                fetch('get_brand.php?id=' + id)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('brandName').value = data.name;
                    });
            }
            modal.style.display = 'block';
        }

        function closeModal() {
            document.getElementById('brandModal').style.display = 'none';
        }

        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this Brand?")) {
                window.location.href = `delete_brand.php?deleteid=${id}`;
            }
        }
        function confirmUpdate(id) {
            if (confirm("Are you sure you want to Update this Brand?")) {
                window.location.href = `edit_brands.php?updateid=${id}`;
            }
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
                    <h4 class="card-title">Brands</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th onclick="sortTable('id')">ID</th>
                                <th onclick="sortTable('name')">Name</th>
                                <th onclick="sortTable('items_count')">Items Count</th>
                                <th onclick="sortTable('models_count')">Models Count</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $brands_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= $row['name'] ?></td>
                                    <td><?= $row['items_count'] ?></td>
                                    <td><?= $row['models_count'] ?></td>
                                    <td>
                                        <button onclick="confirmUpdate(<?= $row['id'] ?>)">Edit</button>
                                        <button onclick="confirmDelete(<?= $row['id'] ?>)">Delete</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <button onclick="redirectToPage()" class="btn btn-primary m-3">Add New Brand</button>


<script>
function redirectToPage() {
    window.location.href = 'add_brand.php'; // Replace with the URL of the page you want to redirect to
}
</script>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="<?= $i == $page ? 'active' : '' ?>">
                                <a href="?page=<?= $i ?>&sort=<?= $sort_column ?>&order=<?= $sort_order ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Close connection
$conn->close();
?>



 
        <!-- /# sidebar -->





    <!-- <div class="content-wrap">
        <div class="main">
            <div class="container-fluid">

              
                <section id="main-content">
                <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Add Brand</h4>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form action="" method="">

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Name</label>
                                                <input type="text" class="form-control" placeholder="Enter Name" required>
                                            </div>
                                           
                                        </div>
                                        
                                       
                                        <button type="submit" class="btn btn-primary">Add Brand</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                </section> -->
    <!-- jquery vendor -->
    <script src="assets/js/lib/jquery.min.js"></script>
    <script src="assets/js/lib/jquery.nanoscroller.min.js"></script>
    <!-- nano scroller -->
    <script src="assets/js/lib/menubar/sidebar.js"></script>
    <script src="assets/js/lib/preloader/pace.min.js"></script>
    <!-- sidebar -->
    
    <!-- bootstrap -->


    <script src="assets/js/scripts.js"></script>
    <!-- scripit init-->





</body>

</html>