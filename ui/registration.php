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
  $delete = $conn->prepare("DELETE FROM users WHERE id = :id");
  $delete->bindParam(":id", $id);
  $delete->execute();
  $_SESSION['status'] = "User deleted success";
  $_SESSION['status_code'] = 'success';
  header('Location: registration.php');
  exit();
}


if (isset($_POST['btn_save'])) {
  $name = $_POST["name"];
  $email = $_POST["email"];
  $password = $_POST["password"];
  $role = $_POST["role"];

  if (empty($name) || empty($email) || empty($password) || empty($role)) {
    $_SESSION['status'] = "Please fill in all required fields";
    $_SESSION['status_code'] = 'error';
    header('Location: registration.php');
    exit();
  }

  // Check if user already exists
  $select = $conn->prepare("SELECT * FROM users WHERE email = :email");
  $select->bindParam(":email", $email);
  $select->execute();
  $user = $select->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    $_SESSION['status'] = "User already exists";
    $_SESSION['status_code'] = 'error';
    header('Location: registration.php');
    exit();
  }
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);
  // If user does not exist, insert into database
  $insert = $conn->prepare("INSERT INTO users (email, password, role, name) VALUES (:email, :password, :role, :name)");
  $insert->bindParam(":email", $email);
  $insert->bindParam(":password", $hashed_password);
  $insert->bindParam(":role", $role);
  $insert->bindParam(":name", $name);




  if ($insert->execute()) {
    $_SESSION['status'] = "User created successfully";
    $_SESSION['status_code'] = 'success';
    header('Location: registration.php');
    exit();
  } else {
    $_SESSION['status'] = "User creation failed";
    $_SESSION['status_code'] = 'error';
    header('Location: registration.php');
    exit();
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
          <h1 class="m-0">User Management</h1>
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
              <h5 class="m-0">Registration</h5>
            </div>
            <div class="card-body">
              <form action="" method="post">
                <div class="form-group">
                  <label for="name">Name</label>
                  <input type="text" class="form-control" placeholder="Enter name" id="name" name="name" required>
                </div>
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" required>
                </div>
                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" class="form-control" placeholder="Enter password" id="password" name="password"
                         required>
                </div>
                <div class="form-group">
                  <label for="role">Role</label>
                  <select class="form-control" id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                  </select>
                </div>
                <button type="submit" name="btn_save" class="btn btn-primary">Register</button>
              </form>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="card card-info">
            <div class="card-header">
              <h5 class="m-0">Users Table</h5>
            </div>
            <div class="card-body">
              <table id="example1" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $select = $conn->prepare("SELECT * FROM users order by id ASC ");
                $select->execute();

                // Loop through results and display in table
                while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
                  echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['role']}</td>
              <td><a href='registration.php?id={$row['id']}' class='btn btn-danger btn-sm'><i class='fa fa-trash'></i></a></td>

              </tr>";
                }
                ?>
                </tbody>
              </table>

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



