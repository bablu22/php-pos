<?php
ob_start();
require_once 'connectdb.php';
session_start();

if ($_SESSION['role'] == 'admin') {
  include_once "header.php";
} else {
  include_once "headeruser.php";
}
// Check if the user is logged in
if ($_SESSION['email'] == '') {
  header('Location: ../index.php');
  exit();
}

if (isset($_POST['add_product'])) {

  // Get form data
  $barcode = $_POST['barcode'];
  $product_name = $_POST['product_name'];
  $category_id = $_POST['category'];
  $description = $_POST['description'];
  $stock_quantity = $_POST['stock_quantity'];
  $purchase_price = $_POST['purchase_price'];
  $sale_price = $_POST['sale_price'];

  // Handle product image upload
  $product_image = null;
  if ($_FILES['product_image']['name'] != '') {
    $product_image = $_FILES['product_image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["product_image"]["name"]);
    move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file);
  }

  $sql = "INSERT INTO products (barcode, name, category, description, stock_quantity, purchase_price, sale_price,image) VALUES (:barcode, :product_name, :category_id, :description, :stock_quantity, :purchase_price, :sale_price, :product_image)";

  if (empty($barcode)) {
    $barcode = date('YmdHis') . rand(10000, 99999);
    // Prepare and execute SQL query

    // Check if product was added successfully
  }
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':barcode', $barcode);
  $stmt->bindParam(':product_name', $product_name);
  $stmt->bindParam(':category_id', $category_id);
  $stmt->bindParam(':description', $description);
  $stmt->bindParam(':stock_quantity', $stock_quantity);
  $stmt->bindParam(':purchase_price', $purchase_price);
  $stmt->bindParam(':sale_price', $sale_price);
  $stmt->bindParam(':product_image', $product_image);
  $stmt->execute();
  if ($stmt->rowCount() > 0) {
    $_SESSION['status'] = "Product added success";
    $_SESSION['status_code'] = 'success';
    header('Location: add_product.php');
    exit();
  } else {
    $_SESSION['status'] = "Product added fail";
    $_SESSION['status_code'] = 'error';
    header('Location: add_product.php');
  }

}

ob_end_flush();
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
      <div class="card card-info">
        <div class="card-header">
          <h3 class="card-title">Add Product</h3>
        </div>
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="card-body">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label for="barcode">Barcode</label>
                  <input type="text" class="form-control" id="barcode" name="barcode" placeholder="Enter barcode">
                </div>
                <div class="form-group">
                  <label for="product-name">Product Name</label>
                  <input type="text" class="form-control" required id="product-name" name="product_name"
                         placeholder="Enter product name">
                </div>
                <div class="form-group">
                  <label for="category">Category</label>
                  <select class="form-control" id="category" required name="category">
                    <?php
                    $categories_query = $conn->query("SELECT * FROM categories ORDER BY name ASC");
                    while ($row = $categories_query->fetch(PDO::FETCH_ASSOC)) {
                      echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                    }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea class="form-control" id="description" required name="description" rows="3"
                            placeholder="Enter description"></textarea>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label for="stock-quantity">Stock Quantity</label>
                  <input type="text" class="form-control" id="stock-quantity" required name="stock_quantity"
                         placeholder="Enter stock quantity">
                </div>
                <div class="form-group">
                  <label for="purchase-price">Purchase Price</label>
                  <input type="text" class="form-control" id="purchase-price" required name="purchase_price"
                         placeholder="Enter purchase price">
                </div>
                <div class="form-group">
                  <label for="sale-price">Sale Price</label>
                  <input type="text" class="form-control" id="sale-price" required name="sale_price"
                         placeholder="Enter sale price">
                </div>
                <div class="form-group">
                  <label for="product-image">Product Image</label>
                  <div class="input-group">
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" id="product-image" required name="product_image"
                             onchange="previewImage(event)">
                      <label class="custom-file-label" for="product-image">Choose file</label>
                    </div>
                  </div>
                  <img id="preview" src="#" alt="Product Image" style="display:none; max-width: 20%; height: auto;">
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary" name="add_product">Add Product</button>
          </div>
        </form>
      </div>

      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->


<script>
  function previewImage(event) {
    var preview = document.getElementById('preview');
    var file = event.target.files[0];
    var reader = new FileReader();
    reader.onloadend = function () {
      preview.src = reader.result;
      preview.style.display = "block";
    }
    if (file) {
      reader.readAsDataURL(file);
    } else {
      preview.src = "";
    }
  }
</script>

<?php include_once 'footer.php'; ?>
