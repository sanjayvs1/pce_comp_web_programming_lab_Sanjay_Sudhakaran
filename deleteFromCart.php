<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if cart_id is provided in the POST data
    if (isset($_POST["cart_id"]) && !empty($_POST["cart_id"])) {
        // Sanitize the cart_id to prevent SQL injection
        $cartId = mysqli_real_escape_string($conn, $_POST["cart_id"]);
        
        // Get UserID from session
        $userId = $_SESSION["UserID"];
        
        // Delete the item from the cart table
        $stmt = $conn->prepare("DELETE FROM Cart WHERE CartID = ? AND UserID = ?");
        $stmt->bind_param("ii", $cartId, $userId);
        
        if ($stmt->execute()) {
            echo "Item deleted successfully from the cart.";
        } else {
            echo "Error deleting item from the cart: " . $conn->error;
        }
        
        $stmt->close();
    } else {
        echo "Cart ID not provided.";
    }
} else {
    // If the request method is not POST, redirect back to the shopping cart page
    header("Location: cart.php");
    exit();
}

$conn->close();
?>
