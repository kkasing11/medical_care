-- FIX PASSWORD CHO TẤT CẢ TÀI KHOẢN
-- Password: 123456 (đã hash với bcrypt)
-- Hash: $2y$10$E4k7jfLDHdyUZWl6uf3/K.WLw2KBvXqLwrLbMHOp4ELo4sYYKvj1C

USE benh_vien;

-- Cập nhật password cho tất cả tài khoản
UPDATE tai_khoan 
SET mat_khau = '$2y$10$E4k7jfLDHdyUZWl6uf3/K.WLw2KBvXqLwrLbMHOp4ELo4sYYKvj1C';

-- Kiểm tra kết quả
SELECT 
    ten_dang_nhap,
    ho_va_ten,
    vai_tro,
    'Password đã update: 123456' as ghi_chu
FROM tai_khoan
ORDER BY vai_tro, id;
