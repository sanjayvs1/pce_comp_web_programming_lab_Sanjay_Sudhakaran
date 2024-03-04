<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect them to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Display a welcome message with the user's username
$welcome_message = "Welcome, " . $_SESSION["username"] . "!";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="css/main.css" />
</head>

<body>
    <?php include 'includes/nav.php'; ?>
    <div class="container">
        <h2>Welcome</h2>
        <p><?php echo $welcome_message; ?></p>
        <p><a href="logout.php">Logout</a></p>
    </div>
</body>

</html>