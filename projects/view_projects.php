<?php
/**
 * View Projects
 *
 * FYPTS - Final Year Project Tracking System
 * Lists all projects with status and progress.
 * Shows a single project detail when ?id=N is provided.
 */

require_once __DIR__ . '/../config/db.php';
requireLogin();

$conn = getConnection();

// Single project view
if (isset($_GET['id'])) {
    $project_id = (int)$_GET['id'];
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
    $single_view = true;
} else {
    $single_view = false;
    $search = trim($_GET['search'] ?? '');
    if ($search) {
        $like = '%' . $search . '%';
        $stmt = $conn->prepare('SELECT * FROM projects WHERE project_title LIKE ? ORDER BY created_at DESC');
        $stmt->bind_param('s', $like);
    } else {
        $stmt = $conn->prepare('SELECT * FROM projects ORDER BY created_at DESC');
    }
    $stmt->execute();
    $projects = $stmt->get_result();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $single_view ? 'Project Details' : 'View Projects' ?> - FYPTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="container py-4">
        <?php
        $error_msg = $_SESSION['error'] ?? '';
        $success_msg = $_SESSION['success'] ?? '';
        unset($_SESSION['error'], $_SESSION['success']);
        if ($error_msg): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php if ($success_msg): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success_msg) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>

        <?php if ($single_view): ?>
            <!-- Single Project Detail View -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <a href="view_projects.php" class="text-decoration-none text-muted small mb-2 d-inline-block">
                        <i class="bi bi-arrow-left"></i> Back to all projects
                    </a>
                    <h1 class="h3 mb-1"><?= htmlspecialchars($project['project_title']) ?></h1>
                </div>
                <div class="d-flex gap-2">
                    <a href="edit_project.php?id=<?= $project['id'] ?>" class="btn btn-outline-primary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="update_status.php?id=<?= $project['id'] ?>" class="btn btn-outline-info">
                        <i class="bi bi-arrow-repeat"></i> Update Status
                    </a>
                    <a href="delete_project.php?id=<?= $project['id'] ?>"
                       class="btn btn-outline-danger"
                       onclick="return confirm('Delete this project permanently?')">
                        <i class="bi bi-trash"></i> Delete
                    </a>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">Description</h5>
                            <p class="text-muted"><?= nl2br(htmlspecialchars($project['description'] ?? 'No description provided.')) ?></p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">Timeline</h5>
                            <div class="row g-3">
                                <div class="col-6">
                                    <small class="text-muted d-block">Start Date</small>
                                    <strong><?= $project['start_date'] ? date('F d, Y', strtotime($project['start_date'])) : 'Not set' ?></strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Expected Completion</small>
                                    <strong><?= $project['expected_completion'] ? date('F d, Y', strtotime($project['expected_completion'])) : 'Not set' ?></strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Created</small>
                                    <strong><?= date('F d, Y', strtotime($project['created_at'])) ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">Status</h5>
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
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">Progress</h5>
                            <div class="text-center mb-3">
                                <span class="display-4 fw-bold text-primary"><?= $project['progress_percentage'] ?>%</span>
                            </div>
                            <div class="progress" style="height:12px;">
                                <div class="progress-bar bg-<?= $project['progress_percentage'] >= 100 ? 'success' : 'primary' ?>"
                                     style="width:<?= $project['progress_percentage'] ?>%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-3">People</h5>
                            <div class="mb-3">
                                <small class="text-muted d-block">Student</small>
                                <strong><?= htmlspecialchars($project['student_name']) ?></strong>
                            </div>
                            <div>
                                <small class="text-muted d-block">Supervisor</small>
                                <strong><?= htmlspecialchars($project['supervisor_name']) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Project List View -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">All Projects</h1>
                    <p class="text-muted mb-0">Browse and manage final year projects</p>
                </div>
                <a href="add_project.php" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Add Project
                </a>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <form method="GET" class="row g-2 align-items-center">
                        <div class="col-md-6 col-lg-4">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search"
                                       placeholder="Search by title..." value="<?= htmlspecialchars($search) ?>">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="view_projects.php" class="btn btn-outline-secondary <?= $search ? '' : 'd-none' ?>">Clear</a>
                        </div>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Project Title</th>
                                    <th>Student</th>
                                    <th>Supervisor</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($projects->num_rows > 0): ?>
                                    <?php $i = 1; while ($row = $projects->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td class="fw-medium">
                                                <a href="view_projects.php?id=<?= $row['id'] ?>"
                                                   class="text-decoration-none">
                                                    <?= htmlspecialchars($row['project_title']) ?>
                                                </a>
                                            </td>
                                            <td><?= htmlspecialchars($row['student_name']) ?></td>
                                            <td><?= htmlspecialchars($row['supervisor_name']) ?></td>
                                            <td>
                                                <span class="badge bg-<?php
                                                    $s = $row['status'];
                                                    if ($s === 'Completed') echo 'success';
                                                    elseif ($s === 'Approved') echo 'primary';
                                                    elseif ($s === 'Proposal Submitted') echo 'secondary';
                                                    elseif ($s === 'Testing Phase') echo 'info';
                                                    elseif ($s === 'Development Phase') echo 'warning';
                                                    else echo 'dark';
                                                ?>"><?= htmlspecialchars($s) ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1" style="height:8px;">
                                                        <div class="progress-bar bg-<?= $row['progress_percentage'] >= 100 ? 'success' : 'primary' ?>"
                                                             style="width:<?= $row['progress_percentage'] ?>%"></div>
                                                    </div>
                                                    <small class="text-muted"><?= $row['progress_percentage'] ?>%</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="view_projects.php?id=<?= $row['id'] ?>"
                                                       class="btn btn-outline-primary" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="edit_project.php?id=<?= $row['id'] ?>"
                                                       class="btn btn-outline-secondary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="update_status.php?id=<?= $row['id'] ?>"
                                                       class="btn btn-outline-info" title="Update Status">
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </a>
                                                    <a href="delete_project.php?id=<?= $row['id'] ?>"
                                                       class="btn btn-outline-danger" title="Delete"
                                                       onclick="return confirm('Delete this project?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="bi bi-journal-text fs-2 d-block mb-2"></i>
                                            <?= $search ? 'No projects matching "' . htmlspecialchars($search) . '"' : 'No projects yet.' ?>
                                            <br>
                                            <a href="add_project.php" class="btn btn-primary btn-sm mt-2">Add Your First Project</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
