<?php
require_once('../config.php');

// Function to calculate available quantity
function getAvailableQuantity($conn, $product_id) {
    // Calculate total purchased quantity
    $purchase_sql = "SELECT SUM(quantity) AS total_purchased FROM purchase WHERE product_id = $product_id";
    $purchase_result = $conn->query($purchase_sql);
    $total_purchased = $purchase_result->fetch_assoc()['total_purchased'] ?? 0;

    // Calculate total issued quantity
    $issue_sql = "SELECT SUM(quantity) AS total_issued FROM issue_product WHERE product_id = $product_id";
    $issue_result = $conn->query($issue_sql);
    $total_issued = $issue_result->fetch_assoc()['total_issued'] ?? 0;

    // Calculate available quantity
    $available_qty = $total_purchased - $total_issued;
    return $available_qty;
}

// Retrieve products with low stock
$sql = "SELECT 
            p.id AS product_id, 
            p.name AS product_name, 
            p.serial_number, 
            c.name AS category_name, 
            p.min_units 
        FROM product p
        JOIN category c ON p.category_id = c.id";

$result = $conn->query($sql);
$low_stock_products = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $available_qty = getAvailableQuantity($conn, $product_id);
        $row['available_qty'] = $available_qty;
        $low_stock_products[] = $row;
    }
}

echo json_encode($low_stock_products);
