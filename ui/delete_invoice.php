<?php

ob_start();
require_once 'connectdb.php';
session_start();


$invoice_id = $_GET['id'];
// First, delete the invoice details
$delete_invoice_details = $conn->prepare("DELETE FROM invoice_details WHERE invoice_id = :invoice_id");
$delete_invoice_details->bindParam(":invoice_id", $invoice_id);
$delete_invoice_details->execute();

// Then, delete the invoice
$delete_invoice = $conn->prepare("DELETE FROM invoices WHERE id = :invoice_id");
$delete_invoice->bindParam(":invoice_id", $invoice_id);

if ($delete_invoice->execute()) {
  $_SESSION['status'] = "Invoice deleted success";
  $_SESSION['status_code'] = 'success';
} else {
  $_SESSION['status'] = "Invoice deleted fail";
  $_SESSION['status_code'] = 'error';
}
header('Location: order_list.php');
exit();
