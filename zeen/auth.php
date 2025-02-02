<?php
session_start();
require_once 'db_config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = "Username already exists.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
                $stmt->bind_param("ss", $username, $hashed_password);

                if ($stmt->execute()) {
                    $success = "Registration successful. You can now log in.";
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        }
    } elseif (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: index.php"); // Redirect to a dashboard or home page
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Authentication - CloudSphere</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1>Welcome to Zeen LMS File Manager</h1>
            <p>Login Koren Ostad</p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="tabs">
                <button class="tab-button active" onclick="switchTab('login')">Login</button>
                <button class="tab-button" onclick="switchTab('register')">Register</button>
            </div>

            <div id="login" class="tab-content active">
                <form method="POST">
                    <div class="form-group">
                        <label for="login-username">Username</label>
                        <input type="text" id="login-username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <input type="password" id="login-password" name="password" required>
                    </div>
                    <button type="submit" name="login" class="btn-primary">Login</button>
                </form>
            </div>

            <div id="register" class="tab-content">
                <form method="POST">
                    <div class="form-group">
                        <label for="register-username">Username</label>
                        <input type="text" id="register-username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="register-password">Password</label>
                        <input type="password" id="register-password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">Confirm Password</label>
                        <input type="password" id="confirm-password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="register" class="btn-primary">Register</button>
                </form>
            </div>

            <div class="guest-link">
                <a href="index.php?guest=1" class="btn-secondary">Continue as Guest</a>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tab content
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });

            // Show the selected tab content
            document.getElementById(tabName).classList.add('active');

            // Update active tab button
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            document.querySelector(`.tab-button[onclick="switchTab('${tabName}')"]`).classList.add('active');
        }
    </script>
</body>
</html>