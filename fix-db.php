<?php
// Tạo password hash MỚI từ PHP
$password = '123456';
$new_hash = password_hash($password, PASSWORD_BCRYPT);

echo "<h2>🔐 TẠO PASSWORD HASH MỚI</h2>";
echo "<p><strong>Password:</strong> $password</p>";
echo "<p><strong>New Hash:</strong></p>";
echo "<pre style='background: #f0f0f0; padding: 15px; border: 2px solid #333;'>$new_hash</pre>";

// Test verify
if (password_verify($password, $new_hash)) {
    echo "<p style='color: green; font-weight: bold;'>✅ Hash này ĐÚNG!</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Hash này SAI!</p>";
}

// Update vào database
require_once 'config/config.php';
require_once 'config/Database.php';

$db = new Database();
$conn = $db->connect();

echo "<hr>";
echo "<h3>📝 Đang update vào database...</h3>";

$sql = "UPDATE tai_khoan SET mat_khau = ?";
$stmt = $conn->prepare($sql);
$result = $stmt->execute([$new_hash]);

if ($result) {
    echo "<p style='color: green;'>✅ ĐÃ UPDATE THÀNH CÔNG!</p>";
    echo "<p>Rows affected: " . $stmt->rowCount() . "</p>";
    
    // Test lại
    echo "<hr>";
    echo "<h3>🧪 Test verify với user bs_nguyen:</h3>";
    
    $test_user = $db->fetchOne("SELECT * FROM tai_khoan WHERE ten_dang_nhap = 'bs_nguyen'", []);
    if ($test_user && password_verify($password, $test_user['mat_khau'])) {
        echo "<div style='background: #d4edda; color: #155724; padding: 20px; border: 3px solid #28a745; border-radius: 10px; margin: 20px 0;'>";
        echo "<h2 style='margin: 0;'>🎉🎉🎉 THÀNH CÔNG!</h2>";
        echo "<h3>Bây giờ bạn có thể đăng nhập với:</h3>";
        echo "<ul style='font-size: 18px;'>";
        echo "<li>Username: <strong>bs_nguyen</strong></li>";
        echo "<li>Password: <strong>123456</strong></li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<a href='modules/auth/login.php' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 18px; display: inline-block; margin-top: 20px;'>→ ĐĂNG NHẬP NGAY!</a>";
    } else {
        echo "<p style='color: red;'>❌ Vẫn chưa đúng!</p>";
    }
} else {
    echo "<p style='color: red;'>❌ LỖI UPDATE!</p>";
    print_r($stmt->errorInfo());
}
?>