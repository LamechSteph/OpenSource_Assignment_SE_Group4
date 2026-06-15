<?php
/**
 * Delete Project
 *
 * FYPTS - Final Year Project Tracking System
 * Deletes a project after confirmation.
 */

require_once __DIR__ . '/../config/db.php';
requireLogin();

$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($project_id <= 0) {
    $_SESSION['error'] = 'Invalid project ID.';
    header('Location: view_projects.php');
    exit();
}

$conn = getConnection();

// Verify project exists
$check = $conn->prepare('SELECT id FROM projects WHERE id = ?');
$check->bind_param('i', $project_id);
$check->execute();
if ($check->get_result()->num_rows === 0) {
    $_SESSION['error'] = 'Project not found.';
    header('Location: view_projects.php');
    exit();
}
$check->close();

// Delete the project
$stmt = $conn->prepare('DELETE FROM projects WHERE id = ?');
$stmt->bind_param('i', $project_id);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Project deleted successfully.';
} else {
    $_SESSION['error'] = 'Failed to delete the project. Please try again.';
}

$stmt->close();
header('Location: view_projects.php');
exit();
