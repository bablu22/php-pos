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
if (isset($_POST['btn_update'])) {
  // Get the user's current password hash from the database
  $email = $_SESSION['email'];
  $stmt = $conn->prepare("SELECT password FROM users WHERE email=:email");
  $stmt->bindParam(":email", $email);
  $stmt->execute();
  $row = $stmt->fetch();
  $current_password_hash = $row['password'];

  // Verify the hash of the old password
  $old_password = $_POST['old_password'];
  if (!password_verify($old_password, $current_password_hash)) {
    $_SESSION['status'] = "Your old password is incorrect.";
    $_SESSION['status_code'] = "error";
    header('Location: change_password.php');
    exit();
  }

  // Check if the "new_password" and "confirm_password" fields match
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];
  if ($new_password != $confirm_password) {
    $_SESSION['status'] = "The new password and confirm password fields do not match.";
    $_SESSION['status_code'] = "error";
    header('Location: change_password.php');
    exit();
  }

  // Hash the new password before updating it in the database
  $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

  // Update the user's password hash in the database with the new password hash
  $stmt = $conn->prepare("UPDATE users SET password=:password WHERE email=:email");
  $stmt->bindParam(":password", $new_password_hash);
  $stmt->bindParam(":email", $email);
  $stmt->execute();

  $_SESSION['status'] = "Your password has been updated successfully.";
  $_SESSION['status_code'] = "success";
  header('Location: change_password.php');
  exit();
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
          <h1 class="m-0">Change Password</h1>
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
          <div class="card card-info">
            <div class="card-header">
              <h3 class="card-title">Change password</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form class="form-horizontal" action="" method="post">
              <div class="card-body">

                <div class="form-group row">
                  <label for="inputPassword3" class="col-sm-2 col-form-label">Old Password</label>
                  <div class="col-sm-10">
                    <input type="password" class="form-control" required name="old_password" id="inputPassword3"
                           placeholder="Old Password">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputPassword3" class="col-sm-2 col-form-label">New Password</label>
                  <div class="col-sm-10">
                    <input type="password" class="form-control" required name="new_password" id="inputPassword3"
                           placeholder="New Password">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="inputPassword3" class="col-sm-2 col-form-label">Confirm Password</label>
                  <div class="col-sm-10">
                    <input type="password" class="form-control" required id="inputPassword3" name="confirm_password"
                           placeholder="Confirm Password">
                  </div>
                </div>

              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <button type="submit" class="btn btn-info" name="btn_update">Update Password</button>
              </div>
              <!-- /.card-footer -->
            </form>
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


<?php
if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
  ?>

  <script>
    $(function () {
      <?php if(isset($_SESSION['status_code'])): ?>
      let icon = '<?php echo $_SESSION['status_code']?>';
      <?php else: ?>
      icon = 'info';
      <?php endif; ?>

      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
      });

      Toast.fire({
        icon: icon,
        title: '<?php echo $_SESSION['status']?>'
      });
    });
  </script>

  <?php
  unset($_SESSION['status']);
}
?>
