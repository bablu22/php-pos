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
              <div class="">
                <form action="barcode/barcode.php" method="POST">
                  <ul class="list-group">
                    <li class="list-group-item list-group-item-info text-center font-weight-bold mb-2 rounded-0">PRODUCT
                      DETAILS
                    </li>
                    <li class="list-group-item">
                      <div class="form-group">
                        <label for="product">Product Name</label>
                        <input type="text" class="form-control" id="product" name="product" readonly
                               value="<?php echo $product_name; ?>">
                      </div>
                      <div class="form-group">
                        <label for="barcode">Product ID</label>
                        <input type="number" class="form-control" id="barcode" name="barcode"
                               value="<?php echo $barcode; ?>" readonly>
                      </div>
                      <div class="form-group">
                        <label for="rate">Rate</label>
                        <input type="number" class="form-control" readonly id="rate" name="rate"
                               value="<?php echo $sale_price; ?>">
                      </div>
                      <div class="form-group">
                        <label for="stock">Rate</label>
                        <input type="number" class="form-control" readonly id="stock" name="stock"
                               value="<?php echo $stock_quantity; ?>">
                      </div>

                      <div class="form-group">
                        <label for="print_qty">Print Quantity</label>
                        <input type="number" class="form-control" id="print_qty" name="print_qty" value="1">
                      </div>
                    </li>
                    <li class="list-group-item ">
                      <button type="submit" class="btn btn-primary">Submit</button>
                    </li>
                  </ul>
                </form>
              </div>

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
