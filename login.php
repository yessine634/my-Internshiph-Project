<?php
session_start(); // Start session at the very top
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Find user by email
    $user = $collection->findOne(['email' => $email]);

    if ($user && password_verify($password, $user['password'])) {
        // Login success: set session and redirect
        $_SESSION["user_id"] = (string)$user->_id;
        $_SESSION["user_name"] = $user['name'];
        require 'vendor/autoload.php';
        $client = new MongoDB\Client("mongodb://localhost:27017");
        $db = $client->elect_lab;

        


        header("Location: homepage.php");
        exit();
    } else {
        // Login failed: redirect back with error param
        header("Location: login.php?error=invalid_credentials");

        exit();
    }
}
if (isset($_GET['error']) && $_GET['error'] == "invalid_credentials") {
    echo "<script>
        alert('Invalid email or password.');
        if (window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.delete('error');
            window.history.replaceState({}, document.title, url.toString());
        }
    </script>";
}
        

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <!-- Material Symbols Filled -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Filled" rel="stylesheet" />
    <link rel="stylesheet" href="styles/sign_up.css">
</head>
<body>
    <div class="navbar">
        <div class="bg-blue-500 p-2 rounded-lg inline-block">
            <img src="logo/artibedded_logo.webp" alt="Logo" class="logo-image">
        </div>
    </div>
    <div class="main-content">
        <div class="card">
            <div class="illustration">
                <img src="logo/artibedded_illustration.jpg" >
            </div>
            <div class="form-section" >
                <h1>Login</h1>
                <form method="POST" action="login.php">
                    <div class="input-row">
                        <span class="material-symbols-filled">
                            <img src="logo/mail.png" class="logo_style">
                        </span>
                        <input required type="email" name="email" placeholder="Email " id="emailInput">
                    </div>
                    <div class="input-row">
                        <span class="material-symbols-filled">
                            <img src="logo/lock_logo.png" class="logo_style">
                        </span>
                        <input required type="password" name="password" placeholder="Password" id="passwordInput">
                    </div>
                    <button class="signup-btn" type="submit" name="login">Login</button>
                    <div class="login-link">
                        Don't have an account?
                        <a href="signup.php">Sign up</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>