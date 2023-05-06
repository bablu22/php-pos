<?php
ob_start();
require_once 'connectdb.php';
session_start();
include_once "header.php";
include 'barcode/barcode128.php';

if ($_SESSION['email'] == '' or $_SESSION['role'] == 'user') {
  header('Location: ../index.php');
  exit();
}

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Admin Dashboard</h1>
        </div>
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <?php
        // Fetch  products from database
        $id = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = $id");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Loop through all products and display their details
        foreach ($products as $product) {
          $id = $product['id'];
          $barcode = $product['barcode'];
          $product_name = $product['name'];
          $category_id = $product['category'];
          $description = $product['description'];
          $stock_quantity = $product['stock_quantity'];
          $purchase_price = $product['purchase_price'];
          $sale_price = $product['sale_price'];
          $image_path = "uploads/" . $product['image'];
          ?>
          <div class="col-lg-6">
            <ul class="list-group">
              <li class="list-group-item list-group-item-info text-center font-weight-bold mb-2 rounded-0">PRODUCT
                DETAILS
              </li>
              <li class="list-group-item">Barcode <span class="float-right"><?= bar128($barcode) ?></span></li>
              <li class="list-group-item">Product Name <span class="float-right"><?= $product_name ?></span></li>
              <li class="list-group-item">Category <span class="float-right"><?= $category_id ?></span></li>
              <li class="list-group-item">Description <span class="float-right"><?= $description ?></span></li>
              <li class="list-group-item">Stock <span class="float-right"><?= $stock_quantity ?></span></li>
              <li class="list-group-item">Purchase price <span class="float-right"><?= $purchase_price ?></span></li>
              <li class="list-group-item">Sale price <span class="float-right"><?= $sale_price ?></span></li>
            </ul>
          </div>
          <div class="col-lg-6">
            <ul class="list-group rounded-0">
              <li class="list-group-item list-group-item-info text-center font-weight-bold mb-2">PRODUCT Image</li>
              <li class="list-group-item">
                <img src="<?= $image_path ?>" class="w-50" alt="<?= $product_name ?>">
              </li>
            </ul>
          </div>
          <?php
        }
        ?>
      </div>

      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php include_once 'footer.php'; ?>
