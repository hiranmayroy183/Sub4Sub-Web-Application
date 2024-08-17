<?php
include 'functions/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Get the selected package from the URL query parameter
$package = isset($_GET['package']) ? htmlspecialchars($_GET['package']) : '';

$packageMessages = [
    'starter' => "I want to purchase the Starter package.",
    'personal' => "I want to purchase the Personal package.",
    'ultimate' => "I want to purchase the Ultimate package.",
];

$message = isset($packageMessages[$package]) ? $packageMessages[$package] : "No package selected.";

// Generate CSRF token for security
generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        $status = 'error';
        $feedbackMessage = 'Invalid CSRF token.';
    } else {
        try {
            // Insert the purchase request into the database
            $stmt = $pdo->prepare("INSERT INTO purchase_requests (user_id, package, message) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $package, $message]);

            $status = 'success';
            $feedbackMessage = 'Your request has been sent successfully!';
        } catch (Exception $e) {
            $status = 'error';
            $feedbackMessage = 'There was an error processing your request. Please try again later.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Package</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .message-box {
            margin-top: 50px;
            text-align: center;
        }
    </style>
    <?php if (isset($status) && $status === 'success'): ?>
    <script>
        // Countdown and redirect script
        let countdown = 5;
        function updateCountdown() {
            if (countdown > 0) {
                document.getElementById('countdown').innerText = countdown;
                countdown--;
            } else {
                window.location.href = 'account.php';
            }
        }

        setInterval(updateCountdown, 1000);
    </script>
    <?php endif; ?>
</head>
<body>
    <div class="container mt-5">
        <?php if (!isset($status)): ?>
            <h1>Purchase Package</h1>
            <form action="purchase.php?package=<?php echo $package; ?>" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="mb-3">
                    <label for="package" class="form-label">Selected Package</label>
                    <input type="text" class="form-control" id="package" name="package" value="<?php echo ucfirst($package); ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="4" readonly><?php echo $message; ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit Request</button>
            </form>
        <?php else: ?>
            <div class="message-box">
                <?php if ($status === 'success'): ?>
                    <div class="alert alert-success">
                        <?php echo $feedbackMessage; ?>
                    </div>
                    <p>Redirecting to your account page in <span id="countdown">5</span> seconds...</p>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <?php echo $feedbackMessage; ?>
                    </div>
                    <p><a href="purchase.php?package=<?php echo $package; ?>">Try again</a></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
