<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db_connection.php';

// Get UserID from session
$userID = $_SESSION["UserID"];

// Fetch items from the cart for the logged-in user
$stmt = $conn->prepare("SELECT Cart.CartID, Items.ItemID, Items.ItemName, Items.Description, Items.Price, Cart.Quantity, Items.QuantityAvailable, Items.ItemImage FROM Cart INNER JOIN Items ON Cart.ItemID = Items.ItemID WHERE Cart.UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/main.css">
    <script>
        function updateQuantity(itemId, newQuantity) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    console.log(xhr.responseText);
                }
            };
            xhr.open("POST", "updateCart.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            if (newQuantity === '10+') {
                document.getElementById('quantity-custom-' + itemId).style.display = 'none';
                newQuantity = document.getElementById('quantity-custom-' + itemId).value;
            }
            xhr.send("item_id=" + itemId + "&quantity=" + newQuantity);
        }
        function deleteItemFromCart(cartId, itemId) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "deleteFromCart.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var itemToRemove = document.getElementById("item-" + itemId);
                    if (itemToRemove) {
                        itemToRemove.remove();
                    }
                }
            };
            xhr.send("cart_id=" + cartId);
        }

    </script>
</head>

<body>
    <div class="container">
        <?php include 'includes/nav.php'; ?>
        <main>
            <h2>Shopping Cart</h2>
            <a class="action-link" href="placeOrder.php">Place Order</a>
            <br><br>
            <div class="items-container">
                <?php
                // Check if there are items in the cart
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                        ?>
                        <div class="item item-<?php echo $row['ItemID']; ?>" id="item-<?php echo $row['ItemID']; ?>">
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
                            <div class="item-quantity">
                                <?php
                                $maxQuantity = $row['QuantityAvailable'];
                                $selectedQuantity = $row['Quantity'];
                                ?>
                                <select id="quantity-<?php echo $row['ItemID']; ?>"
                                    onchange="updateQuantity(<?php echo $row['ItemID']; ?>, this.value)">
                                    <?php
                                    for ($i = 1; $i <= $maxQuantity; $i++) {
                                        echo "<option value='$i'";
                                        if ($i == $selectedQuantity) {
                                            echo " selected";
                                        }
                                        echo ">$i</option>";
                                    }
                                    ?>
                                    <!-- <option value="10+">10+</option> -->
                                </select>
                                <!-- <input type="number" id="quantity-custom-<?php echo $row['ItemID']; ?>" style="display: none;"
                                min="11" max="<?php echo $maxQuantity; ?>" placeholder="Enter quantity"> -->
                            </div>
                            <button
                                onclick="deleteItemFromCart(<?php echo $row['CartID']; ?>, <?php echo $row['ItemID']; ?>)">Delete
                                Item</button>
                        </div>
                        <?php
                    endwhile;
                else:
                    ?>
                    <p>No items in the cart.</p>
                <?php endif; ?>
            </div>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>

</body>

</html>