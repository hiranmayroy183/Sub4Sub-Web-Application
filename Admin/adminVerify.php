<?php
// adminVerify.php

include '../functions/header.php';
include '../functions/db.php';
include '../functions/auth.php';

if (!isAdminLoggedIn()) {
    header('Location: adminLogin.php');
    exit;
}

function notifyUser($userId, $message) {
    global $pdo;

    // Check if the user exists before inserting a notification
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    if ($stmt->fetchColumn() > 0) {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->execute([$userId, $message]);
    } else {
        throw new Exception("User ID $userId does not exist.");
    }
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        $message = 'Invalid CSRF token.';
        $messageType = 'error';
    } else {
        $action = $_POST['action'];
        $uploadId = isset($_POST['upload_id']) ? $_POST['upload_id'] : null;
        $userId = isset($_POST['user_id']) ? $_POST['user_id'] : null;

        if ($userId === null) {
            $message = 'User ID is missing.';
            $messageType = 'error';
        } else {
            try {
                switch ($action) {
                    case 'validate':
                        if ($uploadId !== null) {
                            $stmt = $pdo->prepare("UPDATE user_uploads SET validated = 1, reverify = 0 WHERE id = ?");
                            $stmt->execute([$uploadId]);
                            notifyUser($userId, 'Your input has been validated.');
                            $message = 'User input validated successfully.';
                            $messageType = 'success';
                        }
                        break;

                    case 'delete':
                        if ($uploadId !== null) {
                            $stmt = $pdo->prepare("DELETE FROM user_uploads WHERE id = ?");
                            $stmt->execute([$uploadId]);
                            notifyUser($userId, 'Your input has been deleted.');
                            $message = 'User input deleted successfully.';
                            $messageType = 'success';
                        }
                        break;

                    case 'ban':
                        $stmt = $pdo->prepare("UPDATE users SET banned = 1 WHERE id = ?");
                        $stmt->execute([$userId]);
                        notifyUser($userId, 'Your account has been terminated.');
                        $message = 'User banned successfully.';
                        $messageType = 'success';
                        break;

                    case 'review':
                        if ($uploadId !== null) {
                            $stmt = $pdo->prepare("UPDATE user_uploads SET reverify = 1 WHERE id = ?");
                            $stmt->execute([$uploadId]);
                            notifyUser($userId, 'Please verify your input again.');
                            $message = 'Review requested successfully.';
                            $messageType = 'success';
                        }
                        break;

                    default:
                        $message = 'Invalid action.';
                        $messageType = 'error';
                        break;
                }
            } catch (Exception $e) {
                $message = $e->getMessage();
                $messageType = 'error';
            }
        }
    }
}

// Fetch user uploads
$stmt = $pdo->prepare("SELECT u.*, uu.id as upload_id, uu.youtube_channel_name, uu.youtube_channel_link, uu.image_path, uu.validated, uu.reverify FROM user_uploads uu JOIN users u ON uu.user_id = u.id");
$stmt->execute();
$uploads = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Verify User Uploads</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Verify User Uploads</h1>
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <h2>Pending Verifications</h2>
        <?php foreach ($uploads as $upload): ?>
            <?php if ($upload['validated'] == 0 && $upload['reverify'] == 0 && $upload['banned'] == 0): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($upload['youtube_channel_name']); ?></h5>
                        <p class="card-text"><a href="<?php echo htmlspecialchars($upload['youtube_channel_link']); ?>" target="_blank">Channel Link</a></p>
                        <img src="<?php echo '../' . htmlspecialchars($upload['image_path']); ?>" alt="Uploaded Image" class="img-fluid mb-3">
                        <form action="adminVerify.php" method="post" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="upload_id" value="<?php echo $upload['upload_id']; ?>">
                            <input type="hidden" name="user_id" value="<?php echo $upload['id']; ?>">
                            <input type="hidden" name="action" value="validate">
                            <button type="submit" class="btn btn-success">Validate</button>
                        </form>
                        <form action="adminVerify.php" method="post" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="upload_id" value="<?php echo $upload['upload_id']; ?>">
                            <input type="hidden" name="user_id" value="<?php echo $upload['id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                        <form action="adminVerify.php" method="post" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="user_id" value="<?php echo $upload['id']; ?>">
                            <input type="hidden" name="action" value="ban">
                            <button type="submit" class="btn btn-warning">Ban</button>
                        </form>
                        <form action="adminVerify.php" method="post" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="upload_id" value="<?php echo $upload['upload_id']; ?>">
                            <input type="hidden" name="user_id" value="<?php echo $upload['id']; ?>">
                            <input type="hidden" name="action" value="review">
                            <button type="submit" class="btn btn-info">Review</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <h2>Validated Uploads</h2>
        <?php foreach ($uploads as $upload): ?>
            <?php if ($upload['validated'] == 1): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($upload['youtube_channel_name']); ?></h5>
                        <p class="card-text"><a href="<?php echo htmlspecialchars($upload['youtube_channel_link']); ?>" target="_blank">Channel Link</a></p>
                        <img src="<?php echo '../' . htmlspecialchars($upload['image_path']); ?>" alt="Uploaded Image" class="img-fluid mb-3">
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <h2>Banned Users</h2>
        <?php foreach ($uploads as $upload): ?>
            <?php if ($upload['banned'] == 1): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($upload['full_name']); ?> (<?php echo htmlspecialchars($upload['email']); ?>)</h5>
                        <p class="card-text">YouTube Channel: <?php echo htmlspecialchars($upload['youtube_channel_name']); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <h2>Reverify Records</h2>
        <?php foreach ($uploads as $upload): ?>
            <?php if ($upload['reverify'] == 1): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($upload['youtube_channel_name']); ?></h5>
                        <p class="card-text"><a href="<?php echo htmlspecialchars($upload['youtube_channel_link']); ?>" target="_blank">Channel Link</a></p>
                        <img src="<?php echo '../' . htmlspecialchars($upload['image_path']); ?>" alt="Uploaded Image" class="img-fluid mb-3">
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</body>
</html>
<?php include '../functions/footer.php'; ?>
