<?php

/**
 * Navigation Bar Include
 *
 * FYPTS - Final Year Project Tracking System
 * Reusable navigation partial for authenticated pages.
 */
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="../dashboard.php">FYPTS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>"
                        href="../dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'view_projects.php' ? 'active' : '' ?>"
                        href="../projects/view_projects.php">
                        <i class="bi bi-journal-text"></i> Projects
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'add_project.php' ? 'active' : '' ?>"
                        href="../projects/add_project.php">
                        <i class="bi bi-plus-circle"></i> Add Project
                    </a>
                </li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $current_page === 'users.php' ? 'active' : '' ?>"
                            href="../users.php">
                            <i class="bi bi-people"></i> Users
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'search_project.php' ? 'active' : '' ?>"
                        href="../projects/search_project.php">
                        <i class="bi bi-search"></i> Search
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <?= htmlspecialchars($_SESSION['fullname'] ?? 'User') ?>
                        <span class="badge bg-light text-primary ms-1"><?= htmlspecialchars($_SESSION['role'] ?? '') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text small text-muted"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></span></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="../auth/logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>