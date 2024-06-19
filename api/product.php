<?php
include '../config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['pid'])) {
            // Retrieve a specific product by ID
            $pid = intval($_GET['pid']);
            $sql = "SELECT p.*, c.name AS categoryname FROM product p INNER JOIN category c ON p.category_id = c.id WHERE p.id = $pid";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();
                echo json_encode($product);
            } else {
                echo json_encode(["message" => "Product not found"]);
            }
        } else {
            // Retrieve all products with their category names
            $sql = "SELECT p.*, c.name AS categoryname FROM product p INNER JOIN category c ON p.category_id = c.id";
            $result = $conn->query($sql);
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            echo json_encode($products);
        }
        break;

    case 'POST':
        // Add a new product
        $data = json_decode(file_get_contents("php://input"), true);
        $serial_number = $data['serial_number'];
        $name = isset($data['name']) ? $data['name'] : ''; // Check if 'name' is set
        $category_id = $data['category_id'];
        $min_units = isset($data['min_units']) ? $data['min_units'] : 0;

        $sql = "INSERT INTO product (serial_number, name, category_id, min_units) VALUES ('$serial_number', '$name', '$category_id', '$min_units')";
        
        if ($conn->query($sql) === TRUE) {
            $last_id = $conn->insert_id;
            $sql_select = "SELECT p.*, c.name AS categoryname FROM product p INNER JOIN category c ON p.category_id = c.id WHERE p.id = $last_id";
            $result = $conn->query($sql_select);
            echo json_encode(["message" => "Product created successfully", "data" => $result->fetch_assoc()]);
        } else {
            echo json_encode(["error" => $conn->error]);
        }
        break;

    case 'PUT':
        // Parse the PUT request body
        $data = json_decode(file_get_contents("php://input"), true);

        // Validate and sanitize input values
        $pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0; // Assuming 'pid' is passed in the query string
        $serial_number = isset($data['serial_number']) ? $data['serial_number'] : '';
        $name = isset($data['name']) ? $data['name'] : '';
        $category_id = isset($data['category_id']) ? intval($data['category_id']) : 0; // Convert to integer
        $min_units = isset($data['min_units']) ? intval($data['min_units']) : 0; // Convert to integer

        if ($pid <= 0 || empty($serial_number) || empty($category_id)) {
            echo json_encode(["error" => "Invalid request. Product ID, serial number, and category ID are required"]);
            exit;
        }

        // Validate that category_id exists in the category table (assuming your database structure)
        $check_category_sql = "SELECT id FROM category WHERE id = $category_id";
        $check_category_result = $conn->query($check_category_sql);

        if ($check_category_result->num_rows === 0) {
            echo json_encode(["error" => "Category with id $category_id does not exist"]);
            exit; // Exit script if category doesn't exist
        }

        // Update the product
        $update_sql = "UPDATE product SET serial_number='$serial_number', name='$name', category_id=$category_id, min_units=$min_units WHERE id=$pid";

        if ($conn->query($update_sql) === TRUE) {
            // Retrieve updated product data
            $select_sql = "SELECT p.*, c.name AS categoryname FROM product p INNER JOIN category c ON p.category_id = c.id WHERE p.id = $pid";
            $select_result = $conn->query($select_sql);

            if ($select_result->num_rows > 0) {
                $product = $select_result->fetch_assoc();
                echo json_encode(["message" => "Product updated successfully", "data" => $product]);
            } else {
                echo json_encode(["message" => "Product updated, but could not fetch updated data"]);
            }
        } else {
            echo json_encode(["error" => "Error updating product: " . $conn->error]);
        }
        break;
    
        
    case 'DELETE':
        // Delete a product
        if (isset($_GET['pid'])) {
            $pid = intval($_GET['pid']);
            $sql = "DELETE FROM product WHERE id=$pid";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(["message" => "Product deleted successfully"]);
            } else {
                echo json_encode(["error" => $conn->error]);
            }
        }
        break;

    default:
        echo json_encode(["message" => "Invalid request"]);
        break;
}
?>
