<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_connection.php';

// Check if item_id is provided in the URL
if (isset($_GET["item_id"]) && !empty($_GET["item_id"])) {
    // Sanitize and validate item_id
    $itemID = filter_var($_GET["item_id"], FILTER_VALIDATE_INT);
    if ($itemID === false) {
        echo "Invalid item ID.";
        exit();
    }
    
    // Get UserID from session
    $userID = $_SESSION["UserID"];

    // Check if the item already exists in the cart
    $stmt = $conn->prepare("SELECT * FROM Cart WHERE UserID = ? AND ItemID = ?");
    $stmt->bind_param("ii", $userID, $itemID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Item already exists in the cart
        header("Location: index.php");
        exit();
    } else {
        // Item does not exist in the cart, so add it
        $quantity = 1; // Default quantity
        $totalPrice = 0; // Default total price
        
        // Insert item into the cart
        $stmt = $conn->prepare("INSERT INTO Cart (UserID, ItemID, Quantity, TotalPrice) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $userID, $itemID, $quantity, $totalPrice);
        if ($stmt->execute()) {
            header("Location: index.php");
        } else {
            echo "Error adding item to cart: " . $conn->error;
        }
    }
} else {
    header("Location: index.php");
}

// Close statement and connection
$stmt->close();
$conn->close();
