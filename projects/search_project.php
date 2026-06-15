<?php
/**
 * Search Projects
 *
 * FYPTS - Final Year Project Tracking System
 * Dedicated search page for finding projects by title.
 */

require_once __DIR__ . '/../config/db.php';
requireLogin();

$conn = getConnection();
$results = null;
$search_term = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['q'])) {
    $search_term = trim($_GET['q']);

    if (!empty($search_term)) {
        $like = '%' . $search_term . '%';
        $stmt = $conn->prepare('SELECT * FROM projects WHERE project_title LIKE ? ORDER BY created_at DESC');
        $stmt->bind_param('s', $like);
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Projects - FYPTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../includes/navbar.php'; ?>

    <div class="container py-4">
        <div class="mb-4">
            <h1 class="h3 mb-1">Search Projects</h1>
            <p class="text-muted mb-0">Find projects by title</p>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form method="GET" action="">
                    <div class="row g-2">
                        <div class="col-md-8 col-lg-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control" name="q"
                                       placeholder="Enter project title..."
                                       value="<?= htmlspecialchars($search_term) ?>" autofocus>
                                <button class="btn btn-primary px-4" type="submit">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['q'])): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <?php if ($results): ?>
                            Search Results for "<?= htmlspecialchars($search_term) ?>"
                            <span class="badge bg-secondary ms-2"><?= $results->num_rows ?> found</span>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if ($results && $results->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Project Title</th>
                                        <th>Student</th>
                                        <th>Supervisor</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $results->fetch_assoc()): ?>
                                        <tr>
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
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1" style="height:8px;">
                                                        <div class="progress-bar" style="width:<?= $row['progress_percentage'] ?>%"></div>
                                                    </div>
                                                    <small class="text-muted"><?= $row['progress_percentage'] ?>%</small>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="view_projects.php?id=<?= $row['id'] ?>"
                                                   class="btn btn-sm btn-outline-primary">View</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php elseif (!empty($search_term)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-search fs-1 d-block mb-3"></i>
                            <p class="mb-1">No projects found matching "<strong><?= htmlspecialchars($search_term) ?></strong>"</p>
                            <small>Try a different search term or browse <a href="view_projects.php">all projects</a>.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($results): $results->free(); endif; ?>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-search fs-1 d-block mb-3"></i>
                <p>Enter a project title above to search.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>
