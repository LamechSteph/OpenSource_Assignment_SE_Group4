<?php
/**
 * Update Project Status
 *
 * FYPTS - Final Year Project Tracking System
 * Updates project status and progress percentage.
 */

require_once __DIR__ . '/../config/db.php';
requireLogin();

$conn = getConnection();
$errors = [];

$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare('SELECT * FROM projects WHERE id = ?');
$stmt->bind_param('i', $project_id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$project) {
    $_SESSION['error'] = 'Project not found.';
    header('Location: view_projects.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'] ?? '';
    $new_progress = (int)($_POST['progress_percentage'] ?? $project['progress_percentage']);

    $valid_statuses = ['Proposal Submitted', 'Approved', 'Design Phase', 'Development Phase', 'Testing Phase', 'Completed'];
    if (!in_array($new_status, $valid_statuses)) {
        $errors[] = 'Invalid status selected.';
    }

    $new_progress = max(0, min(100, $new_progress));

    if (empty($errors)) {
        $update = $conn->prepare('UPDATE projects SET status = ?, progress_percentage = ? WHERE id = ?');
        $update->bind_param('sii', $new_status, $new_progress, $project_id);

        if ($update->execute()) {
            $_SESSION['success'] = 'Project status updated successfully.';
            header('Location: view_projects.php?id=' . $project_id);
            exit();
        } else {
            $errors[] = 'Failed to update status.';
        }
        $update->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status - FYPTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="container py-4">
        <div class="mb-4">
            <a href="view_projects.php?id=<?= $project_id ?>" class="text-decoration-none text-muted small d-inline-block mb-2">
                <i class="bi bi-arrow-left"></i> Back to project
            </a>
            <h1 class="h3 mb-1">Update Status</h1>
            <p class="text-muted mb-0"><?= htmlspecialchars($project['project_title']) ?></p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2">
                <?php foreach ($errors as $error): ?>
                    <div><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Current Status</h5>
                        <div class="mb-4">
                            <span class="badge bg-<?php
                                $s = $project['status'];
                                if ($s === 'Completed') echo 'success';
                                elseif ($s === 'Approved') echo 'primary';
                                elseif ($s === 'Proposal Submitted') echo 'secondary';
                                elseif ($s === 'Testing Phase') echo 'info';
                                elseif ($s === 'Development Phase') echo 'warning';
                                else echo 'dark';
                            ?> fs-6"><?= htmlspecialchars($project['status']) ?></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Progress</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="progress flex-grow-1" style="height:10px;">
                                    <div class="progress-bar" style="width:<?= $project['progress_percentage'] ?>%"></div>
                                </div>
                                <strong><?= $project['progress_percentage'] ?>%</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Update</h5>
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label class="form-label">New Status</label>
                                <select class="form-select form-select-lg" name="status">
                                    <option value="Proposal Submitted" <?= $project['status'] === 'Proposal Submitted' ? 'selected' : '' ?>>Proposal Submitted</option>
                                    <option value="Approved" <?= $project['status'] === 'Approved' ? 'selected' : '' ?>>Approved</option>
                                    <option value="Design Phase" <?= $project['status'] === 'Design Phase' ? 'selected' : '' ?>>Design Phase</option>
                                    <option value="Development Phase" <?= $project['status'] === 'Development Phase' ? 'selected' : '' ?>>Development Phase</option>
                                    <option value="Testing Phase" <?= $project['status'] === 'Testing Phase' ? 'selected' : '' ?>>Testing Phase</option>
                                    <option value="Completed" <?= $project['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Progress Percentage</label>
                                <div class="input-group">
                                    <input type="range" class="form-range me-3" name="progress_percentage"
                                           min="0" max="100" value="<?= $project['progress_percentage'] ?>"
                                           id="progressSlider"
                                           oninput="document.getElementById('progressVal').textContent = this.value + '%'">
                                    <span class="fw-bold" id="progressVal"><?= $project['progress_percentage'] ?>%</span>
                                </div>
                                <div class="d-flex justify-content-between small text-muted mt-1">
                                    <span>0%</span><span>25%</span><span>50%</span><span>75%</span><span>100%</span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-check-lg"></i> Update Status
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Flow Guide -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body p-4">
                <h5 class="card-title mb-3">Project Lifecycle</h5>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-secondary fs-6">Proposal Submitted</span>
                    <i class="bi bi-arrow-right text-muted"></i>
                    <span class="badge bg-primary fs-6">Approved</span>
                    <i class="bi bi-arrow-right text-muted"></i>
                    <span class="badge bg-dark fs-6">Design Phase</span>
                    <i class="bi bi-arrow-right text-muted"></i>
                    <span class="badge bg-warning fs-6">Development Phase</span>
                    <i class="bi bi-arrow-right text-muted"></i>
                    <span class="badge bg-info fs-6">Testing Phase</span>
                    <i class="bi bi-arrow-right text-muted"></i>
                    <span class="badge bg-success fs-6">Completed</span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
