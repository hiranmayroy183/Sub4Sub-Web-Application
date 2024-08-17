<?php
include '../functions/header.php';
include '../functions/auth.php';

if (!isAdminLoggedIn()) {
    header('Location: adminLogin.php');
    exit;
}

generateCsrfToken();

// Fetch unread and replied purchase requests from the database
$unreadStmt = $pdo->query("SELECT pr.*, u.username FROM purchase_requests pr JOIN users u ON pr.user_id = u.id WHERE pr.replied = 0 ORDER BY pr.created_at DESC");
$unreadRequests = $unreadStmt->fetchAll();

$repliedStmt = $pdo->query("SELECT pr.*, u.username FROM purchase_requests pr JOIN users u ON pr.user_id = u.id WHERE pr.replied = 1 ORDER BY pr.created_at DESC");
$repliedRequests = $repliedStmt->fetchAll();

// Handle admin replies
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'])) {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        $message = 'Invalid CSRF token.';
        $messageType = 'error';
    } else {
        $userId = $_POST['user_id'];
        $replyMessage = htmlspecialchars($_POST['reply_message']);
        $originalMessage = htmlspecialchars($_POST['original_message']);
        $package = htmlspecialchars($_POST['package']);
        $requestId = $_POST['request_id']; // Added hidden field for request ID

        try {
            // Format the notification message with a header
            $notificationMessage = "Reply to: $package :\n\n Admin Reply:\n$replyMessage";

            // Insert the reply into the notifications table
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt->execute([$userId, $notificationMessage]);

            // Update the purchase request as replied
            $updateStmt = $pdo->prepare("UPDATE purchase_requests SET replied = 1 WHERE id = ?");
            $updateStmt->execute([$requestId]);

            $message = 'Reply sent successfully!';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error sending reply. Please try again.';
            $messageType = 'error';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Purchase Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Purchase Requests</h1>

        <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Unread Messages Section -->
        <h2>Unread Requests</h2>
        <?php if (!empty($unreadRequests)): ?>
            <ul class="list-group">
                <?php foreach ($unreadRequests as $request): ?>
                    <li class="list-group-item">
                        <h5><?php echo htmlspecialchars($request['username']); ?></h5>
                        <p><?php echo htmlspecialchars($request['message']); ?></p>
                        <span class="text-muted">Requested on <?php echo $request['created_at']; ?></span>

                        <form action="adminPayment.php" method="post" class="mt-3">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="user_id" value="<?php echo $request['user_id']; ?>">
                            <input type="hidden" name="original_message" value="<?php echo $request['message']; ?>">
                            <input type="hidden" name="package" value="<?php echo $request['package']; ?>">
                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">

                            <div class="mb-3">
                                <label for="reply_message_<?php echo $request['id']; ?>" class="form-label">Reply Message</label>
                                <textarea name="reply_message" id="reply_message_<?php echo $request['id']; ?>" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Reply</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="alert alert-info">
                No unread requests found.
            </div>
        <?php endif; ?>

        <!-- Replied Messages Section -->
        <h2>Replied Requests</h2>
        <?php if (!empty($repliedRequests)): ?>
            <ul class="list-group">
                <?php foreach ($repliedRequests as $request): ?>
                    <li class="list-group-item">
                        <h5><?php echo htmlspecialchars($request['username']); ?></h5>
                        <p><?php echo htmlspecialchars($request['message']); ?></p>
                        <span class="text-muted">Requested on <?php echo $request['created_at']; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="alert alert-info">
                No replied requests found.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php include '../functions/footer.php'; ?>
