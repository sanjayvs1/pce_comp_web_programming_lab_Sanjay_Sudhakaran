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

// Get current user ID from session
$userID = $_SESSION['UserID'];

// Query to get all orders for the current user along with their items
$sql = "SELECT o.OrderID, o.OrderDate, o.Status, o.TotalAmount, i.ItemID, i.ItemName, i.Description, i.Price, oi.Quantity 
        FROM Orders o
        INNER JOIN OrderItems oi ON o.OrderID = oi.OrderID
        INNER JOIN Items i ON oi.ItemID = i.ItemID
        WHERE o.UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are any orders for the user
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
                'Items' => array()
            );
        }
        $orders[$orderID]['Items'][] = array(
            'ItemID' => $row['ItemID'],
            'ItemName' => $row['ItemName'],
            'Description' => $row['Description'],
            'Price' => $row['Price'],
            'Quantity' => $row['Quantity']
        );
    }
    $orders = array_reverse($orders, true);
}
$stmt->close();
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
                        <h4>Items:</h4>
                        <ul>
                            <?php foreach ($order['Items'] as $item): ?>
                                <li><strong>Item Name</strong>:
                                    <?php echo $item['ItemName']; ?> |
                                    <strong>Price</strong>:
                                    <?php echo $item['Price']; ?> |
                                    <strong>Quantity</strong>:
                                    <?php echo $item['Quantity']; ?>
                                    <?php
                                    // Check if the item is fulfilled
                                    $stmt = $conn->prepare("SELECT Fulfilled FROM OrderItemFulfillment WHERE OrderID = ? AND ItemID = ?");
                                    $stmt->bind_param("ii", $orderID, $item['ItemID']);
                                    $stmt->execute();
                                    $stmt->store_result();
                                    if ($stmt->num_rows > 0) {
                                        echo "<a class='action-link' href='feedback.php?item_id=" . $item['ItemID'] . "'>Give Feedback</a>";
                                    }
                                    $stmt->close();
                                    ?>
                                </li>
                                <br>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You haven't placed an order yet!</p>
            <?php endif; ?>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html>