<?php ob_start();
require_once 'connectdb.php';
session_start();
include_once "header.php";


if ($_SESSION['email'] == '' or $_SESSION['role'] == 'user') {
  header('Location: ../index.php');
  exit();
}

$stmt = $conn->query("SELECT * FROM invoices ORDER BY order_date DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            <div class="card-header">
              <h5 class="m-0">Featured</h5>
            </div>
            <div class="card-body">
              <table id="example1" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>Invoice ID</th>
                  <th>Order Date</th>
                  <th>Subtotal</th>
                  <th>Discount</th>
                  <th>SGST</th>
                  <th>CGST</th>
                  <th>Payment Type</th>
                  <th>Total</th>
                  <th>Paid</th>
                  <th>Due</th>
                  <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $stmt = $conn->query("SELECT * FROM invoices ORDER BY id DESC, order_date DESC");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  ?>
                  <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['order_date']; ?></td>
                    <td><?php echo $row['subtotal']; ?></td>
                    <td><?php echo $row['discount']; ?></td>
                    <td><?php echo $row['sgst']; ?></td>
                    <td><?php echo $row['cgst']; ?></td>
                    <td><?php echo $row['payment_type']; ?></td>
                    <td><?php echo $row['total']; ?></td>
                    <td><?php echo $row['paid']; ?></td>
                    <td><?php echo $row['due']; ?></td>
                    <td>
                      <a href="print_bill.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm" title="Print"><i class="fa fa-print"></i></a>
                      <a href="delete_invoice.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></a>
                    </td>
                  </tr>
                <?php } ?>

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
