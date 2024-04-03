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

// Initialize variables for delivery details
$deliveryDetails = [
    'Name' => '',
    'Address' => '',
    'Pincode' => '',
    'PaymentMethod' => ''
];

// Check if delivery details exist for the user
$stmt = $conn->prepare("SELECT * FROM deliverydetails WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

// If delivery details exist, retrieve them
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $deliveryDetails['Name'] = $row['Name'];
    $deliveryDetails['Address'] = $row['Address'];
    $deliveryDetails['Pincode'] = $row['Pincode'];
    $deliveryDetails['PaymentMethod'] = $row['PaymentMethod'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Insert into Orders table
    $orderDate = date('Y-m-d');
    $totalAmount = 0; // Initialize total amount
    $status = 'Pending'; // Set initial status
    $stmt = $conn->prepare("INSERT INTO Orders (UserID, OrderDate, `Status`, TotalAmount) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $userID, $orderDate, $status, $totalAmount);
    $stmt->execute();
    $orderID = $stmt->insert_id;
    $name = $_POST["name"];
    $address = $_POST["address"];
    $pincode = $_POST["pincode"];
    $paymentMethod = $_POST["payment"];

    // Insert delivery details into the deliverydetails table
    $stmt = $conn->prepare("INSERT INTO deliverydetails (UserID, Name, Address, Pincode, PaymentMethod) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $userID, $name, $address, $pincode, $paymentMethod);
    $stmt->execute();

    // Insert into OrderItems table based on items in the cart
    $stmt = $conn->prepare("INSERT INTO OrderItems (OrderID, ItemID, Quantity, Price) SELECT ?, Items.ItemID, Cart.Quantity, Items.Price FROM Cart INNER JOIN Items ON Cart.ItemID = Items.ItemID WHERE Cart.UserID = ?");
    $stmt->bind_param("ii", $orderID, $userID);
    $stmt->execute();

    // Insert into OrderItemFulfillment table based on items in the cart
    $stmt = $conn->prepare("INSERT INTO OrderItemFulfillment (OrderID, ItemID, SellerID, Fulfilled) SELECT ?, Items.ItemID, Items.SellerID, FALSE FROM Cart INNER JOIN Items ON Cart.ItemID = Items.ItemID WHERE Cart.UserID = ?");
    $stmt->bind_param("ii", $orderID, $userID);
    $stmt->execute();

    // Decrement the QuantityAvailable in the Items table
    $stmt = $conn->prepare("UPDATE Items INNER JOIN Cart ON Items.ItemID = Cart.ItemID SET Items.QuantityAvailable = Items.QuantityAvailable - Cart.Quantity WHERE Cart.UserID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();

    // Delete items from the cart
    $stmt = $conn->prepare("DELETE FROM Cart WHERE UserID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();

    // Update total amount in Orders table based on the sum of prices from OrderItems
    $stmt = $conn->prepare("UPDATE Orders SET TotalAmount = (SELECT SUM(Price * Quantity) FROM OrderItems WHERE OrderID = ?) WHERE OrderID = ?");
    $stmt->bind_param("ii", $orderID, $orderID);
    $stmt->execute();

    header("Location: index.php");
    exit();
}

// Initialize total price
$totalPrice = 0;

// Fetch items from the cart for the logged-in user
$stmt = $conn->prepare("SELECT Cart.CartID, Items.ItemID, Items.ItemName, Items.Description, Items.Price, Cart.Quantity, Items.QuantityAvailable, Items.ItemImage FROM Cart INNER JOIN Items ON Cart.ItemID = Items.ItemID WHERE Cart.UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are items in the cart
if ($result->num_rows > 0) {
    // Loop through each item in the cart
    while ($row = $result->fetch_assoc()) {
        // Calculate the total price for each item and add it to the total price
        $itemTotalPrice = $row['Price'] * $row['Quantity'];
        $totalPrice += $itemTotalPrice;

        // Store the item details in the $itemsInCart array
        $item = [
            'CartID' => $row['CartID'],
            'ItemID' => $row['ItemID'],
            'ItemName' => $row['ItemName'],
            'Description' => $row['Description'],
            'Price' => $row['Price'],
            'Quantity' => $row['Quantity'],
            'QuantityAvailable' => $row['QuantityAvailable'],
            'ItemImage' => $row['ItemImage'],
            'TotalPrice' => $itemTotalPrice  // Optionally, you can store the total price for each item
        ];

        // Add the item to the $itemsInCart array
        $itemsInCart[] = $item;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <div class="container">
    <?php include 'includes/nav.php'; ?>
    <main>
        <h2>Place Order</h2>
        <div class="items-list">
            <?php if (!empty($itemsInCart)): ?>
                <?php foreach ($itemsInCart as $item): ?>
                    <div class="item-li item-<?php echo $item['ItemID']; ?>" id="item-<?php echo $item['ItemID']; ?>">
                        <img src="<?php echo $item["ItemImage"]; ?>" alt="Item image" class="item-image">
                        <p class="item-name">
                            <?php echo $item['ItemName']; ?>
                        </p>
                        <p class="item-description">
                            <?php echo $item['Description']; ?>
                        </p>
                        <p class="item-price">$
                            <?php echo $item['Price']; ?>
                        </p>
                        <p class="item-quantity">Quantity:
                            <?php echo $item['Quantity']; ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No items in the cart.</p>
            <?php endif; ?>
        </div>
        <h3>Total Price: $
            <?php echo $totalPrice; ?>
        </h3>
        <h3>Payment Options:</h3>
        <form action="placeOrder.php" method="post">
            <label for="name">Name:</label><br>
            <input type="text" id="name" name="name" value="<?php echo $deliveryDetails['Name']; ?>" required><br><br>

            <label for="address">Address:</label><br>
            <textarea class="add" id="address" rows="7" cols="35" name="address"
                required><?php echo $deliveryDetails['Address']; ?></textarea><br><br>

            <label for="pincode">Pincode:</label><br>
            <input type="text" id="pincode" name="pincode" value="<?php echo $deliveryDetails['Pincode']; ?>"
                required><br><br>

            Payment Method:
            <input type="radio" class="pmeth" id="cod" name="payment" value="cod" onclick="togglePaymentMethod('cod')"
                <?php if ($deliveryDetails['PaymentMethod'] === 'cod')
                    echo 'checked'; ?>>COD
            <input type="radio" class="pmeth" id="upi" name="payment" value="upi" onclick="togglePaymentMethod('upi')"
                <?php if ($deliveryDetails['PaymentMethod'] === 'upi')
                    echo 'checked'; ?>>UPI
            <div class="cod" id="cod-div" style="display: none;">
                <br>
                <h5 class="ind">Please hand over the amount to the delivery person at the time of delivery!!</h5>
            </div>
            <div class="upi" id="upi-div" style="display: none;">
                <h5 class="upi ind">Scan below QR code for UPI payment</h5>
                <img src="./assets/images/upi qr code.jpeg" alt="scan qr code " class="qr">
            </div>
            <br><br>
            <input type="submit" value="Place Order">
        </form>
            </main>
        <?php include 'includes/footer.php'; ?>
    </div>
    
</body>

</html>