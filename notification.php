<?php
include 'functions/header.php';
include 'functions/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch notifications for the logged-in user
$notifications = getUserNotifications($user_id);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Your Notifications</h1>
        <?php if ($notifications): ?>
            <ul class="list-group">
                <?php foreach ($notifications as $notification): ?>
                    <li class="list-group-item">
                        <?php echo htmlspecialchars($notification['message']); ?>
                        <span class="text-muted float-end"><?php echo $notification['created_at']; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="alert alert-info">
                You have no notifications.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php include 'functions/footer.php'; ?>
