<?php
session_start();

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    // temp placeholder login logic 
    if (empty($email) || empty($password)) {
        $errorMessage = "Please enter both email and password.";
    } else {
        // i will add later 
        $errorMessage = "Login form submitted successfully.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChronoSync Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <h1>CHRONOSYNC</h1>
                <p>Hospitals &amp; Institutions Management</p>
            </div>

            <div class="login-body">
                <?php if (!empty($errorMessage)) : ?>
                    <div class="message-box">
                        <?php echo htmlspecialchars($errorMessage); ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="post">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="volunteer@example.com"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="************"
                            required
                        >
                    </div>

                    <div class="forgot-password">
                        <a href="#">Forgot Password?</a>
                    </div>

                    <button type="submit" class="login-btn">Log In</button>
                </form>

                <div class="signup-text">
                    Don’t have an account? <a href="signup.php">Sign Up</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>