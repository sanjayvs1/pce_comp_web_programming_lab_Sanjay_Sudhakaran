<?php
session_start();

include 'db_connection.php';

if (isset($_GET['search_query'])) {
    $searchQuery = filter_var($_GET['search_query'], FILTER_SANITIZE_STRING);

    $sql = "SELECT * FROM Items WHERE ItemName LIKE '%$searchQuery%'";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Search Results</title>
    <link rel="stylesheet" href="css/main.css" />
</head>

<body>
    <div class="container">
        <?php include 'includes/nav.php'; ?>
        <main>
            <h2>Search Results for "
                <?php echo $searchQuery; ?>"
            </h2>
            <div class="items-container">
                <?php if (isset($result)): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div id="item-<?php echo $row["ItemID"]; ?>" class="item">
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
                            <a href="addToCart.php?item_id=<?php echo $row['ItemID']; ?>">Add to Cart</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>We currently don't have that item!</p>
                <?php endif; ?>
            </div>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html>