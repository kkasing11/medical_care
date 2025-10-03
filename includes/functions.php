<?php
// Helper functions

function redirect($url) {
    header("Location: " . SITE_URL . "/" . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('modules/auth/login.php');
    }
}

function requireRole($role) {
    requireLogin();
    if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== $role) {
        redirect('index.php');
    }
}

function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'ten_dang_nhap' => $_SESSION['ten_dang_nhap'] ?? '',
            'ho_va_ten' => $_SESSION['ho_va_ten'] ?? '',
            'vai_tro' => $_SESSION['vai_tro'] ?? '',
            'email' => $_SESSION['email'] ?? ''
        ];
    }
    return null;
}

function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    if (empty($datetime)) return '';
    return date($format, strtotime($datetime));
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function showAlert($message, $type = 'info') {
    $alertClass = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info'
    ];
    
    $class = $alertClass[$type] ?? 'alert-info';
    return "<div class='alert {$class} alert-dismissible fade show' role='alert'>
                {$message}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    return preg_match('/^[0-9]{10,11}$/', $phone);
}
?>
