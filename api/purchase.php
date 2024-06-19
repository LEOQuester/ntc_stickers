<?php
// Database connection
require '../config.php';

header('Content-Type: application/json');

// Function to fetch purchases with additional details
function getPurchases($conn) {
    $sql = "SELECT purchase.id, product.name AS product_name, product.serial_number, category.name AS category_name, 
                   purchase.quantity, purchase.purchase_price, purchase.supplier_name, purchase.purchase_date 
            FROM purchase 
            JOIN product ON purchase.product_id = product.id 
            JOIN category ON product.category_id = category.id";
    $result = mysqli_query($conn, $sql);

    $purchases = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $purchases[] = $row;
        }
        mysqli_free_result($result);
    }
    return $purchases;
}

// Function to add a new purchase
function addPurchase($conn, $data) {
    // Basic sanitization (escape special characters)
    $product_id = mysqli_real_escape_string($conn, $data->product_id);
    $quantity = mysqli_real_escape_string($conn, $data->quantity);
    $purchase_price = mysqli_real_escape_string($conn, $data->purchase_price);
    $supplier_name = mysqli_real_escape_string($conn, $data->supplier_name);
    $purchase_date = mysqli_real_escape_string($conn, $data->purchase_date);

    $sql = "INSERT INTO purchase (product_id, quantity, purchase_price, supplier_name, purchase_date) 
            VALUES ('$product_id', '$quantity', '$purchase_price', '$supplier_name', '$purchase_date')";
    
    $result = mysqli_query($conn, $sql);
    return $result;
}

// Function to update an existing purchase
function updatePurchase($conn, $id, $data) {
    // Basic sanitization (escape special characters)
    $product_id = mysqli_real_escape_string($conn, $data->product_id);
    $quantity = mysqli_real_escape_string($conn, $data->quantity);
    $purchase_price = mysqli_real_escape_string($conn, $data->purchase_price);
    $supplier_name = mysqli_real_escape_string($conn, $data->supplier_name);
    $purchase_date = mysqli_real_escape_string($conn, $data->purchase_date);

    $sql = "UPDATE purchase SET product_id = '$product_id', quantity = '$quantity', 
            purchase_price = '$purchase_price', supplier_name = '$supplier_name', 
            purchase_date = '$purchase_date' WHERE id = $id";
    
    $result = mysqli_query($conn, $sql);
    return $result;
}

// Function to delete a purchase
function deletePurchase($conn, $id) {
    // Basic sanitization (escape special characters)
    $id = mysqli_real_escape_string($conn, $id);

    $sql = "DELETE FROM purchase WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    return $result;
}

// Main API logic
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $purchases = getPurchases($conn);
        echo json_encode($purchases);
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents('php://input'));
        if (addPurchase($conn, $data)) {
            echo json_encode(['message' => 'Purchase added successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error adding purchase']);
        }
        break;
        
    case 'PUT':
        $id = $_GET['id'];
        $data = json_decode(file_get_contents('php://input'));
        if (updatePurchase($conn, $id, $data)) {
            echo json_encode(['message' => 'Purchase updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error updating purchase']);
        }
        break;
        
    case 'DELETE':
        $id = $_GET['id'];
        if (deletePurchase($conn, $id)) {
            echo json_encode(['message' => 'Purchase deleted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Error deleting purchase']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}

$conn->close();
?>
