<?php
session_start();

include 'db_connection.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_POST["name"])) {
        $errors["name"] = "Name is required";
    } elseif (!preg_match("/^[a-zA-Z]+$/", $_POST["name"])) {
        $errors["name"] = "Name can only contain letters";
    }

    if (empty($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Invalid email address";
    }

    if (empty($_POST["rating"])) {
        $errors["rating"] = "Rating is required";
    }

    if (empty($_POST["feedback"])) {
        $errors["feedback"] = "Feedback is required";
    }

    if (empty($_POST["agree"])) {
        $errors["agree"] = "Please agree to the terms and conditions";
    }

    if (empty($errors)) {
        // No errors, proceed with inserting data into the database
        $name = $_POST["name"];
        $email = $_POST["email"];
        $rating = $_POST["rating"];
        $feedback = $_POST["feedback"];
        $userID = isset($_SESSION["UserID"]) ? $_SESSION["UserID"] : null; // Assuming you have a UserID stored in the session
        $itemID = $_POST["item_id"]; // Assuming the item_id is passed as a GET parameter

        // Prepare and execute the SQL query to insert data into the table
        $stmt = $conn->prepare("INSERT INTO Feedback (UserID, ItemID, Rating, Comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $userID, $itemID, $rating, $feedback);
        $stmt->execute();

        // Reset the form data
        $_POST = [];

        // Show success message
        echo "<script>alert('Form submitted successfully!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>
    <link rel="stylesheet" href="css/main.css" />
</head>

<body>

    <div class="container">
        <?php include 'includes/nav.php'; ?>
        <main>
            <form class="login-form" id="feedbackForm" method="post"
                action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <h2>Feedback Form</h2>
                <input type="hidden" name="item_id"
                    value="<?php echo isset($_GET["item_id"]) ? $_GET["item_id"] : ''; ?>">
                <input placeholder="Name" type="text" id="name" name="name"
                    value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                <span class="error">
                    <?php echo isset($errors['name']) ? $errors['name'] : ''; ?>
                </span>

                <input placeholder="Email" type="email" id="email" name="email"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <span class="error">
                    <?php echo isset($errors['email']) ? $errors['email'] : ''; ?>
                </span>

                <select id="rating" name="rating">
                    <option value="">Rating</option>
                    <?php
                    $ratings = ['1', '2', '3', '4', '5'];
                    foreach ($ratings as $rating) {
                        echo '<option value="' . $rating . '"';
                        if (isset($_POST['rating']) && $_POST['rating'] === $rating) {
                            echo ' selected';
                        }
                        echo '>' . $rating . '</option>';
                    }
                    ?>
                </select>
                <span class="error">
                    <?php echo isset($errors['rating']) ? $errors['rating'] : ''; ?>
                </span>

                <textarea placeholder="Feedback" id="feedback"
                    name="feedback"><?php echo isset($_POST['feedback']) ? htmlspecialchars($_POST['feedback']) : ''; ?></textarea>
                <span class="error">
                    <?php echo isset($errors['feedback']) ? $errors['feedback'] : ''; ?>
                </span>
                <div>
                    <input type="checkbox" id="agree" name="agree">
                    <label for="agree">I agree to the terms and conditions</label>
                    <span class="error">
                        <?php echo isset($errors['agree']) ? $errors['agree'] : ''; ?>
                    </span>
                </div>

                <button type="submit">Submit</button>
            </form>
        </main>
        <?php include 'includes/footer.php'; ?>
    </div>

    <?php
    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (empty($_POST["name"])) {
            $errors["name"] = "Name is required";
        } elseif (!preg_match("/^[a-zA-Z]+$/", $_POST["name"])) {
            $errors["name"] = "Name can only contain letters";
        }

        if (empty($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Invalid email address";
        }

        if (empty($_POST["rating"])) {
            $errors["rating"] = "Rating is required";
        }

        if (empty($_POST["feedback"])) {
            $errors["feedback"] = "Feedback is required";
        }

        if (empty($_POST["agree"])) {
            $errors["agree"] = "Please agree to the terms and conditions";
        }

        if (empty($errors)) {
            echo "<script>alert('Form submitted successfully!');</script>";
            $_POST = [];
        }
    }
    ?>
</body>

</html>