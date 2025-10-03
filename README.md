# HỆ THỐNG QUẢN LÝ BỆNH ÁN

Hệ thống quản lý bệnh án điện tử cho bác sĩ và người nhà bệnh nhân.

## TÍNH NĂNG

### Chức năng cho Bác sĩ (1-7):
1. ✅ Quản lý hồ sơ bệnh án
2. ✅ Cập nhật tiến trình điều trị
3. ✅ Gửi thông báo cho người nhà (hệ thống + Gmail SMTP)
4. ✅ Quản lý thông tin xét nghiệm
5. ✅ Lịch hẹn khám & tái khám
6. ✅ Quản lý thông tin người thân
7. ✅ Website responsive (PC, tablet, smartphone)

## YÊU CẦU HỆ THỐNG

- XAMPP (hoặc WAMP/LAMP)
- PHP >= 7.4
- MySQL >= 5.7
- Web Browser hiện đại (Chrome, Firefox, Edge)

## HƯỚNG DẪN CÀI ĐẶT

### Bước 1: Cài đặt XAMPP
1. Tải XAMPP từ: https://www.apachefriends.org/
2. Cài đặt XAMPP vào thư mục mặc định (C:\xampp)
3. Khởi động Apache và MySQL từ XAMPP Control Panel

### Bước 2: Copy Project
1. Copy thư mục `hospital-management` vào `C:\xampp\htdocs\`
2. Đường dẫn cuối cùng: `C:\xampp\htdocs\hospital-management`

### Bước 3: Tạo Database
1. Mở trình duyệt, truy cập: http://localhost/phpmyadmin
2. Click tab "SQL"
3. Copy toàn bộ nội dung file `database/setup.sql`
4. Paste vào ô SQL và click "Go"
5. Database `benh_vien` sẽ được tạo tự động với sample data

### Bước 4: Cấu hình (Tùy chọn)
Mở file `config/config.php` và chỉnh sửa nếu cần:

```php
// Database (mặc định đã đúng cho XAMPP)
define('DB_HOST', 'localhost');
define('DB_NAME', 'benh_vien');
define('DB_USER', 'root');
define('DB_PASS', '');

// Email SMTP (nếu muốn gửi email thật)
define('MAIL_USERNAME', 'your-email@gmail.com');
define('MAIL_PASSWORD', 'your-app-password');
```

### Bước 5: Truy cập Website
1. Mở trình duyệt
2. Truy cập: http://localhost/hospital-management
3. Trang đăng nhập sẽ hiện ra

## TÀI KHOẢN MẪU

### Bác Sĩ:
- **Username**: `bs_nguyen` | **Password**: `123456`
- **Username**: `bs_tran` | **Password**: `123456`
- **Username**: `bs_le` | **Password**: `123456`

### Bệnh Nhân:
- **Username**: `bn_pham` | **Password**: `123456`
- **Username**: `bn_hoang` | **Password**: `123456`

### Người Nhà:
- **Username**: `nh_nguyen` | **Password**: `123456`
- **Username**: `nh_tran` | **Password**: `123456`

## CẤU TRÚC PROJECT

```
hospital-management/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── images/
├── config/
│   ├── config.php
│   └── Database.php
├── database/
│   └── setup.sql
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php
├── modules/
│   ├── auth/
│   │   ├── login.php
│   │   └── logout.php
│   ├── medical-records/
│   │   ├── list.php
│   │   ├── view.php
│   │   └── update.php
│   ├── appointments/
│   │   ├── list.php
│   │   └── update-appointment.php
│   ├── notifications/
│   │   ├── list.php
│   │   └── send.php
│   └── relatives/
│       └── list.php
└── index.php
```

## HƯỚNG DẪN SỬ DỤNG

### Đăng nhập:
1. Truy cập: http://localhost/hospital-management
2. Nhập username và password (xem phần Tài khoản mẫu)
3. Click "Đăng Nhập"

### Quản lý Hồ sơ bệnh án:
1. Menu: "Hồ Sơ Bệnh Án"
2. Click "Xem" để xem chi tiết
3. Click "Cập Nhật" để thêm tiến trình điều trị mới

### Gửi thông báo:
1. Menu: "Thông Báo"
2. Chọn bệnh nhân
3. Nhập nội dung thông báo
4. Chọn "Cảnh báo" nếu khẩn cấp
5. Click "Gửi Thông Báo"

### Quản lý lịch hẹn:
1. Menu: "Lịch Hẹn"
2. Xem danh sách lịch hẹn
3. Click "Đã Khám" hoặc "Hủy" để cập nhật

## CẤU HÌNH EMAIL SMTP (Tùy chọn)

Để gửi email thông báo thật qua Gmail:

### 1. Tạo App Password cho Gmail:
1. Đăng nhập Gmail
2. Vào: https://myaccount.google.com/security
3. Bật "2-Step Verification"
4. Tìm "App passwords"
5. Tạo password mới cho "Mail"
6. Copy password này

### 2. Cấu hình trong file config.php:
```php
define('MAIL_USERNAME', 'youremail@gmail.com');
define('MAIL_PASSWORD', 'app-password-vừa-tạo');
define('MAIL_FROM', 'youremail@gmail.com');
```

### 3. Cài đặt PHPMailer (nếu chưa có):
```bash
composer require phpmailer/phpmailer
```

## XỬ LÝ LỖI THƯỜNG GẶP

### Lỗi: "Access denied for user"
- Kiểm tra username/password MySQL trong config.php
- Mặc định XAMPP: user='root', pass=''

### Lỗi: "Table doesn't exist"
- Chạy lại file database/setup.sql trong phpMyAdmin

### Lỗi: 404 Not Found
- Kiểm tra đường dẫn: phải là htdocs/hospital-management
- Restart Apache trong XAMPP

### Trang trắng khi truy cập
- Bật display_errors trong php.ini
- Kiểm tra PHP error log

## CÔNG NGHỆ SỬ DỤNG

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: Bootstrap 5.3
- **Icons**: Bootstrap Icons 1.11
- **JavaScript**: Vanilla JS (ES6+)

## TÍNH NĂNG BẢO MẬT

- Password hashing với bcrypt
- SQL Injection prevention (PDO Prepared Statements)
- XSS protection (htmlspecialchars)
- Session management
- Input sanitization

## RESPONSIVE DESIGN

Website tương thích với:
- ✅ Desktop (1920x1080+)
- ✅ Laptop (1366x768+)
- ✅ Tablet (768x1024)
- ✅ Mobile (375x667+)

## LƯU Ý

- Project này dành cho mục đích học tập
- Chưa implement đầy đủ các tính năng bảo mật cho production
- Cần bổ sung validation và error handling cho production
- Email SMTP cần cấu hình riêng

## LIÊN HỆ & HỖ TRỢ

Nếu gặp vấn đề khi cài đặt:
1. Kiểm tra Apache và MySQL đã chạy trong XAMPP
2. Kiểm tra đường dẫn project đúng
3. Kiểm tra database đã được import

## PHÁT TRIỂN THÊM

Có thể mở rộng thêm:
- Chức năng 8-19 (theo yêu cầu ban đầu)
- Module cho vai trò khác (giám đốc, dược sĩ)
- Dashboard analytics
- Export PDF/Excel
- Upload file đính kèm
- Video call tư vấn

---
**Version**: 1.0.0  
**Last Updated**: 2024
