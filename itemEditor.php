<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

if (isset($_GET["item_id"]) && !empty($_GET["item_id"])) {
    $itemID = $_GET["item_id"];

    $stmt = $conn->prepare("SELECT * FROM Items WHERE ItemID = ?");
    $stmt->bind_param("i", $itemID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();
    } else {
        echo "Item not found.";
        exit();
    }
} else {
    echo "Item ID not provided.";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Item</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <div class="container">
        <?php include 'includes/nav.php'; ?>
        <main>
            <h2>Update Item</h2>
            <form action="updateItem.php" method="post">
                <input type="hidden" name="item_id" value="<?php echo $item['ItemID']; ?>">
                <label for="itemName">Item Name:</label><br>
                <input type="text" id="itemName" name="itemName" value="<?php echo $item['ItemName']; ?>" required><br>

                <label for="description">Description:</label><br>
                <textarea id="description" name="description" rows="4" cols="50"
                    required><?php echo $item['Description']; ?></textarea><br>

                <label for="price">Price:</label><br>
                <input type="text" id="price" name="price" value="<?php echo $item['Price']; ?>" required><br>

                <label for="quantityAvailable">Quantity Available:</label><br>
                <input type="number" id="quantityAvailable" name="quantityAvailable"
                    value="<?php echo $item['QuantityAvailable']; ?>" required><br>

                <input type="submit" value="Update Item">
            </form>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>

</body>

</html>