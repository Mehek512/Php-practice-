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

$sql = "SELECT models.id, models.name, brands.name AS brand, 
               (SELECT COUNT(*) FROM items WHERE items.model_id = models.id) AS items_count
        FROM models
        LEFT JOIN brands ON models.brand_id = brands.id
        ORDER BY $sort_column $sort_order
        LIMIT $offset, $items_per_page";
$result = $conn->query($sql);

$total_models_sql = "SELECT COUNT(*) as total FROM models";
$total_models_result = $conn->query($total_models_sql);
$total_models = $total_models_result->fetch_assoc()['total'];
$total_pages = ceil($total_models / $items_per_page);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Add Items </title>

    <!-- ================= Favicon ================== -->
    <!-- Standard -->
    <link rel="shortcut icon" href="http://placehold.it/64.png/000/fff">
    <!-- Retina iPad Touch Icon-->


    <!-- Styles -->
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
            // Implement showing of popover here with relevant form data for add/edit
        }

    
        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this model?")) {
                window.location.href = `delete_model.php?deleteid=${id}`;
            }
        }
        function confirmUpdate(id) {
            if (confirm("Are you sure you want to Update this model?")) {
                window.location.href = `edit_model.php?updateid=${id}`;
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
        <!-- /# sidebar -->

        <div class="content-wrap">
    <div class="main">
        <div class="container-fluid">
            <div class="card">
                <h4 class="card-title">Models</h4>
            </div>
            <div class="table-responsive">
              <table class="table table-striped">

              <thead>
                    <tr>
                        <th onclick="sortTable('id')">ID</th>
                        <th onclick="sortTable('name')">Name</th>
                        <th onclick="sortTable('brand')">Brand</th>
                        <th onclick="sortTable('items_count')">Items Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['brand'] ?></td>
                        <td><?= $row['items_count'] ?></td>
                        <td>
                        <button onclick="confirmUpdate(<?= $row['id'] ?>)"><a href="edit_model.php? updateid='<?= $row['id'] ?>'">Edit</a></button>
                        <button onclick="confirmDelete(<?= $row['id'] ?>)">Delete</button>


                        </td>
                    </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table> 
                <div>
                <button onclick="redirectToPage()" class="btn btn-primary m-3">Add New Modal</button>


<script>
function redirectToPage() {
    window.location.href = 'addmodel.php'; // Replace with the URL of the page you want to redirect to
}
</script>
            </div>
         
    </div>
    <div class="position-absolute bottom-50 end-50">
    <ul class="pagination pagination-circle">
        <li class="page-item page-indicator">
            <a class="page-link" href="?page=<?= max(1, $current_page - 1) ?>&sort=<?= $sort_column ?>&order=<?= $sort_order ?>">
                <i class="icon-arrow-left"></i>
            </a>
        </li>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&sort=<?= $sort_column ?>&order=<?= $sort_order ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <li class="page-item page-indicator">
            <a class="page-link" href="?page=<?= min($total_pages, $current_page + 1) ?>&sort=<?= $sort_column ?>&order=<?= $sort_order ?>">
                <i class="icon-arrow-right"></i>
            </a>
        </li>
    </ul>
</div>



   <!-- <section>
        <div class="content-wrap">
                <div class="main">
                    <div class="container-fluid">

                     
                        <section id="main-content">
                        <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Add Model</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="basic-form">
                                            <form action="" method="post">

                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label>Name</label>
                                                        <input type="text" class="form-control" placeholder="Enter Name" required name="model-name">
                                                    </div>
                                                
                                                    <div class="form-group col-md-6">
                                                    <label>Brand</label>
                                                        <select name="brands" id="brand" class="form-control">
                                                            <option value="1">Select Brand</option>
                                                            <option value="2">Nike</option>
                                                            <option value="3">Puma</option>
                                                            <option value="4">Adides</option>
                                                        </select>
                                                        
                                                    </div>
                                                
                                                </div>
                                                
                                            
                                                <button type="submit" class="btn btn-primary">Add Item</button>
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