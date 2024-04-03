<?php
// Include database connection
include 'db_connection.php';

// Check if UserID is set in the session (assuming you're storing UserID in the session after login)
session_start();
if (!isset($_SESSION["UserID"])) {
    // Redirect to login page if UserID is not set
    header("Location: login.php");
    exit();
}

// Retrieve UserID from session
$UserID = $_SESSION["UserID"];

// Fetch user data from the database
$stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $UserID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Fetch user data
    $row = $result->fetch_assoc();
    $Username = $row['Username'];
    $Email = $row['Email'];
    $UserType = $row['UserType'];
    $ProfilePicture = $row['ProfilePicture'];
} else {
    // No user found with the given UserID
    echo "User not found.";
}

// Close statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <div class="container">
    <?php include 'includes/nav.php'; ?>
        <main>
        <div class="profile">
            <?php if (!empty($ProfilePicture)): ?>
                <img src="<?php echo $ProfilePicture; ?>" alt="Profile Picture" width="100">
            <?php endif; ?>
            <div class="profile-content">
                <p><strong>Username:</strong>
                    <?php echo $Username; ?>
                </p>
                <p><strong>Email:</strong>
                    <?php echo $Email; ?>
                </p>
                <p><strong>User Type:</strong>
                    <?php echo $UserType; ?>
                </p>
                <a class="action-link" href="profileEditor.php">Edit Profile</a>
            </div>
        </div>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html>