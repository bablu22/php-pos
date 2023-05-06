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
  $id = $_GET['id'];
  $delete = $conn->prepare("DELETE FROM categories WHERE id = :id");
  $delete->bindParam(":id", $id);
  $delete->execute();
  $_SESSION['status'] = "Category deleted success";
  $_SESSION['status_code'] = 'success';
  header('Location: category.php');
  exit();
}


if (isset($_POST['btn_save'])) {
  $category_name = $_POST["name"];

  if (empty($category_name)) {
    $_SESSION['status'] = "Please fill in the category name";
    $_SESSION['status_code'] = 'error';
    header('Location: category.php');
    exit();
  }

  // Insert category into categories table
  $insert_category = $conn->prepare("INSERT INTO categories (name) VALUES (:name)");
  $insert_category->bindParam(":name", $category_name);

  if ($insert_category->execute()) {
    $_SESSION['status'] = "Category created successfully";
    $_SESSION['status_code'] = 'success';
  } else {
    $_SESSION['status'] = "Category creation failed";
    $_SESSION['status_code'] = 'error';
  }
  header('Location: category.php');
  exit();
}

if (isset($_POST['edit_btn'])) {
  $category_id = $_POST['id'];
  $category_name = $_POST['name'];

  if (empty($category_name)) {
    $_SESSION['status'] = "Please fill in the category name";
    $_SESSION['status_code'] = 'error';
    header('Location: category.php');
    exit();
  }

  // Update category in categories table
  $update_category = $conn->prepare("UPDATE categories SET name = :name WHERE id = :id");
  $update_category->bindParam(":id", $category_id);
  $update_category->bindParam(":name", $category_name);

  if ($update_category->execute()) {
    $_SESSION['status'] = "Category updated successfully";
    $_SESSION['status_code'] = 'success';
  } else {
    $_SESSION['status'] = "Category update failed";
    $_SESSION['status_code'] = 'error';
  }
  header('Location: category.php');
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
              <h5 class="m-0">Category Form</h5>
            </div>
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group">
                  <label for="name">Category Name</label>
                  <input type="text" class="form-control" placeholder="Enter category" id="name" name="name" required>
                </div>

                <button type="submit" name="btn_save" class="btn btn-primary">Save</button>
              </form>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="card card-info">
            <div class="card-header">
              <h5 class="m-0">All Categories</h5>
            </div>
            <div class="card-body">
              <table id="example1" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>SL</th>
                  <th>Category</th>
                  <th>Edit</th>
                  <th>Delete</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $select = $conn->prepare("SELECT * FROM categories ORDER BY id ASC");
                $select->execute();

                // Loop through results and display in table
                while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
                  echo "<tr>
          <td>{$row['id']}</td>
          <td>{$row['name']}</td>
          <td>
            <button type='button' class='btn btn-default edit-btn' data-toggle='modal' data-target='#modal-default' data-id='{$row['id']}' data-name='{$row['name']}'>
              <i class='fa fa-edit'></i>
            </button>
          </td>
          <td>
            <a href='category.php?id={$row['id']}' class='btn btn-danger btn-sm'>
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
                    <form id="edit-form" method="POST" action="">
                      <div class="modal-header">
                        <h4 class="modal-title">Edit Category</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id" value="">
                        <div class="form-group">
                          <label for="edit-name">Name</label>
                          <input type="text" name="name" id="edit-name" class="form-control" value="">
                        </div>
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
  // When the edit button is clicked
  $('.edit-btn').click(function () {
    // Get the ID and name of the category from the data attributes
    var id = $(this).data('id');
    var name = $(this).data('name');

    // Set the ID and name values in the form
    $('#edit-id').val(id);
    $('#edit-name').val(name);
  });

</script>
