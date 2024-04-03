<?php
// Include database connection
require_once 'db_connection.php';

// Query to get all contact messages
$sql = "SELECT * FROM ContactMessages";
$result = $conn->query($sql);

// Check if there are any messages
if ($result->num_rows > 0) {
    // Output each message as a div
    while ($row = $result->fetch_assoc()) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;'>
                <strong>Message ID:</strong> {$row['MessageID']}<br>
                <strong>User ID:</strong> {$row['UserID']}<br>
                <strong>Title:</strong> {$row['Title']}<br>
                <strong>Message:</strong> {$row['Message']}<br>
                <strong>Timestamp:</strong> {$row['Timestamp']}<br>
            </div>";
    }
} else {
    echo "No messages found.";
}

// Close the database connection
$conn->close();
?>
