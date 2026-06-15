<?php

/**
 * Add Project
 *
 * FYPTS - Final Year Project Tracking System
 * Form to create a new final year project record.
 */

require_once __DIR__ . '/../config/db.php';
requireLogin();

$conn = getConnection();
$errors = [];
$form_data = [
    'project_title' => '',
    'student_name' => '',
    'supervisor_name' => '',
    'description' => '',
    'start_date' => '',
    'expected_completion' => '',
    'status' => 'Proposal Submitted',
    'progress_percentage' => 0
];

// Fetch supervisors for dropdown
$supervisors = $conn->query("SELECT fullname FROM users WHERE role = 'Supervisor' ORDER BY fullname");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_data['project_title'] = trim($_POST['project_title'] ?? '');
    $form_data['student_name'] = trim($_POST['student_name'] ?? '');
    $form_data['supervisor_name'] = trim($_POST['supervisor_name'] ?? '');
    $form_data['description'] = trim($_POST['description'] ?? '');
    $form_data['start_date'] = $_POST['start_date'] ?? '';
    $form_data['expected_completion'] = $_POST['expected_completion'] ?? '';
    $form_data['status'] = $_POST['status'] ?? 'Proposal Submitted';
    $form_data['progress_percentage'] = (int)($_POST['progress_percentage'] ?? 0);

    // Validation
    if (empty($form_data['project_title'])) $errors[] = 'Project title is required.';
    if (empty($form_data['student_name'])) $errors[] = 'Student name is required.';
    if (empty($form_data['supervisor_name'])) $errors[] = 'Supervisor name is required.';

    $valid_statuses = ['Proposal Submitted', 'Approved', 'Design Phase', 'Development Phase', 'Testing Phase', 'Completed'];
    if (!in_array($form_data['status'], $valid_statuses)) $form_data['status'] = 'Proposal Submitted';

    $form_data['progress_percentage'] = max(0, min(100, $form_data['progress_percentage']));

    if (empty($errors)) {
        $stmt = $conn->prepare('INSERT INTO projects (project_title, student_name, supervisor_name, description, start_date, expected_completion, status, progress_percentage) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $start_date = $form_data['start_date'] ?: null;
        $expected_completion = $form_data['expected_completion'] ?: null;
        $stmt->bind_param(
            'sssssssi',
            $form_data['project_title'],
            $form_data['student_name'],
            $form_data['supervisor_name'],
            $form_data['description'],
            $start_date,
            $expected_completion,
            $form_data['status'],
            $form_data['progress_percentage']
        );

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Project created successfully.';
            header('Location: view_projects.php');
            exit();
        } else {
            $errors[] = 'Failed to create project. Please try again.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Project - FYPTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="container py-4">
        <div class="mb-4">
            <h1 class="h3 mb-1">Add New Project</h1>
            <p class="text-muted mb-0">Create a new final year project record</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2">
                <?php foreach ($errors as $error): ?>
                    <div><i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Project Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="project_title"
                                value="<?= htmlspecialchars($form_data['project_title']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Student Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="student_name"
                                value="<?= htmlspecialchars($form_data['student_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supervisor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="supervisor_name" list="supervisorList"
                                value="<?= htmlspecialchars($form_data['supervisor_name']) ?>" required>
                            <datalist id="supervisorList">
                                <?php if ($supervisors && $supervisors->num_rows > 0): ?>
                                    <?php while ($s = $supervisors->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($s['fullname']) ?>">
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                            </datalist>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="4"><?= htmlspecialchars($form_data['description']) ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date"
                                value="<?= htmlspecialchars($form_data['start_date']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Expected Completion</label>
                            <input type="date" class="form-control" name="expected_completion"
                                value="<?= htmlspecialchars($form_data['expected_completion']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="Proposal Submitted" <?= $form_data['status'] === 'Proposal Submitted' ? 'selected' : '' ?>>Proposal Submitted</option>
                                <option value="Approved" <?= $form_data['status'] === 'Approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="Design Phase" <?= $form_data['status'] === 'Design Phase' ? 'selected' : '' ?>>Design Phase</option>
                                <option value="Development Phase" <?= $form_data['status'] === 'Development Phase' ? 'selected' : '' ?>>Development Phase</option>
                                <option value="Testing Phase" <?= $form_data['status'] === 'Testing Phase' ? 'selected' : '' ?>>Testing Phase</option>
                                <option value="Completed" <?= $form_data['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Progress (%)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="progress_percentage"
                                    min="0" max="100" value="<?= $form_data['progress_percentage'] ?>">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-lg"></i> Create Project
                            </button>
                            <a href="view_projects.php" class="btn btn-outline-secondary px-4 ms-2">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>

</html>