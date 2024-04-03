<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if(isset($_GET["item_id"])) {
        $itemID = $_GET["item_id"];
        $stmt = $conn->prepare("DELETE FROM Items WHERE ItemID = ?");
        $stmt->bind_param("i", $itemID);

        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Error deleting item: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Item ID is missing.";
    }
}

$conn->close();
