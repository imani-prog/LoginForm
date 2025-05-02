<?php
session_start();
require_once "database.php";  // Ensure this file correctly connects to your database

// Initialize variables for success message and errors
$successMessage = "";
$errors = [];

if (isset($_POST["submit"])) {
    // Get form input values
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $passwordRepeat = $_POST["confirm_password"];
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);  // Hash the password

    // Validate fields
    if (empty($username) || empty($email) || empty($password) || empty($passwordRepeat)) {
        $errors[] = "All fields are required";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email is not valid";
    }
    if ($password !== $passwordRepeat) {
        $errors[] = "Passwords do not match";
    }

    // If there are no validation errors, insert into the database
    if (empty($errors)) {
        $sql = "INSERT INTO viewers (username, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $passwordHash);
            if (mysqli_stmt_execute($stmt)) {
                // Success: Store success message in session, then redirect
                $_SESSION["success"] = "Registration successful!";
                header("Location: reg.php");  // Redirect to the same page
                exit();
            } else {
                $errors[] = "Error during registration. Please try again.";
            }
        } else {
            $errors[] = "Database error: Could not prepare statement.";
        }
    }

    // Store errors in session and redirect
    if (!empty($errors)) {
        $_SESSION["errors"] = $errors;
        header("Location: reg.php");  // Redirect to refresh the page
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        /* Style for the login button inside the form */
        .login-btn {
            color: white;
            background-color: blueviolet;
            padding: 10px;
            font-size: large;
            border-radius: 10px;
            width: 100%; /* Make the button take full width inside the form */
            margin-top: 10px; /* Adds space above the button */
            cursor: pointer;
        }

        .login-btn:hover {
            background-color: darkviolet; /* Change color when hovered */
        }
    </style>
</head>
<body>
    <div id="form">
        <!-- Display success message if set -->
        <?php
        if (!empty($_SESSION["success"])) {
            echo "<p style='color:green;'>" . $_SESSION["success"] . "</p>";
            unset($_SESSION["success"]);  // Clear success message after displaying
        }

        // Display validation errors if any
        if (!empty($_SESSION["errors"])) {
            foreach ($_SESSION["errors"] as $error) {
                echo "<p style='color:red;'>$error</p>";
            }
            unset($_SESSION["errors"]);  // Clear errors after displaying
        }
        ?>

        <h1>Registration Form</h1>
        <form action="reg.php" method="post">

            <label for="username">Username:</label><br>
            <input type="text" id="username" name="username" required><br><br>

            <label for="email">Email:</label><br>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Password:</label><br>
            <input type="password" id="password" name="password" required><br><br>

            <label for="confirm_password">Re-enter Password:</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>

            <input type="submit" id="btn" name="submit" value="Register">

            <!-- Login Button inside the form -->
            <a href="login.php">
                <button type="button" class="login-btn">Login</button>
            </a>

        </form>
    </div>
</body>
</html>

