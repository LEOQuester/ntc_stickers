<?php

// Include database configuration
require_once('../config.php');

// Set response content type to JSON
header("Content-Type: application/json");

// Handle HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Handle GET request to fetch categories
        if (isset($_GET['id'])) {
            // Fetch a single category by ID
            $categoryId = mysqli_real_escape_string($conn, $_GET['id']);
            $query = "SELECT * FROM category WHERE id = '$categoryId'";
        } else {
            // Fetch all categories
            $query = "SELECT * FROM category";
        }

        $result = mysqli_query($conn, $query);

        if (!$result) {
            http_response_code(500);
            echo json_encode(['error' => mysqli_error($conn)]);
            exit;
        }

        $categories = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }

        echo json_encode($categories);
        break;

    case 'POST':
        // Handle POST request to create a new category
        $data = json_decode(file_get_contents("php://input"), true);
        $name = mysqli_real_escape_string($conn, $data['name']);
        $color = mysqli_real_escape_string($conn, $data['color']);

        $query = "INSERT INTO category (name, color) VALUES ('$name', '$color')";
        if (mysqli_query($conn, $query)) {
            $response = ['message' => 'Category created successfully'];
            echo json_encode($response);
        } else {
            http_response_code(500);
            echo json_encode(['error' => mysqli_error($conn)]);
        }
        break;

    case 'PUT':
        // Handle PUT request to update an existing category
        $data = json_decode(file_get_contents("php://input"), true);
        $categoryId = mysqli_real_escape_string($conn, $_GET['id']);
        $name = mysqli_real_escape_string($conn, $data['name']);
        $color = mysqli_real_escape_string($conn, $data['color']);

        $query = "UPDATE category SET name = '$name', color = '$color' WHERE id = '$categoryId'";
        if (mysqli_query($conn, $query)) {
            $response = ['message' => 'Category updated successfully'];
            echo json_encode($response);
        } else {
            http_response_code(500);
            echo json_encode(['error' => mysqli_error($conn)]);
        }
        break;

    case 'DELETE':
        // Handle DELETE request to delete a category
        if (isset($_GET['id'])) {
            $categoryId = mysqli_real_escape_string($conn, $_GET['id']);
            $query = "DELETE FROM category WHERE id = '$categoryId'";
            
            if (mysqli_query($conn, $query)) {
                $response = ['message' => 'Category id: ' . $categoryId .' removed Successfully'];
                echo json_encode($response);
            } else {
                $response = ['message' => 'Cannot Delete a category that has been used by products'];
                echo json_encode($response);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Category ID not provided']);
        }
        break;
        

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

mysqli_close($conn);
?>
