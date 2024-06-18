<?php 
include 'functions/header.php'; 
include 'functions/auth.php'; 

// Check if the user is logged in and their account is terminated
if (isLoggedIn() && isAccountTerminated($_SESSION['user_id'])) {
    echo '<div class="container">';
    echo '<div class="alert alert-danger">Your account has been terminated. You can <a href="contact.php" target="_blank">contact us</a> for more details.</div>';
    echo '
    <form action="functions/logout.php" method="post">
    <button type="submit" class="btn btn-danger">Logout</button>
    </form>';
    echo '</div>';
    exit;
}

$message = '';
$messageType = '';

generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        $message = 'Invalid CSRF token.';
        $messageType = 'error';
    } else {
        if (isset($_POST['login'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            list($success, $message) = login($email, $password);
            $messageType = $success ? 'success' : 'error';
        } elseif (isset($_POST['register'])) {
            $email = $_POST['email'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $youtube_channel_name = $_POST['youtube_channel_name'];
            $youtube_channel_url = $_POST['youtube_channel_url'];
            $location_address = $_POST['location_address'];
            list($success, $message) = register($email, $password, $confirm_password, $username, $youtube_channel_name, $youtube_channel_url, $location_address);
            $messageType = $success ? 'success' : 'error';
        } elseif (isset($_POST['update_profile'])) {
            $full_name = $_POST['full_name'];
            $location_address = $_POST['location_address'];
            $youtube_channel = $_POST['youtube_channel'];
            $youtube_channel_name = $_POST['youtube_channel_name'];
            $new_password = $_POST['new_password'];

            $email = $_SESSION['email'];
            list($success, $message) = updateProfile($email, $full_name, $location_address, $youtube_channel, $youtube_channel_name);
            $messageType = $success ? 'success' : 'error';

            if (!empty($new_password)) {
                list($success, $passwordMessage) = changePassword($email, $new_password);
                $message .= " " . $passwordMessage;
            }

            if ($success) {
                $userProfile = getUserProfile($email);
            }
        } elseif (isset($_POST['verify_subscription'])) {
            $subscription_urls = array_filter(array_map('trim', explode("\n", $_POST['subscription_verification_urls'])));

            // Validate URLs
            foreach ($subscription_urls as $url) {
                if (strpos($url, 'https://roydigitalnexus.com/') === false) {
                    $message = 'All images must be hosted on https://roydigitalnexus.com/';
                    $messageType = 'error';
                    break;
                }
            }

            if ($messageType !== 'error') {
                // Fetch existing URLs
                $stmt = $pdo->prepare("SELECT subscription_urls FROM users WHERE email = ?");
                $stmt->execute([$_SESSION['email']]);
                $existing_urls = json_decode($stmt->fetchColumn() ?: '[]', true);

                // Check total URLs
                if (count($existing_urls) + count($subscription_urls) > 20) {
                    $message = 'You can only store up to 20 URLs.';
                    $messageType = 'error';
                } else {
                    // Merge and remove duplicates
                    $all_urls = array_unique(array_merge($existing_urls, $subscription_urls));
                    $stmt = $pdo->prepare("UPDATE users SET subscription_urls = ? WHERE email = ?");
                    $stmt->execute([json_encode($all_urls), $_SESSION['email']]);
                    $message = 'Subscription URLs successfully saved.';
                    $messageType = 'success';
                }
            }
        }

        if ($messageType === 'success' && strpos($message, 'Login successful') !== false) {
            header("Location: account.php");
            exit;
        }
    }
}

function getUsernameFromEmail($email) {
    return strstr($email, '@', true);
}

$userProfile = isLoggedIn() ? getUserProfile($_SESSION['email']) : null;

// Fetch user notifications
$notifications = [];
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT message FROM notifications WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $notifications = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>
<div class="container">
    <?php if (isLoggedIn()): ?>
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="alert alert-success">
                    <h4 class="alert-heading">Hello, <?php echo getUsernameFromEmail($_SESSION['email']); ?>!</h4>
                    <p>Welcome back to your account.</p>
                    <form action="functions/logout.php" method="post">
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </div>
            </div>

            <div class="alert alert-warning" role="alert">
                <p>It is necessary to complete your account information to reach other people and get subscribed.</p>
                <p>Please fill out the form below.</p>
            </div>

            <div class="col-md-12">
                

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Update Profile</h3>
                            <form id="updateProfileForm" action="" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <div class="mb-3">
                                    <label for="fullName" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="fullName" name="full_name" value="<?php echo htmlspecialchars($userProfile['full_name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="locationAddress" class="form-label">Location Address</label>
                                    <input type="text" class="form-control" id="locationAddress" name="location_address" value="<?php echo htmlspecialchars($userProfile['location_address']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="youtubeChannel" class="form-label">YouTube Channel URL</label>
                                    <input type="url" class="form-control" id="youtubeChannel" name="youtube_channel"
                                           value="<?php echo htmlspecialchars($userProfile['youtube_channel']); ?>"
                                           <?php if ($userProfile['youtube_channel_changed']): ?> disabled title="You can only change the YouTube channel URL once." <?php endif; ?>
                                           required>
                                    <?php if ($userProfile['youtube_channel_changed']): ?>
                                        <small id="youtubeChannelTooltip" class="form-text text-muted">You can only change the YouTube channel URL once.</small>
                                    <?php endif; ?>
                                </div>
                                <div class="mb-3">
                                    <label for="youtubeChannelName" class="form-label">YouTube Channel Name</label>
                                    <input type="text" class="form-control" id="youtubeChannelName" name="youtube_channel_name" value="<?php echo htmlspecialchars($userProfile['youtube_channel_name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="currentUsername" class="form-label">Username (Read-only)</label>
                                    <input type="text" class="form-control" id="currentUsername" value="<?php echo getUsernameFromEmail($_SESSION['email']); ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="currentEmail" class="form-label">Email (Read-only)</label>
                                    <input type="email" class="form-control" id="currentEmail" value="<?php echo $_SESSION['email']; ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="newPassword" class="form-label">New Password (if updating)</label>
                                    <input type="password" class="form-control" id="newPassword" name="new_password">
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <h4 class="text-center">All the channels you have subscribed to!</h4>
                        <div class="card-body">
                            <form id="subscriptionVerificationForm" action="" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <div class="mb-3">
                                    <label for="subscriptionVerificationUrls" class="form-label">Verify Your Subscriptions:</label>
                                    <textarea readonly class="form-control" id="subscriptionVerificationUrls" name="subscription_verification_urls" rows="4" placeholder="The channels you subscribed to were found to be genuine."><?php echo htmlspecialchars(isset($userProfile['subscription_urls']) ? implode("\n", json_decode($userProfile['subscription_urls'], true)) : ''); ?></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                    
            <?php if ($notifications): ?>
                <div class="alert alert-info">
    <h4 class="alert-heading">Notifications</h4>
    <ul>
        <?php foreach ($notifications as $notification): ?>
            <li><?php echo htmlspecialchars($notification); ?></li>
        <?php endforeach; ?>
    </ul>
    <br>
    <p>
        <a href="notification.php" target="_blank" class="btn btn-primary btn-sm">View all</a>
    </p>
</div>

            <?php endif; ?>

                </div>
                
 
            </div>


        <?php else: ?>
            <div class="row mt-5">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Login</h3>
                            <?php if ($message && isset($_POST['login'])): ?>
                                <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
                                    <?php echo $message; ?>
                                </div>
                            <?php endif; ?>
                            <form id="loginForm" action="" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <div class="mb-3">
                                    <label for="loginEmail" class="form-label">Email address</label>
                                    <input type="email" class="form-control" id="loginEmail" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="loginPassword" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="loginPassword" name="password" required>
                                </div>
                                <div class="mb-3">
                                    <a href="forget.php" class="link-primary">Forgot Password or Username?</a>
                                </div>
                                <button type="submit" name="login" class="btn btn-primary">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">Register</h3>
                            <?php if ($message && isset($_POST['register'])): ?>
                                <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
                                    <?php echo $message; ?>
                                </div>
                            <?php endif; ?>
                            <form id="registerForm" action="" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <div class="mb-3">
                                    <label for="registerEmail" class="form-label">Email address</label>
                                    <input type="email" class="form-control" id="registerEmail" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="registerUsername" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="registerUsername" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="registerPassword" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="registerPassword" name="password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="registerConfirmPassword" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="registerConfirmPassword" name="confirm_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="registerYoutubeChannelName" class="form-label">YouTube Channel Name</label>
                                    <input type="text" class="form-control" id="registerYoutubeChannelName" name="youtube_channel_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="registerYoutubeChannelUrl" class="form-label">YouTube Channel URL</label>
                                    <input type="url" class="form-control" id="registerYoutubeChannelUrl" name="youtube_channel_url" required>
                                </div>
                                <div class="mb-3">
                                    <label for="registerLocation" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="registerLocation" name="location_address" required>
                                </div>
                                <button type="submit" name="register" class="btn btn-primary">Register</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Numbering and limit function for the subscription URLs textarea
        const subscriptionTextarea = document.getElementById('subscriptionVerificationUrls');
        subscriptionTextarea.addEventListener('input', function() {
            let lines = subscriptionTextarea.value.split('\n');
            if (lines.length > 20) {
                subscriptionTextarea.value = lines.slice(0, 20).join('\n');
                subscriptionTextarea.setAttribute('readonly', 'readonly');
            } else {
                for (let i = 0; i < lines.length; i++) {
                    lines[i] = `${i + 1}. ${lines[i].replace(/^\d+\.\s*/, '')}`;
                }
                subscriptionTextarea.value = lines.join('\n');
            }
        });

        // Form validation function
        function validateForm(form) {
            const email = form.querySelector('[name="email"]');
            const password = form.querySelector('[name="password"]');
            const confirmPassword = form.querySelector('[name="confirm_password"]');
            const fullName = form.querySelector('[name="full_name"]');
            const locationAddress = form.querySelector('[name="location_address"]');
            const youtubeChannel = form.querySelector('[name="youtube_channel"]');
            const youtubeChannelName = form.querySelector('[name="youtube_channel_name"]');

            if (email && !email.value.includes('@')) {
                alert('Please enter a valid email address.');
                return false;
            }

            if (password && password.value.length < 6) {
                alert('Password must be at least 6 characters long.');
                return false;
            }

            if (confirmPassword && password.value !== confirmPassword.value) {
                alert('Passwords do not match.');
                return false;
            }

            if (fullName && fullName.value.trim() === '') {
                alert('Full Name is required.');
                return false;
            }

            if (locationAddress && locationAddress.value.trim() === '') {
                alert('Location Address is required.');
                return false;
            }

            if (youtubeChannel && !youtubeChannel.value.startsWith('http')) {
                alert('Please enter a valid URL for the YouTube Channel.');
                return false;
            }

            return true;
        }

        // Attach validation to forms
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            if (!validateForm(this)) event.preventDefault();
        });

        document.getElementById('registerForm').addEventListener('submit', function(event) {
            if (!validateForm(this)) event.preventDefault();
        });

        document.getElementById('updateProfileForm').addEventListener('submit', function(event) {
            if (!validateForm(this)) event.preventDefault();
        });

        // Custom tooltip for YouTube channel field
        const youtubeChannel = document.getElementById('youtubeChannel');
        const youtubeChannelTooltip = document.getElementById('youtubeChannelTooltip');

        if (youtubeChannel && youtubeChannelTooltip) {
            youtubeChannel.addEventListener('mouseover', function() {
                youtubeChannelTooltip.style.display = 'block';
            });
            youtubeChannel.addEventListener('mouseout', function() {
                youtubeChannelTooltip.style.display = 'none';
            });
        }
    });
    </script>

<?php include 'functions/footer.php'; ?>

<?php
// Function to check if the account is terminated
// function isAccountTerminated($user_id) {
//     global $pdo;
//     $stmt = $pdo->prepare("SELECT is_terminated FROM users WHERE id = ?");
//     $stmt->execute([$user_id]);
//     return $stmt->fetchColumn() == 1;
// }
?>
