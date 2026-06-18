<?php

/**
 * Project Progress Tracking Report
 *
 * FYPTS - Final Year Project Tracking System
 * Displays a dedicated reporting page with project progress data,
 * including visual progress bars and status information.
 */

require_once __DIR__ . '/config/db.php';
requireLogin();

$conn = getConnection();

// Fetch all projects with progress info, ordered by progress descending
$projects = $conn->query('
    SELECT id, project_title, student_name, supervisor_name, status, progress_percentage
    FROM projects
    ORDER BY progress_percentage DESC, project_title ASC
');

// Aggregate stats
$total = $projects->num_rows;
$avg = $conn->query('SELECT AVG(progress_percentage) as avg FROM projects');
$avg_progress = round((float)($avg->fetch_assoc()['avg'] ?? 0));
$avg->free();

$completed_count = $conn->query("SELECT COUNT(*) as c FROM projects WHERE progress_percentage = 100");
$completed_projects = $completed_count->fetch_assoc()['c'];
$completed_count->free();

function progressBarColor(int $pct): string
{
    if ($pct >= 100) return 'success';
    if ($pct >= 75)  return 'info';
    if ($pct >= 25)  return 'warning';
    return 'danger';
}

function progressLabel(int $pct): string
{
    if ($pct >= 100) return 'Completed';
    if ($pct >= 75)  return 'Near Completion';
    if ($pct >= 25)  return 'In Progress';
    return 'Early Stage';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Progress - FYPTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <div class="container py-4">
        <div class="mb-4">
            <h1 class="h3 mb-1">Project Progress Report</h1>
            <p class="text-muted mb-0">Track completion progress across all final year projects</p>
        </div>

        <?php
        $success_msg = $_SESSION['success'] ?? '';
        unset($_SESSION['success']);
        if ($success_msg): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($success_msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <p class="text-muted small mb-1">Total Projects</p>
                        <h2 class="h2 mb-0 fw-bold"><?= $total ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <p class="text-muted small mb-1">Average Progress</p>
                        <h2 class="h2 mb-0 fw-bold text-primary"><?= $avg_progress ?>%</h2>
                        <div class="progress mt-2" style="height:8px;">
                            <div class="progress-bar" style="width:<?= $avg_progress ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <p class="text-muted small mb-1">Completed Projects</p>
                        <h2 class="h2 mb-0 fw-bold text-success"><?= $completed_projects ?> / <?= $total ?></h2>
                        <div class="progress mt-2" style="height:8px;">
                            <?php $completion_pct = $total > 0 ? round(($completed_projects / $total) * 100) : 0; ?>
                            <div class="progress-bar bg-success" style="width:<?= $completion_pct ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Distribution -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0">
                    <i class="bi bi-bar-chart-fill text-primary"></i>
                    Progress Distribution
                </h5>
            </div>
            <div class="card-body p-4">
                <?php
                $ranges = [
                    ['label' => '0% – 24% (Early Stage)', 'min' => 0, 'max' => 24, 'color' => 'danger'],
                    ['label' => '25% – 49% (Underway)',   'min' => 25, 'max' => 49, 'color' => 'warning'],
                    ['label' => '50% – 74% (Midway)',     'min' => 50, 'max' => 74, 'color' => 'info'],
                    ['label' => '75% – 99% (Near Done)',  'min' => 75, 'max' => 99, 'color' => 'primary'],
                    ['label' => '100% (Completed)',       'min' => 100, 'max' => 100, 'color' => 'success'],
                ];
                $projects->data_seek(0);
                $all = [];
                while ($r = $projects->fetch_assoc()) {
                    $all[] = $r;
                }
                foreach ($ranges as $range):
                    $count = 0;
                    foreach ($all as $p) {
                        if ($p['progress_percentage'] >= $range['min'] && $p['progress_percentage'] <= $range['max']) {
                            $count++;
                        }
                    }
                    $pct = $total > 0 ? round(($count / $total) * 100) : 0;
                ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small"><?= $range['label'] ?></span>
                            <span class="small fw-bold"><?= $count ?> (<?= $pct ?>%)</span>
                        </div>
                        <div class="progress" style="height:10px;">
                            <div class="progress-bar bg-<?= $range['color'] ?>" style="width:<?= $pct ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Projects Progress Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0">
                    <i class="bi bi-list-check text-primary"></i>
                    All Projects — Progress Overview
                </h5>
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
                                <th>Stage</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($all) > 0): $i = 1; ?>
                                <?php foreach ($all as $row): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td class="fw-medium"><?= htmlspecialchars($row['project_title']) ?></td>
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
                                        <td style="min-width:180px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height:10px;">
                                                    <div class="progress-bar bg-<?= progressBarColor((int)$row['progress_percentage']) ?>"
                                                        style="width:<?= $row['progress_percentage'] ?>%"></div>
                                                </div>
                                                <strong class="small" style="width:40px;"><?= $row['progress_percentage'] ?>%</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= progressBarColor((int)$row['progress_percentage']) ?>">
                                                <?= progressLabel((int)$row['progress_percentage']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="projects/view_projects.php?id=<?= $row['id'] ?>"
                                                class="btn btn-sm btn-outline-primary">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-journal-text fs-2 d-block mb-2"></i>
                                        No projects found.
                                        <br>
                                        <a href="projects/add_project.php" class="btn btn-primary btn-sm mt-2">Add Your First Project</a>
                                    </td>
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