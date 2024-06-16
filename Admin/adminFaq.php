<?php
// adminAbout.php
include '../functions/header.php';
include '../functions/db.php';
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
        $content = $_POST['content'];
        $stmt = $pdo->prepare("UPDATE faq_content SET content = ? WHERE id = 1");
        if ($stmt->execute([$content])) {
            $message = 'Content updated successfully.';
            $messageType = 'success';
        } else {
            $message = 'Failed to update content.';
            $messageType = 'error';
        }
    }
}

// Fetch the current content
$stmt = $pdo->prepare("SELECT content FROM faq_content WHERE id = 1");
$stmt->execute();
$content = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit FAQ Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Edit FAQ Page</h1>
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="adminFaq.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="mb-3">
                <textarea name="content" id="content" rows="10" class="form-control"><?php echo htmlspecialchars($content); ?></textarea>
                <script>
                    CKEDITOR.replace('content');
                </script>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</body>
</html>
<?php include '../functions/footer.php'; ?>
