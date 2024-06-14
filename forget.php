<?php include 'functions/header.php'; ?>
<?php include 'functions/auth.php'; ?>

<?php
$message = '';
$messageType = '';
$mathQuestion = generateMathQuestion();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $userAnswer = intval($_POST['captcha']);
    $correctAnswer = intval($_POST['correct_answer']);

    if ($userAnswer === $correctAnswer) {
        list($success, $message) = resetPassword($email);
        $messageType = $success ? 'success' : 'error';
    } else {
        $message = 'Incorrect CAPTCHA answer. Please try again.';
        $messageType = 'error';
    }
}

function generateMathQuestion() {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $operatorIndex = rand(0, 2);
    $operators = ['+', '-', '/', '*'];
    $operator = $operators[$operatorIndex];

    switch ($operator) {
        case '+':
            $question = "$num1 + $num2";
            $answer = $num1 + $num2;
            break;
        case '-':
            $question = "$num1 - $num2";
            $answer = $num1 - $num2;
            break;
        case '*':
            $question = "$num1 * $num2";
            $answer = $num1 * $num2;
            break;
        case '/':
            // Ensure $num1 is divisible by $num2 for whole number results
            $num1 = $num1 * $num2;
            $question = "$num1 / $num2";
            $answer = $num1 / $num2;
            break;
    }

    return ['question' => $question, 'answer' => $answer];
}
?>

<div class="container mt-5">
    <h3 class="mb-4">Reset Your Password</h3>
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form action="forget.php" method="post">
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="captcha" class="form-label">Solve: <?php echo $mathQuestion['question']; ?></label>
            <input type="number" class="form-control" id="captcha" name="captcha" required>
            <input type="hidden" name="correct_answer" value="<?php echo $mathQuestion['answer']; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Reset Password</button>
    </form>
</div>

<?php include 'functions/footer.php'; ?>
