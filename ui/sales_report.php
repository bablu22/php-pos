<?php
ob_start();
require_once 'connectdb.php';
session_start();
include_once "header.php";


if ($_SESSION['email'] == '' or $_SESSION['role'] == 'user') {
  header('Location: ../index.php');
  exit();
}

$sql = "SELECT DATE_FORMAT(order_date, '%Y-%m-%d') as date, SUM(total) as total_sales
        FROM invoices
        WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
        GROUP BY DATE_FORMAT(order_date, '%Y-%m-%d')
        ORDER BY DATE_FORMAT(order_date, '%Y-%m-%d') ASC";

$result = $conn->query($sql);

// Loop through query results and store data in arrays for charting
$dates = array();
$sales = array();
if ($result->rowCount() > 0) {
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $dates[] = $row["date"];
    $sales[] = $row["total_sales"];
  }
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
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <h5 class="m-0">Featured</h5>
            </div>
            <div class="card-body">
              <canvas id="salesChart"></canvas>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Set up chart data
  const salesData = {
    labels: <?php echo json_encode($dates); ?>,
    datasets: [{
      label: 'Total Sales',
      data: <?php echo json_encode($sales); ?>,
      backgroundColor: 'rgba(54, 162, 235, 0.2)',
      borderColor: 'rgba(54, 162, 235, 1)',
      borderWidth: 1
    }]
  };

  // Create chart
  const ctx = document.getElementById('salesChart').getContext('2d');
  const salesChart = new Chart(ctx, {
    type: 'bar',
    data: salesData,
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>

<?php include_once 'footer.php'; ?>
