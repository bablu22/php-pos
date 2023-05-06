<?php
include_once "connectdb.php";

$product_id = $_GET['barcode'];
$barcode = $_GET['barcode'];

$select = $conn->prepare("SELECT * FROM products WHERE id=$product_id OR barcode=$barcode");
$select->execute();
$row = $select->fetch(PDO::FETCH_ASSOC);
$response = $row;
header('Content-type:application.json');

echo json_encode($response);
