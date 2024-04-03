<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    // Redirect the user to the login page or display an error message
    header("Location: login.php");
    exit();
}

// Include database connection
require_once 'db_connection.php';

// Get current seller ID from session
$sellerID = $_SESSION['UserID'];

// Check if an order item has been marked as fulfilled
if (isset($_GET['order_id']) && isset($_GET['item_id'])) {
    $orderID = $_GET['order_id'];
    $itemID = $_GET['item_id'];

    // Update the fulfillment status in the OrderItemFulfillment table
    $updateQuery = "UPDATE OrderItemFulfillment SET Fulfilled = TRUE WHERE OrderID = ? AND ItemID = ? AND SellerID = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("iii", $orderID, $itemID, $sellerID);
    $stmt->execute();
    $stmt->close();

    // Check if all items in the order have been fulfilled
    $checkCompletionQuery = "SELECT COUNT(*) AS num_items FROM OrderItemFulfillment WHERE OrderID = ? AND Fulfilled = FALSE";
    $stmt = $conn->prepare($checkCompletionQuery);
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $numItems = $row['num_items'];

    if ($numItems === 0) {
        // If all items have been fulfilled, update the order status to "Completed"
        $updateOrderStatusQuery = "UPDATE Orders SET Status = 'Completed' WHERE OrderID = ?";
        $stmt = $conn->prepare($updateOrderStatusQuery);
        $stmt->bind_param("s", $orderID);
        $stmt->execute();
    }
}

// Query to get orders containing items listed by the seller
$sql = "SELECT o.OrderID, o.OrderDate, o.Status, o.TotalAmount, d.Name AS DeliveryName, d.Address AS DeliveryAddress, d.Pincode AS DeliveryPincode, d.PaymentMethod AS DeliveryPaymentMethod,
        i.ItemID, i.ItemName, i.Description, i.Price, oi.Quantity, of.Fulfilled
        FROM Orders o
        INNER JOIN OrderItems oi ON o.OrderID = oi.OrderID
        INNER JOIN Items i ON oi.ItemID = i.ItemID
        LEFT JOIN DeliveryDetails d ON o.UserID = d.UserID
        LEFT JOIN OrderItemFulfillment of ON o.OrderID = of.OrderID AND oi.ItemID = of.ItemID
        WHERE i.SellerID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sellerID);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are any orders for the seller
if ($result->num_rows > 0) {
    $orders = array();
    // Iterate through the result set and group items by order
    while ($row = $result->fetch_assoc()) {
        $orderID = $row['OrderID'];
        if (!isset($orders[$orderID])) {
            $orders[$orderID] = array(
                'OrderDate' => $row['OrderDate'],
                'Status' => $row['Status'],
                'TotalAmount' => $row['TotalAmount'],
                'DeliveryDetails' => array(
                    'Name' => $row['DeliveryName'],
                    'Address' => $row['DeliveryAddress'],
                    'Pincode' => $row['DeliveryPincode'],
                    'PaymentMethod' => $row['DeliveryPaymentMethod']
                ),
                'Items' => array()
            );
        }
        $orders[$orderID]['Items'][] = array(
            'ItemID' => $row['ItemID'],
            'ItemName' => $row['ItemName'],
            'Description' => $row['Description'],
            'Price' => $row['Price'],
            'Quantity' => $row['Quantity'],
            'Fulfilled' => $row['Fulfilled']
        );
    }
    $orders = array_reverse($orders, true);
}

// Count the number of orders fulfilled
$stmt = $conn->prepare("SELECT COUNT(*) AS num_orders_fulfilled FROM OrderItemFulfillment WHERE SellerID = ? AND Fulfilled = TRUE");
$stmt->bind_param("i", $sellerID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$numOrdersFulfilled = $row['num_orders_fulfilled'];

// Count the number of orders unfulfilled
$stmt = $conn->prepare("SELECT COUNT(*) AS num_orders_unfulfilled FROM OrderItemFulfillment WHERE SellerID = ? AND Fulfilled = FALSE");
$stmt->bind_param("i", $sellerID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$numOrdersUnfulfilled = $row['num_orders_unfulfilled'];

$stmt->close();
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
            <p>Number of orders fulfilled:
                <?php echo $numOrdersFulfilled; ?>
            </p>
            <p>Number of orders unfulfilled:
                <?php echo $numOrdersUnfulfilled; ?>
            </p>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $orderID => $order): ?>
                    <div class="order">
                        <h3>Order ID:
                            <?php echo $orderID; ?>
                        </h3>
                        <p>Order Date:
                            <?php echo $order['OrderDate']; ?>
                        </p>
                        <p>Status:
                            <?php echo $order['Status']; ?>
                        </p>
                        <p>Total Amount:
                            <?php echo $order['TotalAmount']; ?>
                        </p>
                        <h4>Delivery Details:</h4>
                        <p>Name: <?php echo $order['DeliveryDetails']['Name']; ?></p>
                        <p>Address: <?php echo $order['DeliveryDetails']['Address']; ?></p>
                        <p>Pincode: <?php echo $order['DeliveryDetails']['Pincode']; ?></p>
                        <p>Payment Method: <?php echo $order['DeliveryDetails']['PaymentMethod']; ?></p>
                        <h4>Items:</h4>
                        <ul>
                            <?php foreach ($order['Items'] as $item): ?>
                                <li>
                                    <strong>Item Name</strong>:
                                    <?php echo $item['ItemName']; ?> |
                                    <!-- <strong>Description</strong>:
                                <?php echo $item['Description']; ?> | -->
                                    <strong>Price</strong>:
                                    <?php echo $item['Price']; ?> |
                                    <strong>Quantity</strong>:
                                    <?php echo $item['Quantity']; ?>
                                    <?php if (!$item['Fulfilled']): ?>
                                        <a class="action-link"
                                            href='?order_id=<?php echo $orderID; ?>&item_id=<?php echo $item['ItemID']; ?>'>Mark as
                                            Fulfilled</a>
                                    <?php endif; ?>
                                </li>
                                <br>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No orders found for your items.</p>
            <?php endif; ?>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html>
