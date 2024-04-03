<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Retrieve form data
  $uname = $_POST["username"];
  $pword = $_POST["password"];
  $email = $_POST["email"];
  $userType = $_POST["userType"];

  // Validation
  $errors = [];
  if (strlen($uname) < 3 || strlen($uname) > 255) {
    $errors[] = "Username must be between 3 and 255 characters.";
  }
  if (strlen($pword) < 8) {
    $errors[] = "Password must be at least 8 characters long.";
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
  }

  // Check for duplicate username
  include 'db_connection.php';
  $stmt = $conn->prepare("SELECT Username FROM Users WHERE Username = ?");
  $stmt->bind_param("s", $uname);
  $stmt->execute();
  $stmt->store_result();
  if ($stmt->num_rows > 0) {
    $errors[] = "Username already exists. Please choose a different one.";
  }
  $stmt->close();

  // Check for duplicate email
  $stmt = $conn->prepare("SELECT Email FROM Users WHERE Email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();
  if ($stmt->num_rows > 0) {
    $errors[] = "Email already exists. Please use a different one.";
  }
  $stmt->close();

  // Handle profile picture upload
  $profilePicture = $_FILES["profilePicture"]["name"];
  $profilePictureTmpName = $_FILES["profilePicture"]["tmp_name"];
  $profilePictureSize = $_FILES["profilePicture"]["size"];
  $profilePictureError = $_FILES["profilePicture"]["error"];

  if ($profilePictureError === UPLOAD_ERR_OK) {
    // Check file size
    if ($profilePictureSize > 5000000) {
      $errors[] = "Profile picture is too large. Maximum size allowed is 5MB.";
    }
    // Move uploaded file to desired directory
    $uploadPath = "profile_pictures/" . basename($profilePicture);
    if (!move_uploaded_file($profilePictureTmpName, $uploadPath)) {
      $errors[] = "Failed to upload profile picture.";
    }
  } else {
    $errors[] = "Error uploading profile picture.";
  }

  // If no errors, proceed with registration
  if (empty ($errors)) {
    $hashedPassword = password_hash($pword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO Users (Username, `Password`, Email, UserType, ProfilePicture) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $uname, $hashedPassword, $email, $userType, $uploadPath);
    if ($stmt->execute()) {
      echo "<p>Registration successful!</p>";
      header("Location: login.php");
      exit(); // Ensure script stops executing after redirection
    } else {
      echo "<p>Error: " . $conn->error . "</p>";
    }
    $stmt->close();
  } else {
    echo "<ul>";
    foreach ($errors as $error) {
      echo "<li>$error</li>";
    }
    echo "</ul>";
  }

  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Registration</title>
  <link rel="stylesheet" href="css/main.css">
</head>

<body>
  <div class="container">
  <?php include 'includes/nav.php'; ?>
  <main>
  <form class="login-form" action="signup.php" method="post" enctype="multipart/form-data">
      <h2>User Registration</h2>
      <input placeholder="Username" type="text" id="username" name="username" required><br>

      <input placeholder="Password" type="password" id="password" name="password" required><br>

      <input placeholder="Email" type="email" id="email" name="email" required><br>

      <label for="userType">User Type:</label><br>
      <select id="userType" name="userType">
        <option value="Buyer">Buyer</option>
        <option value="Seller">Seller</option>
      </select><br>

      <label for="profilePicture">Profile Picture:</label><br>
      <input type="file" id="profilePicture" name="profilePicture"><br>

      <button type="submit" value="Register">Sign Up</button>
      <p>Already have an account? <a href="login.php">Log in here</a>.</p>
    </form>
  </main>
  <?php include 'includes/footer.php'; ?>
  </div>
</body>

</html>