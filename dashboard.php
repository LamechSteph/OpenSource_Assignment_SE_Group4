<?php

/**
 * Dashboard
 *
 * FYPTS - Final Year Project Tracking System
 * Displays summary statistics and recent projects.
 */

require_once __DIR__ . '/config/db.php';
requireLogin();

$conn = getConnection();

// Dashboard statistics
$stats = [];

// Total projects
$result = $conn->query('SELECT COUNT(*) as total FROM projects');
$stats['total'] = $result->fetch_assoc()['total'];

// Completed projects
$result = $conn->query("SELECT COUNT(*) as total FROM projects WHERE status = 'Completed'");
$stats['completed'] = $result->fetch_assoc()['total'];

// In progress (Development + Testing phases)
$result = $conn->query("SELECT COUNT(*) as total FROM projects WHERE status IN ('Development Phase', 'Testing Phase')");
$stats['in_progress'] = $result->fetch_assoc()['total'];

// Pending (Proposal Submitted + Approved + Design Phase)
$result = $conn->query("SELECT COUNT(*) as total FROM projects WHERE status IN ('Proposal Submitted', 'Approved', 'Design Phase')");
$stats['pending'] = $result->fetch_assoc()['total'];

// Recent projects (last 5)
$recent = $conn->query('SELECT id, project_title, student_name, status, progress_percentage, created_at FROM projects ORDER BY created_at DESC LIMIT 5');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - FYPTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <div class="container py-4">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Dashboard</h1>
                <p class="text-muted mb-0">Overview of all final year projects</p>
            </div>
            <a href="projects/add_project.php" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> New Project
            </a>
        </div>

        <?php
        $success_msg = $_SESSION['success'] ?? '';
        unset($_SESSION['success']);
        if ($success_msg): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success_msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Total Projects</p>
                                <h2 class="h2 mb-0 fw-bold"><?= $stats['total'] ?></h2>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                                <i class="bi bi-journal-text fs-4 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Completed</p>
                                <h2 class="h2 mb-0 fw-bold text-success"><?= $stats['completed'] ?></h2>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-3">
                                <i class="bi bi-check-circle fs-4 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">In Progress</p>
                                <h2 class="h2 mb-0 fw-bold text-info"><?= $stats['in_progress'] ?></h2>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded-3">
                                <i class="bi bi-arrow-repeat fs-4 text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted small mb-1">Pending</p>
                                <h2 class="h2 mb-0 fw-bold text-warning"><?= $stats['pending'] ?></h2>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                                <i class="bi bi-clock fs-4 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0">Recent Projects</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Project Title</th>
                                <th>Student</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Created</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent->num_rows > 0): ?>
                                <?php while ($row = $recent->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-medium"><?= htmlspecialchars($row['project_title']) ?></td>
                                        <td><?= htmlspecialchars($row['student_name']) ?></td>
                                        <td>
                                            <span class="badge bg-<?php
                                                                    $status = $row['status'];
                                                                    if ($status === 'Completed') echo 'success';
                                                                    elseif ($status === 'Approved') echo 'primary';
                                                                    elseif ($status === 'Proposal Submitted') echo 'secondary';
                                                                    elseif ($status === 'Testing Phase') echo 'info';
                                                                    elseif ($status === 'Development Phase') echo 'warning';
                                                                    else echo 'dark';
                                                                    ?>"><?= htmlspecialchars($status) ?></span>
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
                                        <td><small class="text-muted"><?= date('M d, Y', strtotime($row['created_at'])) ?></small></td>
                                        <td>
                                            <a href="projects/view_projects.php?id=<?= $row['id'] ?>"
                                                class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No projects found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>

</html>