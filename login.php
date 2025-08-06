<?php 
include('db.php');
//Session 
if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
    $timeout_message = "⏰ Session expired due to inactivity. Please log in again.";
}

// Check if the user has submitted both email and password via the POST request
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
}
// Prepare a SQL query to select the user with the provided email
$sql = "SELECT * FROM users WHERE email = ?"; //? is placeholder 

$stmt = $conn->prepare($sql);               // Prepare the SQL statement to prevent SQL injection
$stmt->bind_param("s", $email);             // Bind the email parameter to the SQL query
$stmt->execute();                           // Execute the prepared statement
$result = $stmt->get_result();              // Get the result set from the executed query

// Check if a user with the provided email exists
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();  // Fetch user data from the result

    // Verify the submitted password with the hashed password from the database
    if (password_verify($password, $row['password'])) {
        session_start();                               // Start a new session
        $_SESSION['user_id'] = $row['id'];             // Store user ID in session
        $_SESSION['username'] = $row['username'];      // Store username in session
        header("Location: home.php");                  // Redirect to the home page
        exit();
    } else {
        // Password is incorrect
        $error_message = "<div class='error'>Incorrect Password⚠️</div>";
    }
} else {
    // Email not found in the database
    $error_message = "<div class='error'>Not Valid Email⚠️</div>";
}

$stmt->close();  // Close the prepared statement


$conn->close();  
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nazem Login</title>
    <link rel="stylesheet" href="style_login.css">
    <link rel="icon" type="image/png" href="logo1.png">
</head>

<body>

<div class="sidebar">
    <h1><img src='logo.png'></h1>
    <p>Organize Your Daily Tasks, Set Deadlines, And Stay Productive With Us!</p>
</div>

<div class="login-container">
    <h2>Login To Your Account</h2>
    <form method="POST" action="login.php">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" autocomplete='off' placeholder='Example@mail.com'required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" placeholder='Enter Your Password' required>

        <button type="submit">Login</button>
    </form>

    <div class="signup-link">
    <p>Don't Have An Account Yet?😧 <a href="signup.php">Sign Up!</a></p>

    <?php
    if (!empty($timeout_message)) {
        echo "<div class='error'>$timeout_message</div>";
    }
    if (!empty($error_message)) {
        echo "<div class='session_error'>$error_message</div>";
    }
    ?>
</div>    
</div>
</body>
</html>