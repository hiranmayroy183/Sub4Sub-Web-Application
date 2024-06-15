<?php
include 'functions/header.php';
include 'functions/db.php';

// Fetch the current content
$stmt = $pdo->prepare("SELECT content FROM contact_content WHERE id = 1");
$stmt->execute();
$content = $stmt->fetchColumn();
?>
<div class="container mt-5">
    <?php echo $content; ?>
</div>
<?php include 'functions/footer.php'; ?>
