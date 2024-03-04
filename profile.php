<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  exit;
}
// Include the database connection file
require_once "db_connection.php";
// Retrieve user information from the database
$sql = "SELECT id, username, email FROM users WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
  // Bind variables to the prepared statement as parameters
  $stmt->bind_param("i", $_SESSION["id"]);
  // Attempt to execute the prepared statement
  if ($stmt->execute()) {
    // Store result
    $stmt->store_result();
    // Check if user exists
    if ($stmt->num_rows == 1) {
      // Bind result variables
      $stmt->bind_result($id, $username, $email);
      if ($stmt->fetch()) {
        // User information
        $user_id = $id;
        $user_username = $username;
        $user_email = $email;
      }
    } else {
      // Redirect to error page if user doesn't exist
      header("location: error.php");
      exit;
    }
  } else {
    // Redirect to error page if query execution fails
    header("location: error.php");
    exit;
  }
  // Close statement
  $stmt->close();
}
// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CircleFit - Profile</title>
  <link rel="stylesheet" href="css/main.css" />
</head>
<body>
  <?php include 'includes/nav.php'; ?>
  <div class="container">
    <h2>Profile</h2>
    <p><strong>Username:</strong> <?php echo $user_username; ?></p>
    <p><strong>Email:</strong> <?php echo $user_email; ?></p>
    <p><a href="welcome.php">Back to Welcome Page</a></p>
  </div>
</body>
</html>