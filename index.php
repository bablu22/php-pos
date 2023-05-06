<?php

session_start();

require_once 'ui/connectdb.php';

if (isset($_POST['login_btn'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $select = $conn->prepare('SELECT * FROM users WHERE email = ?');
  $select->execute([$email]);
  $row = $select->fetch(PDO::FETCH_ASSOC);

  if ($row) {
    // Verify the entered password against the stored hash
    if (password_verify($password, $row['password'])) {
      // Password is correct, set session variables and redirect
      if ($row['role'] === 'admin') {
        $_SESSION['status'] = 'Admin login success';
        $_SESSION['status_code'] = 'success';
        header('refresh:1;ui/dashboard.php');

        $_SESSION['email'] = $row['email'];
        $_SESSION['role'] = $row['role'];

      } elseif ($row['role'] === 'user') {
        $_SESSION['status'] = 'User login success';
        $_SESSION['status_code'] = 'success';
        header('refresh:1;ui/user.php');

        $_SESSION['email'] = $row['email'];
        $_SESSION['role'] = $row['role'];
      }
    } else {
      // Password is incorrect
      $_SESSION['status'] = 'Wrong email or password';
      $_SESSION['status_code'] = 'error';
      header('Location: index.php');
      exit();
    }
  } else {
    // User not found
    $_SESSION['status'] = 'Wrong email or password';
    $_SESSION['status_code'] = 'error';
    header('Location: index.php');
    exit();
  }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PHP-POS</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Toaster -->
  <link rel="stylesheet" href="plugins/toastr/toastr.min.css">

  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="index.php" class="h1"><b>PHP</b>POS</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form action="" method="post">
        <div class="input-group mb-3">
          <input type="email" class="form-control" name="email" required placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" required name="password" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <p class="mb-1">
              <a href="forgot-password.html">I forgot my password</a>
            </p>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type=" " name="login_btn" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>


    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- SweetAlert2 -->
<script src="plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toaster -->
<script src="plugins/toastr/toastr.min.js"></script>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>

</html>

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
