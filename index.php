<?php

/**
 * Landing Page
 *
 * FYPTS - Final Year Project Tracking System
 * Public landing page. Redirects authenticated users to dashboard.
 */

require_once __DIR__ . '/config/db.php';
startSession();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FYPTS - Final Year Project Tracking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-mortarboard-fill"></i> FYPTS
            </a>
            <div class="ms-auto">
                <a href="auth/login.php" class="btn btn-outline-light btn-sm me-2">Sign In</a>
                <a href="auth/register.php" class="btn btn-light btn-sm">Get Started</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="bg-primary text-white py-5">
        <div class="container py-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-3">Final Year Project Tracking System</h1>
                    <p class="lead mb-4 opacity-90">
                        A centralized platform for universities to manage, track, and monitor
                        final year student projects from proposal submission to completion.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="auth/register.php" class="btn btn-light btn-lg px-4">Get Started</a>
                        <a href="auth/login.php" class="btn btn-outline-light btn-lg px-4">Sign In</a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="bi bi-journal-text" style="font-size: 12rem; opacity: 0.2;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Key Features</h2>
                <p class="text-muted">Everything you need to manage final year projects</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-3">
                        <div class="card-body">
                            <div class="bg-primary bg-opacity-10 rounded-3 d-inline-flex p-3 mb-3">
                                <i class="bi bi-people fs-2 text-primary"></i>
                            </div>
                            <h5 class="card-title">User Management</h5>
                            <p class="card-text text-muted small">Admins can create, view, and manage users with role-based access for Admin, Supervisor, and Student accounts.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-3">
                        <div class="card-body">
                            <div class="bg-success bg-opacity-10 rounded-3 d-inline-flex p-3 mb-3">
                                <i class="bi bi-journal-text fs-2 text-success"></i>
                            </div>
                            <h5 class="card-title">Project Management</h5>
                            <p class="card-text text-muted small">Add, edit, delete, and view projects with detailed information including title, description, and assigned supervisor.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-3">
                        <div class="card-body">
                            <div class="bg-warning bg-opacity-10 rounded-3 d-inline-flex p-3 mb-3">
                                <i class="bi bi-graph-up-arrow fs-2 text-warning"></i>
                            </div>
                            <h5 class="card-title">Status Tracking</h5>
                            <p class="card-text text-muted small">Track project progress through six defined stages from Proposal Submitted to Completed with percentage tracking.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 text-center p-3">
                        <div class="card-body">
                            <div class="bg-info bg-opacity-10 rounded-3 d-inline-flex p-3 mb-3">
                                <i class="bi bi-search fs-2 text-info"></i>
                            </div>
                            <h5 class="card-title">Search & Dashboard</h5>
                            <p class="card-text text-muted small">Powerful search by project title and an overview dashboard showing total, completed, in-progress, and pending projects.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Status Flow -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Project Lifecycle</h2>
                <p class="text-muted">Projects move through six defined stages</p>
            </div>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <span class="badge bg-secondary fs-6 p-3">Proposal Submitted</span>
                <i class="bi bi-arrow-right text-muted align-self-center"></i>
                <span class="badge bg-primary fs-6 p-3">Approved</span>
                <i class="bi bi-arrow-right text-muted align-self-center"></i>
                <span class="badge bg-dark fs-6 p-3">Design Phase</span>
                <i class="bi bi-arrow-right text-muted align-self-center"></i>
                <span class="badge bg-warning fs-6 p-3">Development Phase</span>
                <i class="bi bi-arrow-right text-muted align-self-center"></i>
                <span class="badge bg-info fs-6 p-3">Testing Phase</span>
                <i class="bi bi-arrow-right text-muted align-self-center"></i>
                <span class="badge bg-success fs-6 p-3">Completed</span>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-1 small">&copy; <?= date('Y') ?> FYPTS. Final Year Project Tracking System</p>
            <p class="mb-0 small text-muted">Open Source Technologies Academic Project</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>