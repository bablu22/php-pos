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

$id = $_GET['id'];

// Get the product's image name from the database
$stmt = $conn->prepare("SELECT image FROM products WHERE id=:id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);
$product_image = $product['image'];

// Delete the product from the database
$stmt = $conn->prepare("DELETE FROM products WHERE id=:id");
$stmt->bindParam(':id', $id);
$stmt->execute();

// Delete the product image from the uploads folder
$target_file = "uploads/" . $product_image;
if (file_exists($target_file)) {
  unlink($target_file);
}

// Check if product was deleted successfully
if ($stmt->rowCount() > 0) {
  $_SESSION['status'] = "Product deleted successfully";
  $_SESSION['status_code'] = 'success';

} else {
  $_SESSION['status'] = "Product deletion failed";
  $_SESSION['status_code'] = 'error';
}

header('Location: products.php');
exit();
