<?php

/**
 * User Registration
 *
 * FYPTS - Final Year Project Tracking System
 * Allows new users to create an account.
 */

require_once __DIR__ . '/../config/db.php';
startSession();

$errors = [];
$fullname = $email = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'Student';

    // Validation
    if (empty($fullname)) {
        $errors[] = 'Full name is required.';
    }

    if (empty($email)) {
        $errors[] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    $valid_roles = ['Admin', 'Supervisor', 'Student'];
    if (!in_array($role, $valid_roles)) {
        $role = 'Student';
    }

    // Check if email already exists
    if (empty($errors)) {
        $conn = getConnection();
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = 'An account with this email already exists.';
        }
        $stmt->close();
    }

    // Insert user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $conn = getConnection();
        $stmt = $conn->prepare('INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $fullname, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            $success = true;
            $_SESSION['success'] = 'Registration successful! You can now login.';
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
        $stmt->close();
    }

    if (!empty($errors)) {
        $_SESSION['error'] = implode(' ', $errors);
    }

    if ($success) {
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
    <title>Register - FYPTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold text-primary">FYPTS</h1>
                    <p class="text-muted">Final Year Project Tracking System</p>
                </div>
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h2 class="h5 text-center mb-4">Create New Account</h2>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger py-2">
                                <?php foreach ($errors as $error): ?>
                                    <div><?= htmlspecialchars($error) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="fullname" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="fullname" name="fullname"
                                    value="<?= htmlspecialchars($fullname) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?= htmlspecialchars($email) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    minlength="6" required>
                                <div class="form-text">Minimum 6 characters.</div>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password"
                                    name="confirm_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="Student" selected>Student</option>
                                    <option value="Supervisor">Supervisor</option>
                                    <option value="Admin">Admin</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>

                        <div class="text-center mt-3">
                            <p class="mb-0 small">Already have an account? <a href="login.php">Login here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>