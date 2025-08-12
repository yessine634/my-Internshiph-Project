<?php
require 'database.php'; // MongoDB connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $repeatPassword = $_POST["repeatPassword"];

    if (strlen($password) < 8) {
    header("Location: signup.php?error=short_password");
    exit();
}

    if ($password !== $repeatPassword) {
        header("Location: signup.php?error=password_mismatch");
        exit();
    }

    $existingUser = $collection->findOne(["email" => $email]);
    if ($existingUser) {
        header("Location: signup.php?error=email_exists");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $insertResult = $collection->insertOne([
            "name" => $name,
            "email" => $email,
            "password" => $hashedPassword,
            "created_at" => new \MongoDB\BSON\UTCDateTime((new DateTime())->getTimestamp() * 1000)
        ]);

        if ($insertResult->getInsertedCount() === 1) {
            header("Location: signup.php?success=1");
            exit();
        } else {
            header("Location: signup.php?error=server_error");
            exit();
        }
    } catch (Exception $e) {
        header("Location: signup.php?error=mongo_error");
        exit();
    }
}

// Display error messages using JavaScript alert
if (isset($_GET['error'])) {
    if ($_GET['error'] == "password_mismatch") {
        echo "<script>alert('Passwords do not match!');</script>";
    } elseif ($_GET['error'] == "email_exists") {
        echo "<script>alert('Email already exists!');</script>";
    } elseif ($_GET['error'] == "server_error") {
        echo "<script>alert('Server error! Please try again later.');</script>";
    } elseif ($_GET['error'] == "mongo_error") {
        echo "<script>alert('MongoDB error occurred.');</script>";
    }
    elseif ($_GET['error'] == "short_password") {
    echo "<script>alert('Password must be at least 8 characters long!');</script>";
}
}

if (isset($_GET['success'])) {
    echo "<script>alert('Account created successfully!');</script>";
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
       
    
    </style>
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
            <div class="form-section">
                <h1>Sign up</h1>
                <form method="POST" action="signup.php">
                    <div class="input-row">
                        <span class="material-symbols-filled">
                            <img src="logo/account.png" class="logo_style">
                        </span>
                        <input required type="text" name="name" placeholder="Name" id="NameInput">
                    </div>
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
                        <input required type="password" name="password" placeholder="Password" id="passwordInput" minlength="8">
                    </div>
                    <div class="input-row">
                        <span class="material-symbols-filled">
                            <img src="logo/lock_logo.png" class="logo_style">
                        </span>
                        <input required type="password" name="repeatPassword" placeholder="Repeat Password" id="repeatPasswordInput" minlength="8"  >
                    </div>
                    <button class="signup-btn" type="submit" name="submit">Sign up</button>
                    <div class="login-link">
                        Already have an account?
                        <a href="login.php">Log in</a>
                    </div>
                </form>
            </div>
        </div>
    </div>


</body>

</html>