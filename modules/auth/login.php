<?php
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten_dang_nhap = sanitize($_POST['ten_dang_nhap']);
    $mat_khau = $_POST['mat_khau'];
    
    if (empty($ten_dang_nhap) || empty($mat_khau)) {
        $error = 'Vui lòng nhập đầy đủ thông tin!';
    } else {
        $db = new Database();
        $conn = $db->connect();
        
        $sql = "SELECT * FROM tai_khoan WHERE ten_dang_nhap = ?";
        $user = $db->fetchOne($sql, [$ten_dang_nhap]);
        
        if ($user && password_verify($mat_khau, $user['mat_khau'])) {
            // Đăng nhập thành công
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['ten_dang_nhap'] = $user['ten_dang_nhap'];
            $_SESSION['ho_va_ten'] = $user['ho_va_ten'];
            $_SESSION['vai_tro'] = $user['vai_tro'];
            $_SESSION['email'] = $user['email'];
            
            header("Location: " . SITE_URL . "/index.php");
            exit();
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
        }
    }
}

$pageTitle = 'Đăng Nhập';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-container {
            max-width: 450px;
            margin: auto;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 30px;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
        }
        .btn-login:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            transition: all 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="card">
                <div class="card-header text-center">
                    <i class="bi bi-hospital fs-1"></i>
                    <h3 class="mt-2"><?php echo SITE_NAME; ?></h3>
                    <p class="mb-0">Đăng nhập để tiếp tục</p>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-person"></i> Tên đăng nhập</label>
                            <input type="text" name="ten_dang_nhap" class="form-control" required autofocus>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-lock"></i> Mật khẩu</label>
                            <input type="password" name="mat_khau" class="form-control" required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-login">
                                <i class="bi bi-box-arrow-in-right"></i> Đăng Nhập
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="alert alert-info mb-0">
                        <small>
                            <strong>Tài khoản mẫu:</strong><br>
                            <i class="bi bi-person-badge"></i> Bác sĩ: <code>bs_nguyen</code> / <code>123456</code><br>
                            <i class="bi bi-person-badge"></i> Bác sĩ: <code>bs_tran</code> / <code>123456</code>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
