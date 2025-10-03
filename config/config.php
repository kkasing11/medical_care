<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'benh_vien');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Site configuration
define('SITE_NAME', 'Hệ Thống Quản Lý Bệnh Án');
define('SITE_URL', 'http://localhost/hospital-management');

// Email configuration (Gmail SMTP)
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'your-email@gmail.com'); // Thay bằng email của bạn
define('MAIL_PASSWORD', 'your-app-password'); // App password của Gmail
define('MAIL_FROM', 'your-email@gmail.com');
define('MAIL_FROM_NAME', 'Hệ Thống Bệnh Viện');

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Session configuration
ini_set('session.cookie_httponly', 1);
session_start();
?>
