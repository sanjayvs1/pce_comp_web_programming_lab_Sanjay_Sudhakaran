<?php
session_start();

// Include database connection
require_once 'db_connection.php';

$errors = []; // Initialize an array to store validation errors

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate email
    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address";
    }

    // Validate title (max length: 100 characters)
    if (empty($_POST['title']) || strlen($_POST['title']) > 100) {
        $errors['title'] = "Title is required and must be less than 100 characters";
    }

    // Validate message (max length: 500 characters)
    if (empty($_POST['message']) || strlen($_POST['message']) > 500) {
        $errors['message'] = "Message is required and must be less than 500 characters";
    }

    // If no validation errors, proceed to insert into the database
    if (empty($errors)) {
        // Get user ID from session
        $userID = $_SESSION['UserID'];

        // Get form data
        $title = $_POST['title'];
        $message = $_POST['message'];

        // Prepare and execute SQL statement to insert message into the database
        $stmt = $conn->prepare("INSERT INTO ContactMessages (UserID, Title, Message) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userID, $title, $message);
        $stmt->execute();
        $stmt->close();

        // Close the database connection
        $conn->close();

        // Redirect back to the contact us page or display a success message
        header("Location: {$_SERVER['PHP_SELF']}?success=true");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="css/main.css">
    <script>
        function validateForm() {
            var email = document.getElementById('email').value;
            var title = document.getElementById('title').value;
            var message = document.getElementById('message').value;

            if (!email || !email.match(/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/)) {
                alert('Please enter a valid email address');
                return false;
            }

            if (!title || title.length > 100) {
                alert('Title is required and must be less than 100 characters');
                return false;
            }

            if (!message || message.length > 500) {
                alert('Message is required and must be less than 500 characters');
                return false;
            }

            return true;
        }
    </script>
</head>

<body>
    <div class="container">
        <?php include 'includes/nav.php'; ?>
        <main>
            <form class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                onsubmit="return validateForm()">
                <h2>Contact Us</h2>
                <input placeholder="Email" type="email" id="email" name="email" required><br>
                <input placeholder="Title" type="text" id="title" name="title" required><br>
                <textarea placeholder="Message" id="message" name="message" rows="4" cols="50" required></textarea><br>
                <button type="submit">Submit</button>
            </form>
            <?php
            // Display validation errors
            foreach ($errors as $error) {
                echo "<p style='color: red; text-align: center;'>$error</p>";
            }

            // Display success message if present in URL query parameters
            if (isset($_GET['success']) && $_GET['success'] === "true") {
                echo "<p style='color: green; text-align: center;'>Message submitted successfully!</p>";
            }
            ?>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>
</body>

</html>