<?php
session_start();

include 'db_connection.php';

$stmt = $conn->prepare("SELECT * FROM Items WHERE QuantityAvailable > 0");
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CircleFit</title>
    <link rel="stylesheet" href="css/main.css" />
</head>

<body>
    <div class="container">
        <?php include 'includes/nav.php'; ?>
        <main>
            <div class="items-container">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div id="item-<?php echo $row["ItemID"]; ?>" class="item">
                        <img src="<?php echo $row["ItemImage"]; ?>" alt="Item image" class="item-image">
                        <a href="item.php?item_id=<?php echo $row["ItemID"]; ?>" class="item-name">
                            <?php echo $row['ItemName']; ?>
                        </a>
                        <p class="item-description">
                            <?php echo $row['Description']; ?>
                        </p>
                        <p class="item-price">$
                            <?php echo $row['Price']; ?>
                        </p>
                        <p class="item-quantity">
                            <?php echo $row['QuantityAvailable']; ?>
                            left in stock!
                        </p>
                        <a class="action-link" href="addToCart.php?item_id=<?php echo $row['ItemID']; ?>">Add to Cart</a>
                    </div>
                <?php endwhile; ?>
            </div>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html>