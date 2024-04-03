<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Redirect the user to the login page if not logged in
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_connection.php';

// Retrieve form data
$itemName = $_POST["itemName"];
$description = $_POST["description"];
$price = $_POST["price"];
$quantityAvailable = $_POST["quantityAvailable"];
$sellerID = $_SESSION["UserID"]; 
// Handle item image upload
$itemImage = ""; // Default value
if ($_FILES["itemImage"]["error"] === UPLOAD_ERR_OK) {
    $itemImageTmpName = $_FILES["itemImage"]["tmp_name"];
    $itemImageName = $_FILES["itemImage"]["name"];
    $itemImagePath = "item_images/" . $itemImageName; // Destination path for storing the uploaded image

    if (move_uploaded_file($itemImageTmpName, $itemImagePath)) {
        $itemImage = $itemImagePath; // Set the item image path if upload is successful
    } else {
        echo "<p>Error uploading item image.</p>";
    }
}

// Insert item into the database
$stmt = $conn->prepare("INSERT INTO items (ItemName, `Description`, Price, QuantityAvailable, SellerID, ItemImage) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssdiss", $itemName, $description, $price, $quantityAvailable, $sellerID, $itemImage);


if ($stmt->execute()) {
    echo "<p>Item added successfully!</p>";
    header("Location: dashboard.php");
} else {
    echo "<p>Error: " . $conn->error . "</p>";
}

// Close statement and connection
$stmt->close();
$conn->close();
