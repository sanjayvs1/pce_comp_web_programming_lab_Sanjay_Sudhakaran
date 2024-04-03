<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_connection.php';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve item_id and quantity from the POST parameters
    $itemID = $_POST["item_id"];
    $newQuantity = $_POST["quantity"];

    // Get UserID from session
    $userID = $_SESSION["UserID"];

    // Update the quantity in the Cart table
    $stmt = $conn->prepare("UPDATE Cart SET Quantity = ? WHERE UserID = ? AND ItemID = ?");
    $stmt->bind_param("iii", $newQuantity, $userID, $itemID);

    if ($stmt->execute()) {
        echo "Quantity updated successfully.";
    } else {
        echo "Error updating quantity: " . $conn->error;
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>
