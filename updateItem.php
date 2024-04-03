<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemID = $_POST["item_id"];
    $itemName = $_POST["itemName"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $quantityAvailable = $_POST["quantityAvailable"];

    $stmt = $conn->prepare("UPDATE Items SET ItemName = ?, `Description` = ?, Price = ?, QuantityAvailable = ? WHERE ItemID = ?");
    $stmt->bind_param("ssdii", $itemName, $description, $price, $quantityAvailable, $itemID);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error updating item: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
