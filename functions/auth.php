<?php
include 'db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        if (function_exists('random_bytes')) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } else {
            $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
        }
    }
}

function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function register($email, $password, $confirm_password, $youtube_channel_name) {
    global $pdo;
    if ($password !== $confirm_password) {
        return [false, "Passwords do not match."];
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (email, password, youtube_channel_name) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$email, $hash, $youtube_channel_name]);
        return [true, "Registration successful."];
    } catch (\PDOException $e) {
        if ($e->getCode() == 23000) { // Integrity constraint violation: 1062 Duplicate entry
            return [false, "Email already registered."];
        } else {
            throw $e;
        }
    }
}

function login($email, $password) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        return [true, "Login successful."];
    } else {
        return [false, "Invalid email or password."];
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function resetPassword($email) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $newPassword = "newpassword";
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $updateStmt->execute([$hash, $email]);

        return [true, "Password reset successful. Your new password is '$newPassword'. Please change it after logging in."];
    } else {
        return [false, "Email not found."];
    }
}

function updateProfile($email, $full_name, $location_address, $youtube_channel, $youtube_channel_name) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT youtube_channel, youtube_channel_changed FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user['youtube_channel_changed'] && $user['youtube_channel'] !== $youtube_channel) {
        return [false, "You can only change the YouTube channel URL once."];
    }

    if (!strpos($youtube_channel, 'youtube.com')) {
        return [false, "The YouTube channel URL must contain 'youtube.com'."];
    }

    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, location_address = ?, youtube_channel = ?, youtube_channel_name = ?, youtube_channel_changed = ? WHERE email = ?");
    $stmt->execute([$full_name, $location_address, $youtube_channel, $youtube_channel_name, ($user['youtube_channel'] !== $youtube_channel) ? 1 : $user['youtube_channel_changed'], $email]);

    return [true, "Profile updated successfully."];
}

function changePassword($email, $new_password) {
    global $pdo;
    $hash = password_hash($new_password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$hash, $email]);

    return [true, "Password updated successfully."];
}

function getUserProfile($email) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT full_name, location_address, youtube_channel, youtube_channel_name, youtube_channel_changed FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}
?>
