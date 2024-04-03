<?php
session_start();

include 'db_connection.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit();
}

if ($_SESSION["UserType"] !== "Seller") {
    echo "You do not have permission to access this page.";
    exit();
}

$UserID = $_SESSION["UserID"];

$stmt = $conn->prepare("SELECT * FROM Items WHERE SellerID = ?");
$stmt->bind_param("i", $UserID);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CircleFit - Dashboard</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <div class="container">
        <?php include 'includes/nav.php'; ?>
        <main>
            <form class="login-form" action="addItem.php" method="post" enctype="multipart/form-data">
                <h2>Add Item</h2>
                <input placeholder="Item Name" type="text" id="itemName" name="itemName" required><br>

                <textarea placeholder="Description" id="description" name="description" rows="4"
                    cols="50"></textarea><br>

                <input placeholder="Price" type="text" id="price" name="price" required><br>

                <input placeholder="Quantity Available" type="number" id="quantityAvailable" name="quantityAvailable"
                    required><br>

                <label for="itemImage">Item Image:</label><br>
                <input type="file" id="itemImage" name="itemImage"><br>

                <input type="submit" value="Add Item">
            </form>
            <h2>Items</h2>
            <div class="items-container">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="item">
                        <img src="<?php echo $row["ItemImage"]; ?>" alt="Item image" class="item-image">
                        <p class="item-name">
                            <?php echo $row['ItemName']; ?>
                        </p>
                        <p class="item-description">
                            <?php echo $row['Description']; ?>
                        </p>
                        <p class="item-price">$
                            <?php echo $row['Price']; ?>
                        </p>
                        <p class="item-quantity">Quantity:
                            <?php echo $row['QuantityAvailable']; ?>
                        </p>
                        <a class="action-link-2" href="deleteItem.php?item_id=<?php echo $row['ItemID']; ?>">Delete Item</a>
                        <a class="action-link" href="itemEditor.php?item_id=<?php echo $row['ItemID']; ?>">Update Item</a>
                    </div>
                <?php endwhile; ?>
            </div>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html>