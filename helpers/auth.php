<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Memeriksa apakah user sudah login (Versi camelCase)
 */
function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }
}

/**
 * Alias fungsi untuk penulisan versi snake_case (check_auth)
 */
function check_auth() {
    checkAuth();
}

/**
 * Memeriksa role user (misal: 'admin' atau 'staff')
 */
function checkRole($allowedRoles = []) {
    checkAuth();
    if (!in_array($_SESSION['role'], $allowedRoles)) {
        header('Location: /dashboard.php');
        exit;
    }
}

function check_role($allowedRoles = []) {
    checkRole($allowedRoles);
}