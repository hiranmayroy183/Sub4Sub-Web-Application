<?php
include 'functions/db.php';
include 'functions/auth.php';
include 'functions/header.php';

if (!isLoggedIn()) {
    echo '
    <div class="container mt-3">
        <div class="alert alert-warning" role="alert">
            You must be logged in to access this page. Please <a href="account.php" class="alert-link">login</a> or <a href="account.php" class="alert-link">register</a>.
        </div>
    </div>';
    include 'functions/footer.php';
    exit;
}

if (!isset($_SESSION['redirected_from_sub4sub']) || !$_SESSION['redirected_from_sub4sub']) {
    echo '
    <div class="container mt-3">
        <div class="alert alert-danger" role="alert">
            You are not authorized to access this page directly. Please go to the <a href="sub4sub.php" class="alert-link">sub4sub page</a> first.
        </div>
    </div>';
    include 'functions/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $youtube_channel_name = isset($_POST['youtube_channel_name']) ? $_POST['youtube_channel_name'] : '';
    $youtube_channel_link = isset($_POST['youtube_channel_link']) ? $_POST['youtube_channel_link'] : '';
    $upload_dir = 'upload/';
    $upload_file = $upload_dir . basename($_FILES['uploaded_image']['name']);
    
    if (move_uploaded_file($_FILES['uploaded_image']['tmp_name'], $upload_file)) {
        $stmt = $pdo->prepare("INSERT INTO user_uploads (user_id, youtube_channel_name, youtube_channel_link, image_path) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $youtube_channel_name, $youtube_channel_link, $upload_file]);
        echo '
        <div class="container mt-3">
            <div class="alert alert-success" role="alert">
                File uploaded successfully.
            </div>
        </div>';
    } else {
        echo '
        <div class="container mt-3">
            <div class="alert alert-danger" role="alert">
                There was an error uploading the file.
            </div>
        </div>';
    }
}

?>

<div class="container mt-3">
    <h1 class="text-center">Upload Your YouTube Channel Verification</h1>
    <form action="verify.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="youtube_channel_name" class="form-label">YouTube Channel Name</label>
            <input type="text" class="form-control" id="youtube_channel_name" name="youtube_channel_name" required>
        </div>
        <div class="mb-3">
            <label for="youtube_channel_link" class="form-label">YouTube Channel Link</label>
            <input type="url" class="form-control" id="youtube_channel_link" name="youtube_channel_link" required>
        </div>
        <div class="mb-3">
            <label for="uploaded_image" class="form-label">Upload Image</label>
            <input type="file" class="form-control" id="uploaded_image" name="uploaded_image" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <?php if (isset($upload_file)): ?>
        <div class="mt-3">
            <h3>Image Preview:</h3>
            <img src="<?= htmlspecialchars($upload_file) ?>" alt="Uploaded Image" class="img-fluid">
        </div>
    <?php endif; ?>
</div>
<br><br><br>
<?php include 'functions/footer.php'; ?>
