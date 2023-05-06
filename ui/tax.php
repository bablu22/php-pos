<?php
ob_start();
require_once 'connectdb.php';
session_start();
include_once "header.php";


if ($_SESSION['email'] == '' or $_SESSION['role'] == 'user') {
  header('Location: ../index.php');
  exit();
}

if (isset($_GET['id'])) {
  $tax_id = $_GET['id'];

  // Delete tax record from the tax table
  $delete_tax = $conn->prepare("DELETE FROM tax WHERE id = :id");
  $delete_tax->bindParam(":id", $tax_id);

  if ($delete_tax->execute()) {
    $_SESSION['status'] = "Tax record deleted successfully";
    $_SESSION['status_code'] = 'success';
  } else {
    $_SESSION['status'] = "Tax record deletion failed";
    $_SESSION['status_code'] = 'error';
  }
  header('Location: tax.php');
  exit();
}


if (isset($_POST['btn_save'])) {

  $sgst = filter_input(INPUT_POST, 'sgst', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
  $cgst = filter_input(INPUT_POST, 'cgst', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
  $discount = filter_input(INPUT_POST, 'discount', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

  if (!$sgst || !$cgst || !$discount) {
    // Input data is invalid, handle the error
    // For example:
    $_SESSION['status'] = "Invalid input data";
    $_SESSION['status_code'] = 'error';
    header('Location: tax.php');
    exit();
  }


  // Insert tax data into tax table
  $insert_tax = $conn->prepare("INSERT INTO tax (sgst, cgst, discount) VALUES (:sgst, :cgst, :discount)");
  $insert_tax->bindParam(":sgst", $sgst);
  $insert_tax->bindParam(":cgst", $cgst);
  $insert_tax->bindParam(":discount", $discount);

  if ($insert_tax->execute()) {
    $_SESSION['status'] = "Tax created successfully";
    $_SESSION['status_code'] = 'success';
  } else {
    $_SESSION['status'] = "Tax creation failed";
    $_SESSION['status_code'] = 'error';
  }
  header('Location: tax.php');
  exit();
}


if (isset($_POST['edit_btn'])) {
  $tax_id = $_POST['id'];
  $sgst = $_POST['sgst'];
  $cgst = $_POST['cgst'];
  $discount = $_POST['discount'];

  // Update tax record in tax table
  $update_tax = $conn->prepare("UPDATE tax SET sgst = :sgst, cgst = :cgst, discount = :discount WHERE id = :id");
  $update_tax->bindParam(":id", $tax_id);
  $update_tax->bindParam(":sgst", $sgst);
  $update_tax->bindParam(":cgst", $cgst);
  $update_tax->bindParam(":discount", $discount);

  if ($update_tax->execute()) {
    $_SESSION['status'] = "Tax record updated successfully";
    $_SESSION['status_code'] = 'success';
  } else {
    $_SESSION['status'] = "Tax record update failed";
    $_SESSION['status_code'] = 'error';
  }
  header('Location: tax.php');
  exit();
}


ob_end_flush();
?>


<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Category Management</h1>
        </div>
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-4">
          <div class="card card-info">
            <div class="card-header">
              <h5 class="m-0">Tax And Discount Form</h5>
            </div>
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group">
                  <label for="sgst">SGST (%)</label>
                  <input type="text" class="form-control" placeholder="Enter SGST" id="sgst" name="sgst" required>
                </div>

                <div class="form-group">
                  <label for="cgst">CGST (%)</label>
                  <input type="text" class="form-control" placeholder="Enter CGST" id="cgst" name="cgst" required>
                </div>

                <div class="form-group">
                  <label for="discount">Discount (%)</label>
                  <input type="text" class="form-control" placeholder="Enter Discount" id="discount" name="discount"
                         required>
                </div>

                <button type="submit" name="btn_save" class="btn btn-primary">Save</button>
              </form>

            </div>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="card card-info">
            <div class="card-header">
              <h5 class="m-0">Taxes</h5>
            </div>
            <div class="card-body">
              <table id="example1" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>SL</th>
                  <th>SGST</th>
                  <th>CGST</th>
                  <th>Discount</th>
                  <th>Edit</th>
                  <th>Delete</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $select = $conn->prepare("SELECT * FROM tax ORDER BY id ASC");
                $select->execute();

                // Loop through results and display in table
                while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
                  echo "<tr>
          <td>{$row['id']}</td>
          <td>{$row['sgst']}</td>
          <td>{$row['cgst']}</td>
          <td>{$row['discount']}</td>
          <td>
            <button type='button' class='btn btn-default edit-btn' data-toggle='modal' data-target='#modal-default' data-id='{$row['id']}' >
              <i class='fa fa-edit'></i>
            </button>
          </td>
          <td>
            <a href='tax.php?id={$row['id']}' class='btn btn-danger btn-sm'>
              <i class='fa fa-trash'></i>
            </a>
          </td>
        </tr>";
                }
                ?>
                </tbody>
              </table>


              <!-- Edit category modal -->
              <div class="modal fade" id="modal-default">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form id="edit-form" class="p-3" method="POST" action="">
                      <input type="hidden" name="id" value="">
                      <div class="form-group">
                        <label for="sgst">SGST (%)</label>
                        <input type="text" class="form-control" placeholder="Enter SGST" id="sgst" name="sgst" required>
                      </div>

                      <div class="form-group">
                        <label for="cgst">CGST (%)</label>
                        <input type="text" class="form-control" placeholder="Enter CGST" id="cgst" name="cgst" required>
                      </div>

                      <div class="form-group">
                        <label for="discount">Discount (%)</label>
                        <input type="text" class="form-control" placeholder="Enter Discount" id="discount"
                               name="discount" required>
                      </div>

                      <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" name="edit_btn" class="btn btn-primary">Save changes</button>
                      </div>
                    </form>

                  </div>
                  <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
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
</div>


<?php include_once 'footer.php'; ?>


<script>
  $(document).on('click', '.edit-btn', function () {
    var id = $(this).data('id');
    var sgst = $(this).closest('tr').find('td:eq(1)').text();
    var cgst = $(this).closest('tr').find('td:eq(2)').text();
    var discount = $(this).closest('tr').find('td:eq(3)').text();

    $('#edit-form input[name="id"]').val(id);
    $('#edit-form input[name="sgst"]').val(sgst);
    $('#edit-form input[name="cgst"]').val(cgst);
    $('#edit-form input[name="discount"]').val(discount);
  });

</script>

