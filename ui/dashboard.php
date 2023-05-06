<?php
ob_start();
require_once 'connectdb.php';
session_start();
include_once "header.php";

if ($_SESSION['email'] == '' or $_SESSION['role'] == 'user') {
  header('Location: ../index.php');
  exit();
}

// get total number of products
$sql = $conn->prepare("SELECT COUNT(*) as total_products FROM products");
$sql->execute();
$row = $sql->fetch(PDO::FETCH_ASSOC);
$total_products = $row['total_products'];

$sql = $conn->prepare("SELECT *  FROM products");
$sql->execute();
$products = $sql->fetch(PDO::FETCH_ASSOC);


$sql = $conn->prepare("SELECT c.name, COUNT(*) as total_products
                       FROM products p
                       JOIN categories c ON p.category = c.id
                       GROUP BY c.name");
$sql->execute();
$rows = $sql->fetchAll(PDO::FETCH_ASSOC);

// Create arrays for chart data
$categories = [];
$products = [];

foreach($rows as $row) {
  $categories[] = $row['name'];
  $products[] = $row['total_products'];
}




// get total number of orders
$sql = $conn->prepare("SELECT COUNT(*) as total_orders FROM invoices");
$sql->execute();
$row = $sql->fetch(PDO::FETCH_ASSOC);
$total_orders = $row['total_orders'];

// get total number of product categories
$sql = $conn->prepare("SELECT COUNT(*) as total_categories FROM categories");
$sql->execute();
$row = $sql->fetch(PDO::FETCH_ASSOC);
$total_categories = $row['total_categories'];

// get total number of customers
$sql = $conn->prepare("SELECT COUNT(*) as total_customers FROM users");
$sql->execute();
$row = $sql->fetch(PDO::FETCH_ASSOC);
$total_customers = $row['total_customers'];

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
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $dates[] = $row["date"];
    $sales[] = $row["total_sales"];
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
      <div class="row">
        <div class="col-lg-12">
          <div class="row">
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><?php echo $total_products ?></h3>

                  <p>Total Products</p>
                </div>
                <div class="icon">
                  <i class="fa fa-shopping-bag"></i>
                </div>
                <a href="products.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-success">
                <div class="inner">
                  <h3><?php echo $total_categories ?></h3>

                  <p>Total Categories</p>
                </div>
                <div class="icon">
                  <i class="fa fa-cart-plus"></i>
                </div>
                <a href="category.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3><?php echo $total_customers ?></h3>

                  <p>User Registrations</p>
                </div>
                <div class="icon">
                  <i class="fa fa-user"></i>
                </div>
                <a href="registration.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3><?php echo $total_orders ?></h3>
                  <p>Total Orders</p>
                </div>
                <div class="icon">
                  <i class="fa fa-chart-area"></i>
                </div>
                <a href="order_list.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="card">
                <div class="card-header">
                  Sales report
                </div>
                <div class="card-body">
                  <canvas id="sales-chart" width="400" height="200"></canvas>
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="card">
                <div class="card-header">
                  Sales report
                </div>
                <div class="card-body">
                  <canvas id="products-chart" width="400" height="200"></canvas>
                </div>
              </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
<script>
  function createChart(ctx, chartData, chartOptions) {
    return new Chart(ctx, {
      type: chartOptions.type,
      data: {
        labels: chartData.labels,
        datasets: [{
          label: chartData.label,
          data: chartData.data,
          borderColor: chartOptions.borderColor,
          fill: chartOptions.fill,
          backgroundColor: chartOptions.backgroundColor,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
          display: false
        },
        scales: {
          xAxes: [{
            ticks: {
              autoSkip: true,
              maxTicksLimit: 10
            }
          }],
          yAxes: [{
            ticks: {
              beginAtZero: true,
              callback: function (value, index, values) {
                return '$' + value.toFixed(2);
              }
            }
          }]
        }
      }
    });
  }

  // Fetch sales data from database
  const salesData = <?php echo json_encode($sales); ?>;
  const salesCtx = document.getElementById('sales-chart').getContext('2d');
  const salesChart = createChart(salesCtx, {
    labels: <?php echo json_encode($sales); ?>,
    data: salesData,
    label: 'Sales',
  }, {
    type: 'bar',
    borderColor: 'blue',
    fill: true,
  });

  // Fetch product data from database
  const productData = <?php echo json_encode($products); ?>;
  const productCtx = document.getElementById('products-chart').getContext('2d');
  const productChart = createChart(productCtx, {
    labels: <?php echo json_encode($categories); ?>,
    data: productData,
    label: 'Total Products',
  }, {
    type: 'bar',
    backgroundColor: 'gray',
  });

</script>


<?php include_once 'footer.php'; ?>
