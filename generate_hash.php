<?php
// Script để tạo password hash cho 123456
$password = '123456';
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "Password: $password\n";
echo "Hash: $hash\n";
echo "\n";

// Verify hash
if (password_verify($password, $hash)) {
    echo "✓ Hash is valid!\n";
} else {
    echo "✗ Hash is invalid!\n";
}
?>
