<?php include 'functions/header.php'; ?>
<?php include 'functions/auth.php'; ?>

<?php
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
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            list($success, $message) = register($email, $password, $confirm_password);
            $messageType = $success ? 'success' : 'error';
        } elseif (isset($_POST['update_profile'])) {
            $full_name = $_POST['full_name'];
            $location_address = $_POST['location_address'];
            $youtube_channel = $_POST['youtube_channel'];
            $new_password = $_POST['new_password'];

            $email = $_SESSION['email'];
            list($success, $message) = updateProfile($email, $full_name, $location_address, $youtube_channel);
            $messageType = $success ? 'success' : 'error';

            if (!empty($new_password)) {
                list($success, $passwordMessage) = changePassword($email, $new_password);
                $message .= " " . $passwordMessage;
            }

            if ($success) {
                $userProfile = getUserProfile($email);
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
        </div>

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
<!-- YouTube Channel URL Section -->
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
                                <label for="registerPassword" class="form-label">Password</label>
                                <input type="password" class="form-control" id="registerPassword" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="registerConfirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="registerConfirmPassword" name="confirm_password" required>
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
    const youtubeChannelName = document.getElementById('youtubeChannelName');

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
