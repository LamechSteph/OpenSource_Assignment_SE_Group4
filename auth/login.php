<?php

/**
 * User Login
 *
 * FYPTS - Final Year Project Tracking System
 * Authenticates users and starts a session.
 */

require_once __DIR__ . '/../config/db.php';
startSession();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../dashboard.php');
    exit();
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email)) {
        $errors[] = 'Email address is required.';
    }

    if (empty($password)) {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        $conn = getConnection();
        $stmt = $conn->prepare('SELECT id, fullname, email, password, role FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['fullname'] = $row['fullname'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = $row['role'];

                $_SESSION['success'] = 'Welcome back, ' . htmlspecialchars($row['fullname']) . '!';
                header('Location: ../dashboard.php');
                exit();
            } else {
                $errors[] = 'Invalid email or password.';
            }
        } else {
            $errors[] = 'Invalid email or password.';
        }
        $stmt->close();
    }

    if (!empty($errors)) {
        $_SESSION['error'] = implode(' ', $errors);
        header('Location: login.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FYPTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-5 col-lg-4">
                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold text-primary">FYPTS</h1>
                    <p class="text-muted">Final Year Project Tracking System</p>
                </div>
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h2 class="h5 text-center mb-4">Sign In</h2>

                        <?php
                        $error_msg = $_SESSION['error'] ?? '';
                        $success_msg = $_SESSION['success'] ?? '';
                        unset($_SESSION['error'], $_SESSION['success']);

                        if ($error_msg): ?>
                            <div class="alert alert-danger py-2"><?= htmlspecialchars($error_msg) ?></div>
                        <?php endif; ?>
                        <?php if ($success_msg): ?>
                            <div class="alert alert-success py-2"><?= htmlspecialchars($success_msg) ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= htmlspecialchars($email) ?>" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Sign In</button>
                        </form>

                        <div class="text-center mt-3">
                            <p class="mb-0 small">Don't have an account? <a href="register.php">Register here</a></p>
                        </div>
                        <div class="text-center mt-2">
                            <a href="../index.php" class="small text-decoration-none">&larr; Back to Home</a>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">Demo: admin@fypts.com / password</small>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>