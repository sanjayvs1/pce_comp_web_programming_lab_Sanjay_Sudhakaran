<?php
session_start();

include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION["UserID"];

// Retrieve user's current information from the database
$stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $uname = $_POST["username"];
    $pword = $_POST["password"];
    $email = $_POST["email"];
    $userType = $_POST["userType"];

    // Validation
    $errors = [];
    // Validate username, password, email, etc.

    // Handle profile picture upload
    // Upload and validation logic here...

    // Update user information in the database if there are no errors
    // Update database query here...

    // Redirect to profile page or show success message
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/main.css">
</head>

<body>
    <div class="container">
        <?php include 'includes/nav.php'; ?>
        <main>
            <form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                enctype="multipart/form-data">
                <h2>Edit Profile</h2>
                <input placeholder="Username" type="text" id="username" name="username"
                    value="<?php echo $user['Username']; ?>" required><br>

                <input placeholder="Email" type="email" id="email" name="email" value="<?php echo $user['Email']; ?>"
                    required><br>

                <label for="userType">User Type:</label><br>
                <select id="userType" name="userType">
                    <option value="Buyer" <?php if ($user['UserType'] === 'Buyer')
                        echo 'selected'; ?>>Buyer</option>
                    <option value="Seller" <?php if ($user['UserType'] === 'Seller')
                        echo 'selected'; ?>>Seller</option>
                </select><br>

                <label for="profilePicture">Profile Picture:</label><br>
                <input type="file" id="profilePicture" name="profilePicture"><br>

                <button type="submit" value="Update">Update</button>
            </form>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html>