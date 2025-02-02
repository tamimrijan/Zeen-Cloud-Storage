<?php
session_start();
require_once 'db_config.php';

function register_user($username, $password) {
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashed_password);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

function login_user($username, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true;
        }
    }
    return false;
}

function logout_user() {
    unset($_SESSION['user_id']);
    unset($_SESSION['username']);
    session_destroy();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}
?>

