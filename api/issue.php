<?php
header('Content-Type: application/json');

require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGetRequest($conn);
        break;
    case 'POST':
        handlePostRequest($conn);
        break;
    case 'PUT':
        handlePutRequest($conn);
        break;
    case 'DELETE':
        handleDeleteRequest($conn);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}

function handleGetRequest($conn) {
    $sql = "SELECT 
                i.id, 
                i.bus_number, 
                i.permit_number, 
                i.issue_date, 
                p.id AS product_id, 
                p.name AS product_name, 
                p.serial_number, 
                c.name AS category_name,
                ip.quantity
            FROM issue i
            JOIN issue_product ip ON i.id = ip.issue_id
            JOIN product p ON ip.product_id = p.id
            JOIN category c ON p.category_id = c.id";
    $result = mysqli_query($conn, $sql);
    $issues = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $issue_id = $row['id'];
        if (!isset($issues[$issue_id])) {
            $issues[$issue_id] = [
                'id' => $row['id'],
                'bus_number' => $row['bus_number'],
                'permit_number' => $row['permit_number'],
                'issue_date' => $row['issue_date'],
                'products' => []
            ];
        }
        $issues[$issue_id]['products'][] = [
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'serial_number' => $row['serial_number'],
            'category_name' => $row['category_name'],
            'quantity' => $row['quantity']
        ];
    }
    echo json_encode(array_values($issues));
}


function handlePostRequest($conn) {
    $data = json_decode(file_get_contents('php://input'), true);

    $bus_number = $data['bus_number'];
    $permit_number = $data['permit_number'];
    $products = $data['products'];

    $sql = "INSERT INTO issue (bus_number, permit_number) VALUES ('$bus_number', '$permit_number')";
    if (mysqli_query($conn, $sql)) {
        $issue_id = mysqli_insert_id($conn);

        foreach ($products as $product) {
            $product_id = $product['product_id'];
            $quantity = $product['quantity'];
            $sql = "INSERT INTO issue_product (issue_id, product_id, quantity) VALUES ($issue_id, $product_id, $quantity)";
            mysqli_query($conn, $sql);
        }

        echo json_encode(['message' => 'Issue created successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error creating issue: ' . mysqli_error($conn)]);
    }
}

function handlePutRequest($conn) {
    $data = json_decode(file_get_contents('php://input'), true);

    $issue_id = $_GET['id'];
    $bus_number = $data['bus_number'];
    $permit_number = $data['permit_number'];
    $products = $data['products'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("UPDATE issue SET bus_number = ?, permit_number = ? WHERE id = ?");
    $stmt->bind_param("ssi", $bus_number, $permit_number, $issue_id);

    if ($stmt->execute()) {
        $stmt->close();

        // Delete existing products for the issue
        $stmt = $conn->prepare("DELETE FROM issue_product WHERE issue_id = ?");
        $stmt->bind_param("i", $issue_id);
        $stmt->execute();
        $stmt->close();

        // Insert new products for the issue
        $stmt = $conn->prepare("INSERT INTO issue_product (issue_id, product_id, quantity) VALUES (?, ?, ?)");

        foreach ($products as $product) {
            $product_id = $product['product_id'];
            $quantity = $product['quantity'];
            $stmt->bind_param("iii", $issue_id, $product_id, $quantity);
            $stmt->execute();
        }

        $stmt->close();
        echo json_encode(['message' => 'Issue updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error updating issue: ' . $conn->error]);
    }
}


function handleDeleteRequest($conn) {
    if (isset($_GET['id'])) {
        $issue_id = $_GET['id'];

        $sql = "DELETE FROM issue_product WHERE issue_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $issue_id);

        if ($stmt->execute()) {
            $stmt->close();

            $sql = "DELETE FROM issue WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $issue_id);

            if ($stmt->execute()) {
                echo json_encode(['message' => 'Issue deleted successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error deleting issue: ' . $stmt->error]);
            }
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error deleting issue products: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Missing issue ID']);
    }
}


?>
