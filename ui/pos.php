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
function fill_product($conn)
{
  $output = "";
  $select = $conn->prepare("SELECT * FROM products");
  $select->execute();
  $result = $select->fetchAll();

  foreach ($result as $row) {
    $output .= '<option value="' . $row["id"] . '">' . $row['name'] . '</option>';
  }

  return $output;
}

$select = $conn->prepare("SELECT * FROM tax WHERE id=1");
$select->execute();
$row = $select->fetch(PDO::FETCH_ASSOC);


// submit the data into database

if (isset($_POST['btn_save'])) {
  $order_date = date('Y-m-d');
  $subtotal = $_POST['subtotal'];
  $discount = $_POST['discount'];
  $sgst = $_POST['sgst'];
  $cgst = $_POST['cgst'];
  $payment_type = $_POST['rb'];
  $due = $_POST['due'];
  $paid = $_POST['paid'];
  $total = $_POST['total'];


  // Insert data into invoice table
  $insert = $conn->prepare("INSERT INTO invoices (order_date, subtotal, discount, sgst, cgst, payment_type, due, total, paid) VALUES (:order_date, :subtotal, :discount, :sgst, :cgst, :payment_type, :due,:total, :paid)");
  $insert->bindParam(":order_date", $order_date);
  $insert->bindParam(":subtotal", $subtotal);
  $insert->bindParam(":discount", $discount);
  $insert->bindParam(":sgst", $sgst);
  $insert->bindParam(":cgst", $cgst);
  $insert->bindParam(":payment_type", $payment_type);
  $insert->bindParam(":due", $due);
  $insert->bindParam(":total", $total);
  $insert->bindParam(":paid", $paid);


  if ($insert->execute()) {
    $invoice_id = $conn->lastInsertId();

    // Insert data into invoice_details table
    foreach ($_POST['data'] as $data) {
      $product_data = json_decode($data, true);

      $category_id = $product_data['category'];
      $barcode = $product_data['barcode'];
      $product_id = $product_data['product_id'];
      $product_name = $product_data['product_name'];
      $quantity = $product_data['quantity'];
      $sale_price = $product_data['sale_price'];
      $total_price = $product_data['total_price'];

      $stmt = $conn->prepare("SELECT name FROM categories WHERE id = :category_id");
      $stmt->bindParam(":category_id", $category_id);
      $stmt->execute();
      $category_name = $stmt->fetchColumn();


      $insert_details = $conn->prepare("INSERT INTO invoice_details (invoice_id, category, barcode, product_id, product_name, quantity, sale_price, total_price, order_date) VALUES (:invoice_id, :category_name, :barcode, :product_id, :product_name, :quantity, :sale_price, :total_price, :order_date)");
      $insert_details->bindParam(":invoice_id", $invoice_id);
      $insert_details->bindParam(":category_name", $category_name);
      $insert_details->bindParam(":barcode", $barcode);
      $insert_details->bindParam(":product_id", $product_id);
      $insert_details->bindParam(":product_name", $product_name);
      $insert_details->bindParam(":quantity", $quantity);
      $insert_details->bindParam(":sale_price", $sale_price);
      $insert_details->bindParam(":total_price", $total_price);
      $insert_details->bindParam(":order_date", $order_date);
      $insert_details->execute();

      // Decrement product quantity in products table
      $decrement_quantity = $conn->prepare("UPDATE products SET stock_quantity = products.stock_quantity - :quantity WHERE id = :product_id");
      $decrement_quantity->bindParam(":quantity", $quantity);
      $decrement_quantity->bindParam(":product_id", $product_id);
      $decrement_quantity->execute();
    }

    // Show success message
    $_SESSION['status'] = "Invoice created successfully";
    $_SESSION['status_code'] = 'success';
  } else {
    $_SESSION['status'] = "Invoice creation failed";
    $_SESSION['status_code'] = 'error';
  }
  header('Location: pos.php');
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
              <h5 class="m-0">POS</h5>
            </div>
            <div class="card-body">
              <form action="" method="post">
                <div class="row">
                  <div class="col-lg-8">
                    <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-barcode"></i></span>
                      </div>
                      <input type="text" class="form-control"  name="barcode" placeholder="Scan Barcode">
                    </div>

                    <select class="form-control select2" id="product-dropdown" style="width: 100%;">
                      <option selected="selected">Select</option>
                      <?php echo fill_product($conn); ?>
                    </select>

                    <div class=" mt-4">

                      <!-- /.card-header -->
                      <div class="card-body table-responsive p-0" style="height: 500px;">
                        <table id="product_table" class="table table-bordered table-head-fixed text-nowrap">
                          <thead class="bg-da">
                          <tr>
                            <th>Product</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>QTY</th>
                            <th>Total</th>
                            <th>Del</th>
                          </tr>
                          </thead>
                          <tbody>

                          </tbody>
                        </table>
                      </div>
                      <!-- /.card-body -->
                    </div>

                  </div>
                  <div class="col-lg-4">

                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">SUBTOTAL(TK)</span>
                      </div>
                      <input type="text" class="form-control" name="subtotal" id="subtotal" readonly>
                      <div class="input-group-append">
                        <span class="input-group-text">TK</span>
                      </div>
                    </div>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">DISCOUNT(%)</span>
                      </div>
                      <input type="text" class="form-control" name="discount" id="discount"
                             value="<?php echo $row['discount']; ?>">
                      <div class="input-group-append">
                        <span class="input-group-text">%</span>
                      </div>
                    </div>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">DISCOUNT(TK)</span>
                      </div>
                      <input type="text" class="form-control" id="discount_amount" readonly>
                      <div class="input-group-append">
                        <span class="input-group-text">TK</span>
                      </div>
                    </div>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">SGST(%)</span>
                      </div>
                      <input type="text" class="form-control" name="sgst" id="sgst" value="<?php echo $row['sgst']; ?>"
                             readonly>
                      <div class="input-group-append">
                        <span class="input-group-text">%</span>
                      </div>
                    </div>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">CGST(%)</span>
                      </div>
                      <input type="text" class="form-control" name="cgst" id="cgst" value="<?php echo $row['cgst']; ?>"
                             readonly>
                      <div class="input-group-append">
                        <span class="input-group-text">%</span>
                      </div>
                    </div>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">SGST(TK)</span>
                      </div>
                      <input type="text" class="form-control" id="sgst_amount" readonly>
                      <div class="input-group-append">
                        <span class="input-group-text">TK</span>
                      </div>
                    </div>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">CGST(TK)</span>
                      </div>
                      <input type="text" class="form-control" id="cgst_amount" readonly>
                      <div class="input-group-append">
                        <span class="input-group-text">TK</span>
                      </div>
                    </div>
                    <hr style="height: 2px;border-width: 0;color: black;background-color: black">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">TOTAL(TK)</span>
                      </div>
                      <input type="text" class="form-control" name="total" id="total" readonly>
                      <div class="input-group-append">
                        <span class="input-group-text">TK</span>
                      </div>
                    </div>
                    <div class="form-group clearfix mt-3">
                      <div class="icheck-success d-inline">
                        <input type="radio" name="rb" value="Cash" checked="" id="radioSuccess1">
                        <label for="radioSuccess1">
                          CASH
                        </label>
                      </div>
                      <div class="icheck-info d-inline">
                        <input type="radio" name="rb" value="Card" id="radioSuccess2">
                        <label for="radioSuccess2">CARD
                        </label>
                      </div>
                      <div class="icheck-primary d-inline">
                        <input type="radio" name="rb" value="Check" id="radioSuccess3">
                        <label for="radioSuccess3">
                          CHECK
                        </label>
                      </div>
                    </div>
                    <hr style="height: 2px;border-width: 0;color: black;background-color: black">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">DUE(TK)</span>
                      </div>
                      <input type="text" class="form-control" name="due" id="due" readonly>
                      <div class="input-group-append">
                        <span class="input-group-text">TK</span>
                      </div>
                    </div>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">PAID(TK)</span>
                      </div>
                      <input type="text" id="paid" required name="paid" class="form-control">
                      <div class="input-group-append">
                        <span class="input-group-text">TK</span>
                      </div>
                    </div>
                    <hr style="height: 2px;border-width: 0;color: black;background-color: black">
                    <button type="submit" name="btn_save" class="btn btn-primary">Save Order</button>

                  </div>
                </div>
              </form>
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

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

<script>
  $(document).ready(function () {
    // Listen for changes to the barcode input field
    $('input[name="barcode"],#product-dropdown').on('change', function () {
      // Retrieve the barcode value from the input field
      const barcode = $(this).val();

      // Make an AJAX request to the script.php file to retrieve the product data based on the barcode
      $.ajax({
        url: 'get_product.php',
        data: {barcode: barcode},
        type: 'GET',
        dataType: 'json',
        success: function (response) {
          // Append a new row with the product data to the table
          const sale_price = response.sale_price;
          const quantity = $('input[name="qty"]').val() || 1; // Set default value of 1 if input is empty
          const total = sale_price * quantity;

          $('#product_table tbody').append(`
  <tr>
    <td>${response.name}</td>
    <td>${response.stock_quantity}</td>
    <td>${response.sale_price}</td>
    <td><input value="${quantity}" type="number" name="qty"></td>
    <td>${total}</td>
    <td>
      <button class="btn btn-danger btn-sm">Del</button>
      <input type="hidden" name="product_id[]" value="${response.id}">
      <input type="hidden" name="data[]" value='{"product_id":"${response.id}", "barcode":"${response.barcode}", "product_name":"${response.name}", "stock":"${response.stock_quantity}", "category":"${response.category}", "quantity":"${quantity}", "sale_price":"${response.sale_price}", "total_price":"${total}"}'>
    </td>
  </tr>
`);

          // Calculate the total of sale_price and quantity for all rows in the table
          let total_sale_price = 0;
          let total_quantity = 0;
          $('#product_table tbody tr').each(function () {
            const row_sale_price = parseFloat($(this).find('td:nth-child(3)').text());
            const row_quantity = parseInt($(this).find('input[name="qty"]').val());
            total_sale_price += row_sale_price;
            total_quantity += row_quantity;

            // Attach a change event handler to the quantity input
            $(this).find('input[name="qty"]').on('change', function () {
              const new_quantity = parseInt($(this).val());
              const new_total = row_sale_price * new_quantity;
              $(this).closest('tr').find('td:nth-child(5)').text(new_total);
              recalculateTotals();

              // Update the data input field with the updated quantity and total_price for this row
              const data = $(this).closest('tr').find('input[name="data[]"]').val();
              const newData = JSON.parse(data);
              newData.quantity = new_quantity;
              newData.total_price = new_total;
              $(this).closest('tr').find('input[name="data[]"]').val(JSON.stringify(newData));
            });
          });


          $('#discount').on('change keyup', function () {
            recalculateTotals();
          });


          // Update the total in the footer row of the table
          function recalculateTotals() {
            let total_sale_price = 0;
            let total_quantity = 0;
            let subtotal = 0;
            $('#product_table tbody tr').each(function () {
              const row_sale_price = parseFloat($(this).find('td:nth-child(3)').text());
              const row_quantity = parseInt($(this).find('input[name="qty"]').val());
              const row_total = row_sale_price * row_quantity;
              $(this).find('td:nth-child(5)').text(row_total);
              total_sale_price += row_sale_price;
              total_quantity += row_quantity;
              subtotal += row_total;
            });
            $('#product_table tfoot td:nth-child(3)').text(total_sale_price.toFixed(2));
            $('#product_table tfoot td:nth-child(4)').text(total_quantity);
            $('#subtotal').val(subtotal.toFixed(2));

            // var subtotal = parseFloat(document.getElementById("subtotal").value);
            const discount = parseFloat(document.getElementById("discount").value);
            const sgst = parseFloat(document.getElementById("sgst").value);
            const cgst = parseFloat(document.getElementById("cgst").value);

            // Calculate discount amount
            const discountAmount = subtotal * discount / 100;

            // Calculate SGST and CGST amounts
            const sgstAmount = subtotal * sgst / 100;
            const cgstAmount = subtotal * cgst / 100;

            // Calculate total amount
            const total = subtotal - discountAmount + sgstAmount + cgstAmount;

            // Update the input fields with the calculated values
            document.getElementById("discount_amount").value = discountAmount.toFixed(2);
            document.getElementById("sgst_amount").value = sgstAmount.toFixed(2);
            document.getElementById("cgst_amount").value = cgstAmount.toFixed(2);
            document.getElementById("total").value = total.toFixed(2);
            document.getElementById("due").value = total.toFixed(2);


          }

          recalculateTotals();

          $('input[name="barcode"]').val('');
          // Attach a click event handler to the "Del" button
          $(document).on('click', '#product_table tbody tr button', function () {
            $(this).closest('tr').remove(); // Remove the corresponding row from the table
            recalculateTotals(); // Recalculate the totals after removing the row
          });

          $(document).on('input', '#paid', function () {
            const paid = parseFloat($(this).val());
            const total = parseFloat($('#total').val());
            const due = total - paid;
            $('#due').val(due.toFixed(2));
            $('#paid_amount').val(paid.toFixed(2));
          });


        }
      });
    });
  });


</script>

<?php include_once 'footer.php'; ?>
