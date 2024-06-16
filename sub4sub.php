<?php
include 'functions/header.php';
include 'functions/auth.php';

if (!isLoggedIn()) {
    echo '
    <div class=".ml-1 container mt-3">
        <div class="alert alert-warning" role="alert">
            You must be logged in to access this page. Please <a href="account.php" class="alert-link">login</a> or <a href="account.php" class="alert-link">register</a>.
        </div>
    </div>';
    include 'functions/footer.php';
    exit;
}

global $pdo;

$stmt = $pdo->query("SELECT email, youtube_channel_name, youtube_channel FROM users");
$users = $stmt->fetchAll();
?>

<div class="container mt-3">
    <h2>YouTube Channels to Subscribe</h2>
    <p class="alert alert-secondary">This is the list of all available YouTube channels that you can subscribe to, and in return, you will be subscribed to them. It's a simple calculation. Learn how to do everything from here. <a href="faq.php">Learn me</a></p>            
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Username</th>
                <th>Channel Name</th>
                <th>Channel URL</th>
                <th>Verify</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <?php
                $username = explode('@', $user['email'])[0];
                $channelName = $user['youtube_channel_name'];
                $channelURL = $user['youtube_channel'];
                ?>
                <tr>
                    <td><?= htmlspecialchars($username) ?></td>
                    <td><?= htmlspecialchars($channelName) ?></td>
                    <td><a href="<?= htmlspecialchars($channelURL) ?>" target="_blank"><button type="button" class="btn btn-primary">Open</button></a></td>
                    <td><a href="verify.php" target="_blank" ><button type="button" class="btn btn-secondary">Verify</button></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'functions/footer.php'; ?>
