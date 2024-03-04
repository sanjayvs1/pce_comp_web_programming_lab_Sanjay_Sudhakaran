<?php
// Include the database connection file
require_once "db_connection.php";

// Define variables and initialize with empty values
$username = $email = $password = "";
$username_err = $email_err = $password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Validate username
  if (empty(trim($_POST["username"]))) {
    $username_err = "Please enter a username.";
  } else {
    $username = trim($_POST["username"]);
  }

  // Validate email
  if (empty(trim($_POST["email"]))) {
    $email_err = "Please enter an email address.";
  } else {
    $email = trim($_POST["email"]);
  }

  // Validate password
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter a password.";
  } else {
    $password = trim($_POST["password"]);
  }

  // Check input errors before inserting into database
  if (empty($username_err) && empty($email_err) && empty($password_err)) {
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Prepare an insert statement
    $sql = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
      // Bind variables to the prepared statement as parameters
      $stmt->bind_param("sss", $username, $email, $passwordHash);

      // Attempt to execute the prepared statement
      if ($stmt->execute()) {
        // Redirect to login page
        header("location: login.php");
        exit;
      } else {
        echo "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      $stmt->close();
    }
  }

  // Close connection
  $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CircleFit - Sign Up</title>
  <link rel="stylesheet" href="css/main.css" />
</head>

<body>
  <?php include 'includes/nav.php'; ?>
  <div class="container">
    <form class="signup-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <h2>Sign Up</h2>
      <input type="text" placeholder="Username" name="username" value="<?php echo $username; ?>">
      <input type="email" placeholder="Email" name="email" value="<?php echo $email; ?>">
      <input type="password" placeholder="Password" name="password" value="<?php echo $password; ?>">
      <button type="submit">Sign Up</button>
      <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </form>
  </div>
</body>

</html>