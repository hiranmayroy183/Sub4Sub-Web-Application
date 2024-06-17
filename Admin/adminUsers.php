<?php
include '../functions/header.php';
include '../functions/db.php';
include '../functions/auth.php';

if (!isAdminLoggedIn()) {
    header('Location: adminLogin.php');
    exit;
}

generateCsrfToken();

$message = '';
$messageType = '';

function validateUserInput($data) {
    $errors = [];
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }
    if (empty($data['username']) || !preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
        $errors[] = "Invalid username. Only alphanumeric characters and underscores are allowed.";
    }
    if (isset($data['password']) && strlen($data['password']) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    if (isset($data['password']) && $data['password'] !== $data['confirm_password']) {
        $errors[] = "Passwords do not match.";
    }
    if (empty($data['youtube_channel_url']) || !preg_match('/^https:\/\/www\.youtube\.com\//', $data['youtube_channel_url'])) {
        $errors[] = "Invalid YouTube channel URL.";
    }
    return $errors;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        $message = 'Invalid CSRF token.';
        $messageType = 'error';
    } else {
        if (isset($_POST['add_user'])) {
            $inputData = [
                'email' => $_POST['email'],
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'confirm_password' => $_POST['confirm_password'],
                'youtube_channel_name' => $_POST['youtube_channel_name'],
                'youtube_channel_url' => $_POST['youtube_channel_url'],
                'location' => $_POST['location']
            ];

            $errors = validateUserInput($inputData);
            if (empty($errors)) {
                $registerResult = register($inputData['email'], $inputData['password'], $inputData['confirm_password'], $inputData['username'], $inputData['youtube_channel_name'], $inputData['youtube_channel_url'], $inputData['location']);
                if ($registerResult[0]) {
                    $message = 'User added successfully.';
                    $messageType = 'success';
                } else {
                    $message = $registerResult[1];
                    $messageType = 'error';
                }
            } else {
                $message = implode('<br>', $errors);
                $messageType = 'error';
            }
        } elseif (isset($_POST['update_user'])) {
            $inputData = [
                'email' => $_POST['email'],
                'username' => $_POST['username'],
                'full_name' => $_POST['full_name'],
                'location' => $_POST['location'],
                'youtube_channel_name' => $_POST['youtube_channel_name'],
                'youtube_channel_url' => $_POST['youtube_channel_url'],
                'new_password' => $_POST['new_password']
            ];

            $errors = validateUserInput($inputData);
            if (empty($errors)) {
                $updateResult = updateProfile($inputData['email'], $inputData['full_name'], $inputData['location'], $inputData['youtube_channel_url'], $inputData['youtube_channel_name']);
                if ($updateResult[0]) {
                    if (!empty($inputData['new_password'])) {
                        list($success, $passwordMessage) = changePassword($inputData['email'], $inputData['new_password']);
                        $message .= " " . $passwordMessage;
                    }
                    $message = $updateResult[1];
                    $messageType = 'success';
                } else {
                    $message = $updateResult[1];
                    $messageType = 'error';
                }
            } else {
                $message = implode('<br>', $errors);
                $messageType = 'error';
            }
        } elseif (isset($_POST['delete_user'])) {
            $email = $_POST['email'];
            $stmt = $pdo->prepare("DELETE FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $message = 'User deleted successfully.';
            $messageType = 'success';
        }
    }
}

// Fetch users
$stmt = $pdo->query("SELECT email, username, full_name, youtube_channel_name, youtube_channel, location_address FROM users");
$users = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Manage Users</h1>
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>YouTube Channel Name</th>
                    <th>YouTube Channel URL</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['youtube_channel_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['youtube_channel']); ?></td>
                        <td><?php echo htmlspecialchars($user['location_address']); ?></td>
                        <td>
                            <a href="adminUsers.php?email=<?php echo urlencode($user['email']); ?>" class="btn btn-primary">Edit</a>
                            <form action="adminUsers.php" method="post" style="display:inline-block;">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (isset($_GET['email'])): 
            $email = $_GET['email'];
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $userProfile = $stmt->fetch();
            ?>
            <h2>Edit User</h2>
            <form action="adminUsers.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($userProfile['username']); ?>">
                </div>
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($userProfile['full_name']); ?>">
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($userProfile['location_address']); ?>">
                </div>
                <div class="mb-3">
                    <label for="youtube_channel_name" class="form-label">YouTube Channel Name</label>
                    <input type="text" class="form-control" id="youtube_channel_name" name="youtube_channel_name" value="<?php echo htmlspecialchars($userProfile['youtube_channel_name']); ?>">
                </div>
                <div class="mb-3">
                    <label for="youtube_channel_url" class="form-label">YouTube Channel URL</label>
                    <input type="text" class="form-control" id="youtube_channel_url" name="youtube_channel_url" value="<?php echo htmlspecialchars($userProfile['youtube_channel']); ?>">
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password (if updating)</label>
                    <input type="password" class="form-control" id="new_password" name="new_password">
                </div>
                <button type="submit" name="update_user" class="btn btn-primary">Update User</button>
            </form>
        <?php else: ?>
            <h2>Add User</h2>
            <form action="adminUsers.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="mb-3">
                    <label for="youtube_channel_name" class="form-label">YouTube Channel Name</label>
                    <input type="text" class="form-control" id="youtube_channel_name" name="youtube_channel_name" required>
                </div>
                <div class="mb-3">
                    <label for="youtube_channel_url" class="form-label">YouTube Channel URL</label>
                    <input type="text" class="form-control" id="youtube_channel_url" name="youtube_channel_url" required>
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" required>
                </div>
                <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
<?php include '../functions/footer.php'; ?>
