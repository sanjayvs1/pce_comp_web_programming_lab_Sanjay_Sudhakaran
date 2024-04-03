<?php

// Include database connection
require_once 'db_connection.php';

session_start();

// Check if ItemID is provided in the URL
if (isset($_GET['item_id'])) {
    // Sanitize the input to prevent SQL injection
    $itemID = $conn->real_escape_string($_GET['item_id']);

    // Prepare SQL statement
    $sql = "SELECT * FROM Items WHERE ItemID = $itemID";

    // Execute SQL query
    $result = $conn->query($sql);

} else {
    echo "ItemID parameter is missing.";
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My Orders</title>
    <link rel="stylesheet" href="css/main.css" />
</head>

<body>
    <div class="container">
        <?php include 'includes/nav.php'; ?>
        <main>
            <div class="productDetails">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>

                        <img class="product-img" src="<?php echo $row['ItemImage']; ?>" alt="Item Image">
                        <div class="product-content">
                            <h2>
                                <?php echo $row['ItemName']; ?>
                            </h2>
                            <p><strong>Description:</strong>
                                <?php echo $row['Description']; ?>
                            </p>
                            <p><strong>Price:</strong> $
                                <?php echo $row['Price']; ?>
                            </p>
                            <p><strong>Quantity Available:</strong>
                                <?php echo $row['QuantityAvailable']; ?>
                            </p>
                            <p><strong>Seller ID:</strong>
                                <?php echo $row['SellerID']; ?>
                            </p>
                            <br>
                            <a class="action-link" href="addToCart.php?item_id=<?php echo $row['ItemID']; ?>">Add to Cart</a>
                        </div>

                        <?php
                    }
                } else {
                    echo "<p>Item not found.</p>";
                }
                ?>
            </div>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html>