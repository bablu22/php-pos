<?php

ob_start();
require_once 'connectdb.php';
session_start();

// call the FPDF library
require("fpdf/fpdf.php");
$pdf = new FPDF('P', 'mm', array(80, 200));

$invoice_id = $_GET['id'];

// get invoice data from database
$query = $conn->prepare("SELECT * FROM invoices WHERE id = :id");
$query->bindParam(":id", $invoice_id);
$query->execute();
$invoice = $query->fetch();

// add new page
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(60, 8, 'PHP POS', 1, 1, "C");

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(60, 5, 'ADDRESS: YOUR SHOP ADDRESS', 0, 1, "C");
$pdf->Cell(60, 5, 'WEBSITE: WWW.PHPPOS.COM', 0, 1, "C");
$pdf->Cell(60, 5, 'PHONE NUMBER: 01000000', 0, 1, "C");

$pdf->Cell(60, 5, 'EMAIL: john@example.com', 0, 1, "C");
$pdf->Line(10, $pdf->GetY(), 70, $pdf->GetY());
$pdf->Ln(1);


$pdf->SetFont('Arial', 'BI', 8);
$pdf->Cell(20, 4, 'Bill No: ', 0, 0, "");

$pdf->SetFont('Courier', 'BI', 8);
$pdf->Cell(40, 4, $invoice_id, 0, 1, "");

$pdf->SetFont('Arial', 'BI', 8);
$pdf->Cell(20, 4, "Date", 0, 0, "");

$pdf->SetFont('Courier', 'BI', 8);
$pdf->Cell(40, 4, $invoice['order_date'], 0, 1, "");


// create table header
$pdf->SetX(7);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(34, 5, 'Product Name', 1, 0, 'C');
$pdf->Cell(11, 5, 'QTY', 1, 0, 'C');
$pdf->Cell(8, 5, 'PRC', 1, 0, 'C');
$pdf->Cell(12, 5, 'Total', 1, 1, 'C');

// get invoice details from database
$query = $conn->prepare("SELECT * FROM invoice_details WHERE invoice_id = :invoice_id");
$query->bindParam(":invoice_id", $invoice_id);
$query->execute();
$invoice_details = $query->fetchAll();

// create table rows
$pdf->SetFont('Arial', '', 12);
$total_price = 0;
foreach ($invoice_details as $detail) {
  $pdf->SetX(7);
  $pdf->SetFont('helvetica', 'B', 8);
  $pdf->Cell(34, 5, $detail['product_name'], 1, 0);
  $pdf->Cell(11, 5, $detail['quantity'], 1, 0, 'C');
  $pdf->Cell(8, 5, $detail['sale_price'], 1, 0, 'C');
  $pdf->Cell(12, 5, $detail['total_price'], 1, 1, 'C');
  $total_price += $detail['total_price'];
}

// create table footer
$pdf->SetX(7);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(20, 5, "", 0, 0, 'L');
$pdf->Cell(25, 5, "SUBTOTAL(TK)", 1, 0, 'C');
$pdf->Cell(20, 5, $invoice['subtotal'], 1, 1, 'C');

$pdf->SetX(7);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(20, 5, "", 0, 0, 'L');
$pdf->Cell(25, 5, "DISCOUNT(%)", 1, 0, 'C');
$pdf->Cell(20, 5, $invoice['discount'], 1, 1, 'C');


$discount_tk = $invoice['discount'] / 100;
$discount = $discount_tk * $invoice['subtotal'];


$pdf->SetX(7);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(20, 5, "", 0, 0, 'L');
$pdf->Cell(25, 5, "SGST(%)", 1, 0, 'C');
$pdf->Cell(20, 5, $invoice['sgst'], 1, 1, 'C');

$pdf->SetX(7);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(20, 5, "", 0, 0, 'L');
$pdf->Cell(25, 5, "CGST(%)", 1, 0, 'C');
$pdf->Cell(20, 5, $invoice['cgst'], 1, 1, 'C');

$cgst_tk = $invoice['cgst'] / 100;
$sgst_tk = $invoice['sgst'] / 100;


$cgst_amount = $cgst_tk * $invoice['subtotal'];
$sgst_amount = $sgst_tk * $invoice['subtotal'];


$pdf->SetX(7);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(20, 5, "", 0, 0, 'L');
$pdf->Cell(25, 5, "SGST(TK)", 1, 0, 'C');
$pdf->Cell(20, 5, $sgst_amount, 1, 1, 'C');

$pdf->SetX(7);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(20, 5, "", 0, 0, 'L');
$pdf->Cell(25, 5, "CGST(TK)", 1, 0, 'C');
$pdf->Cell(20, 5, $cgst_amount, 1, 1, 'C');

$pdf->SetX(7);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(20, 5, "", 0, 0, 'L');
$pdf->Cell(25, 5, "TOTAL(TK)", 1, 0, 'C');
$pdf->Cell(20, 5, $invoice['total'], 1, 1, 'C');

$pdf->SetX(7);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(20, 5, "", 0, 0, 'L');
$pdf->Cell(25, 5, "PAID(TK)", 1, 0, 'C');
$pdf->Cell(20, 5, $invoice['paid'], 1, 1, 'C');

$pdf->SetX(7);
$pdf->SetFont('Courier', 'B', 8);
$pdf->Cell(20, 5, "", 0, 0, 'L');
$pdf->Cell(25, 5, "DUE(TK)", 1, 0, 'C');
$pdf->Cell(20, 5, $invoice['due'], 1, 1, 'C');






$pdf->Output();
