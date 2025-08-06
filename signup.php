<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (!empty($username) && !empty($email) && !empty($password)) {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            $success_message = "Account created successfully🎉 <a href='login.php'>Login now</a>";
        } else {
            $error_message = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "All fields are required!";
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nazem Sign Up</title>
    <link rel="stylesheet" href="style_signup.css">
    <link rel="icon" type="image/png" href="logo1.png">
</head>

<body>
    <div class="sidebar">
        <img src="logo.png" alt="Logo"> 
        <p>Where productivity meets simplicity. 🚀<br>Let نــظّــم sort your chaos.</p>
    </div>

    <div class="login-container">
        <h2>Sign Up</h2>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Username" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Example@mail.com" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="ُEnter Your Password" required>

            <button type="submit">Sign Up</button>
        </form>
            <div class="login-link">
        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?= $success_message ?></div>
        <?php else: ?>
            <p>Already Have An Account?🤩 <a href="login.php">Login</a></p>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?= $error_message ?></div>
        <?php endif; ?>
    </div>

    </div>
</body>

</html>
