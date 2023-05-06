<?php
ob_start();
require_once 'connectdb.php';
session_start();
include_once "header.php";


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
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header ">
              <a href="add_product.php" class="btn btn-sm btn-primary">Add Product</a>
            </div>
            <div class="card-body">
              <table id="example1" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>Product Image</th>
                  <th>Barcode</th>
                  <th>Product Name</th>
                  <th>Category</th>
                  <th>Stock Quantity</th>
                  <th>Purchase Price</th>
                  <th>Sale Price</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $products_query = $conn->query("SELECT * FROM products");
                while ($row = $products_query->fetch(PDO::FETCH_ASSOC)) {
                  echo '<tr>';
                  echo '<td><img src="uploads/' . $row['image'] . '" width="50px" alt=""></td>';
                  echo '<td>' . $row['barcode'] . '</td>';
                  echo '<td>' . $row['name'] . '</td>';
                  $category_query = $conn->query("SELECT name FROM categories WHERE id=" . $row['category']);
                  $category_name = $category_query->fetch(PDO::FETCH_ASSOC)['name'];
                  echo '<td>' . $category_name . '</td>';
                  echo '<td>' . $row['stock_quantity'] . '</td>';
                  echo '<td>' . $row['purchase_price'] . '</td>';
                  echo '<td>' . $row['sale_price'] . '</td>';
                  echo '<td>
    <div class="d-flex">
        <a href="view_product.php?id=' . $row['id'] . '" class="btn btn-sm btn-primary mr-2">
            <i class="fas fa-eye"></i>
        </a>
        <a href="edit_product.php?id=' . $row['id'] . '" class="btn btn-sm btn-warning mr-2">
            <i class="fas fa-edit"></i>
        </a>
        <a href="barcode_product.php?id=' . $row['id'] . '" class="btn btn-sm btn-info mr-2" >
            <i class="fas fa-barcode"></i>
        </a>
        <a href="delete_product.php?id=' . $row['id'] . '" class="btn btn-sm btn-danger mr-2">
            <i class="fas fa-trash-alt"></i>
        </a>
    </div>
</td>
';
                  echo '</tr>';
                }
                ?>
                </tbody>
              </table>

            </div>
          </div>
        </div>

      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php include_once 'footer.php'; ?>
