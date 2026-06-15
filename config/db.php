<?php

/**
 * Database Configuration and Connection
 *
 * Final Year Project Tracking System (FYPTS)
 * Uses MySQLi with prepared statements for secure database access.
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'fypts');

/**
 * Get database connection
 *
 * @return mysqli Returns a MySQLi connection object
 */
function getConnection(): mysqli
{
    static $conn = null;

    if ($conn === null) {
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($conn->connect_error) {
                throw new Exception('Database connection failed: ' . $conn->connect_error);
            }

            $conn->set_charset('utf8mb4');
        } catch (Exception $e) {
            die('Database connection error. Please check your configuration.<br>' . $e->getMessage());
        }
    }

    return $conn;
}

/**
 * Start a secure session
 */
function startSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Check if user is logged in, redirect if not
 */
function requireLogin(): void
{
    startSession();
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = 'Please login to access this page.';
        header('Location: ../auth/login.php');
        exit();
    }
}

/**
 * Check if user has a specific role
 *
 * @param string $role Required role
 * @return bool True if user has the required role
 */
function hasRole(string $role): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Check if user has any of the specified roles
 *
 * @param array $roles Array of allowed roles
 * @return bool True if user has one of the roles
 */
function hasAnyRole(array $roles): bool
{
    return isset($_SESSION['role']) && in_array($_SESSION['role'], $roles);
}
