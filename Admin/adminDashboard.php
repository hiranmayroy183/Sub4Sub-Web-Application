<?php
// adminDashboard.php
include '../functions/header.php';
include '../functions/auth.php';

if (!isAdminLoggedIn()) {
    header('Location: adminLogin.php');
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        $message = 'Invalid CSRF token.';
        $messageType = 'error';
    } else {
        $newUsername = $_POST['username'];
        $newPassword = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword !== $confirmPassword) {
            $message = 'Passwords do not match.';
            $messageType = 'error';
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admin SET username = ?, password = ? WHERE id = 1");
            if ($stmt->execute([$newUsername, $hashedPassword])) {
                $message = 'Username and password updated successfully.';
                $messageType = 'success';
            } else {
                $message = 'Failed to update username and password.';
                $messageType = 'error';
            }
        }
    }
}

// Fetch current admin username
$stmt = $pdo->prepare("SELECT username FROM admin WHERE id = 1");
$stmt->execute();
$currentUsername = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Admin Dashboard</h1>
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-6">
                <ul>
                    <li><a href="adminAbout.php">Edit About Page</a></li>
                    <li><a href="adminContact.php">Edit Contact Page</a></li>
                    <li><a href="adminTOS.php">Edit Terms of Service</a></li>
                    <li><a href="adminPrivacy.php">Edit Privacy Policy</a></li>
                    <li><a href="adminFaq.php">Edit FAQs</a></li>
                    <li><a href="adminUsers.php">Edit Users</a></li>
                </ul>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2>Admin Info</h2>
                        <form action="adminDashboard.php" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <div class="mb-3">
                                <label for="username" class="form-label">New Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($currentUsername); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php include '../functions/footer.php'; ?>
