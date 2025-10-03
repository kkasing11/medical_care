-- Tạo database
DROP DATABASE IF EXISTS benh_vien;
CREATE DATABASE benh_vien CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE benh_vien;

-- Bảng tài khoản người dùng
CREATE TABLE tai_khoan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_dang_nhap VARCHAR(100) UNIQUE NOT NULL,
    mat_khau VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    so_dien_thoai VARCHAR(20),
    ho_va_ten VARCHAR(255),
    ngay_sinh DATE,
    gioi_tinh ENUM('nam', 'nu', 'khac'),
    dia_chi VARCHAR(255),
    vai_tro ENUM('giam_doc', 'bac_si', 'nhan_vien', 'duoc_si', 'benh_nhan', 'nguoi_nha') NOT NULL,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng khoa
CREATE TABLE khoa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_khoa VARCHAR(255) NOT NULL,
    mo_ta TEXT
);

-- Bảng bác sĩ
CREATE TABLE bac_si (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tai_khoan_id INT NOT NULL,
    khoa_id INT NOT NULL,
    chuc_vu VARCHAR(100),
    FOREIGN KEY (tai_khoan_id) REFERENCES tai_khoan(id),
    FOREIGN KEY (khoa_id) REFERENCES khoa(id)
);

-- Bảng nhân viên y tế
CREATE TABLE nhan_vien_y_te (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tai_khoan_id INT NOT NULL,
    khoa_id INT NOT NULL,
    FOREIGN KEY (tai_khoan_id) REFERENCES tai_khoan(id),
    FOREIGN KEY (khoa_id) REFERENCES khoa(id)
);

-- Bảng bệnh nhân
CREATE TABLE benh_nhan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tai_khoan_id INT NOT NULL,
    ma_so_benh_an VARCHAR(50) UNIQUE,
    FOREIGN KEY (tai_khoan_id) REFERENCES tai_khoan(id)
);

-- Bảng người thân bệnh nhân
CREATE TABLE nguoi_than (
    id INT AUTO_INCREMENT PRIMARY KEY,
    benh_nhan_id INT NOT NULL,
    tai_khoan_id INT NOT NULL,
    moi_quan_he VARCHAR(100),
    FOREIGN KEY (benh_nhan_id) REFERENCES benh_nhan(id),
    FOREIGN KEY (tai_khoan_id) REFERENCES tai_khoan(id)
);

-- Bảng hồ sơ bệnh án
CREATE TABLE ho_so_benh_an (
    id INT AUTO_INCREMENT PRIMARY KEY,
    benh_nhan_id INT NOT NULL,
    bac_si_id INT,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    chan_doan TEXT,
    phac_do_dieu_tri TEXT,
    FOREIGN KEY (benh_nhan_id) REFERENCES benh_nhan(id),
    FOREIGN KEY (bac_si_id) REFERENCES bac_si(id)
);

-- Bảng tiến trình điều trị
CREATE TABLE tien_trinh_dieu_tri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ho_so_benh_an_id INT NOT NULL,
    nguoi_cap_nhat INT,
    thoi_gian_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP,
    ghi_chu TEXT,
    FOREIGN KEY (ho_so_benh_an_id) REFERENCES ho_so_benh_an(id),
    FOREIGN KEY (nguoi_cap_nhat) REFERENCES tai_khoan(id)
);

-- Bảng thông tin xét nghiệm
CREATE TABLE thong_tin_xet_nghiem (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ho_so_benh_an_id INT NOT NULL,
    loai_xet_nghiem VARCHAR(255),
    thoi_gian_xet_nghiem DATETIME,
    ket_qua TEXT,
    FOREIGN KEY (ho_so_benh_an_id) REFERENCES ho_so_benh_an(id)
);

-- Bảng thuốc
CREATE TABLE thuoc (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_thuoc VARCHAR(255) NOT NULL,
    mo_ta TEXT,
    cach_dung TEXT
);

-- Bảng ghi nhận thuốc cho bệnh nhân
CREATE TABLE thuoc_benh_nhan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    benh_nhan_id INT NOT NULL,
    thuoc_id INT NOT NULL,
    lieu_dung VARCHAR(50),
    ngay_bat_dau DATE,
    ngay_ket_thuc DATE,
    thoi_gian_nhac_uong TIME,
    FOREIGN KEY (benh_nhan_id) REFERENCES benh_nhan(id),
    FOREIGN KEY (thuoc_id) REFERENCES thuoc(id)
);

-- Bảng lịch hẹn khám & tái khám
CREATE TABLE lich_hen_kham (
    id INT AUTO_INCREMENT PRIMARY KEY,
    benh_nhan_id INT NOT NULL,
    bac_si_id INT NOT NULL,
    thoi_gian_hen DATETIME,
    trang_thai ENUM('da_dat', 'da_huy', 'da_kham') DEFAULT 'da_dat',
    ghi_chu TEXT,
    FOREIGN KEY (benh_nhan_id) REFERENCES benh_nhan(id),
    FOREIGN KEY (bac_si_id) REFERENCES bac_si(id)
);

-- Bảng thông báo cho người nhà
CREATE TABLE thong_bao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nguoi_nhan_id INT NOT NULL,
    noi_dung TEXT,
    canh_bao BOOLEAN DEFAULT FALSE,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    da_doc BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (nguoi_nhan_id) REFERENCES tai_khoan(id)
);

-- Bảng chat (bác sĩ - người nhà)
CREATE TABLE tro_chuyen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nguoi_gui_id INT NOT NULL,
    nguoi_nhan_id INT NOT NULL,
    noi_dung TEXT,
    thoi_gian_gui DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoi_gui_id) REFERENCES tai_khoan(id),
    FOREIGN KEY (nguoi_nhan_id) REFERENCES tai_khoan(id)
);

-- Bảng ghi lại lịch sử AI phân tích bệnh án
CREATE TABLE lich_su_ai_phan_tich (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ho_so_benh_an_id INT NOT NULL,
    ket_qua_phan_tich TEXT,
    thoi_gian_phan_tich DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ho_so_benh_an_id) REFERENCES ho_so_benh_an(id)
);

-- Bảng ghi nhận lịch sử chatbot hỗ trợ người nhà
CREATE TABLE lich_su_chatbot (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tai_khoan_id INT NOT NULL,
    cau_hoi TEXT,
    tra_loi TEXT,
    thoi_gian_hoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tai_khoan_id) REFERENCES tai_khoan(id)
);

-- ===========================
-- INSERT SAMPLE DATA
-- ===========================

-- Insert Khoa
INSERT INTO khoa (ten_khoa, mo_ta) VALUES
('Nội khoa', 'Khoa điều trị các bệnh nội khoa'),
('Ngoại khoa', 'Khoa phẫu thuật và điều trị ngoại khoa'),
('Sản phụ khoa', 'Khoa chăm sóc sức khỏe phụ nữ và trẻ em'),
('Nhi khoa', 'Khoa điều trị bệnh về trẻ em'),
('Tim mạch', 'Khoa chuyên về tim mạch');

-- Insert Tài khoản (mật khẩu: 123456 - hash bằng bcrypt)
INSERT INTO tai_khoan (ten_dang_nhap, mat_khau, email, so_dien_thoai, ho_va_ten, ngay_sinh, gioi_tinh, dia_chi, vai_tro) VALUES
-- Bác sĩ
('bs_nguyen', '$2y$10$E4k7jfLDHdyUZWl6uf3/K.WLw2KBvXqLwrLbMHOp4ELo4sYYKvj1C', 'nguyenvana@hospital.vn', '0901234567', 'Nguyễn Văn A', '1980-05-15', 'nam', 'Hà Nội', 'bac_si'),
('bs_tran', '$2y$10$E4k7jfLDHdyUZWl6uf3/K.WLw2KBvXqLwrLbMHOp4ELo4sYYKvj1C', 'tranthib@hospital.vn', '0902234567', 'Trần Thị B', '1985-08-20', 'nu', 'TP.HCM', 'bac_si'),
('bs_le', '$2y$10$E4k7jfLDHdyUZWl6uf3/K.WLw2KBvXqLwrLbMHOp4ELo4sYYKvj1C', 'levanc@hospital.vn', '0903234567', 'Lê Văn C', '1978-03-10', 'nam', 'Đà Nẵng', 'bac_si'),

-- Bệnh nhân
('bn_pham', '$2y$10$E4k7jfLDHdyUZWl6uf3/K.WLw2KBvXqLwrLbMHOp4ELo4sYYKvj1C', 'phamvand@email.com', '0911234567', 'Phạm Văn D', '1990-12-25', 'nam', 'Hà Nội', 'benh_nhan'),
('bn_hoang', '$2y$10$E4k7jfLDHdyUZWl6uf3/K.WLw2KBvXqLwrLbMHOp4ELo4sYYKvj1C', 'hoangthie@email.com', '0912234567', 'Hoàng Thị E', '1995-06-30', 'nu', 'Hải Phòng', 'benh_nhan'),
('bn_vo', '$2y$10$E4k7jfLDHdyUZWl6uf3/K.WLw2KBvXqLwrLbMHOp4ELo4sYYKvj1C', 'vovanf@email.com', '0913234567', 'Võ Văn F', '1988-09-15', 'nam', 'Huế', 'benh_nhan'),

-- Người nhà
('nh_nguyen', '$2y$10$E4k7jfLDHdyUZWl6uf3/K.WLw2KBvXqLwrLbMHOp4ELo4sYYKvj1C', 'nguyenthig@email.com', '0921234567', 'Nguyễn Thị G', '1992-04-20', 'nu', 'Hà Nội', 'nguoi_nha'),
('nh_tran', '$2y$10$E4k7jfLDHdyUZWl6uf3/K.WLw2KBvXqLwrLbMHOp4ELo4sYYKvj1C', 'tranvanh@email.com', '0922234567', 'Trần Văn H', '1987-11-08', 'nam', 'Hải Phòng', 'nguoi_nha');

-- Insert Bác sĩ
INSERT INTO bac_si (tai_khoan_id, khoa_id, chuc_vu) VALUES
(1, 1, 'Trưởng khoa Nội'),
(2, 3, 'Bác sĩ'),
(3, 5, 'Phó khoa Tim mạch');

-- Insert Bệnh nhân
INSERT INTO benh_nhan (tai_khoan_id, ma_so_benh_an) VALUES
(4, 'BA001'),
(5, 'BA002'),
(6, 'BA003');

-- Insert Người thân
INSERT INTO nguoi_than (benh_nhan_id, tai_khoan_id, moi_quan_he) VALUES
(1, 7, 'Vợ'),
(2, 8, 'Chồng');

-- Insert Hồ sơ bệnh án
INSERT INTO ho_so_benh_an (benh_nhan_id, bac_si_id, chan_doan, phac_do_dieu_tri) VALUES
(1, 1, 'Viêm loét dạ dày tá tràng', 'Điều trị nội khoa, uống thuốc kháng sinh và thuốc bảo vệ niêm mạc dạ dày'),
(2, 2, 'Thai kỳ 38 tuần, sức khỏe tốt', 'Theo dõi định kỳ, chuẩn bị sinh'),
(3, 3, 'Tăng huyết áp độ 2', 'Điều chỉnh chế độ ăn uống, tập luyện, dùng thuốc hạ huyết áp');

-- Insert Tiến trình điều trị
INSERT INTO tien_trinh_dieu_tri (ho_so_benh_an_id, nguoi_cap_nhat, ghi_chu) VALUES
(1, 1, 'Bệnh nhân đã uống thuốc đều đặn, triệu chứng giảm'),
(2, 2, 'Thai nhi phát triển bình thường, cân nặng 3.2kg'),
(3, 3, 'Huyết áp ổn định ở mức 130/85 mmHg');

-- Insert Xét nghiệm
INSERT INTO thong_tin_xet_nghiem (ho_so_benh_an_id, loai_xet_nghiem, thoi_gian_xet_nghiem, ket_qua) VALUES
(1, 'Nội soi dạ dày', '2024-10-01 09:00:00', 'Phát hiện viêm loét nhẹ ở tá tràng'),
(2, 'Siêu âm thai', '2024-10-02 10:30:00', 'Thai nhi khỏe mạnh, vị trí bình thường'),
(3, 'Xét nghiệm máu', '2024-10-01 14:00:00', 'Cholesterol cao, đường huyết bình thường');

-- Insert Thuốc
INSERT INTO thuoc (ten_thuoc, mo_ta, cach_dung) VALUES
('Omeprazole 20mg', 'Thuốc ức chế bơm proton', 'Uống 1 viên/ngày trước bữa ăn sáng'),
('Acid folic', 'Vitamin cho bà bầu', 'Uống 1 viên/ngày'),
('Amlodipine 5mg', 'Thuốc hạ huyết áp', 'Uống 1 viên/ngày vào buổi sáng'),
('Amoxicillin 500mg', 'Kháng sinh', 'Uống 2 viên/lần, ngày 3 lần sau ăn');

-- Insert Thuốc cho bệnh nhân
INSERT INTO thuoc_benh_nhan (benh_nhan_id, thuoc_id, lieu_dung, ngay_bat_dau, ngay_ket_thuc, thoi_gian_nhac_uong) VALUES
(1, 1, '1 viên/ngày', '2024-10-01', '2024-10-30', '07:00:00'),
(1, 4, '2 viên/lần, 3 lần/ngày', '2024-10-01', '2024-10-10', '08:00:00'),
(2, 2, '1 viên/ngày', '2024-09-01', '2024-12-01', '08:00:00'),
(3, 3, '1 viên/ngày', '2024-10-01', NULL, '07:00:00');

-- Insert Lịch hẹn khám
INSERT INTO lich_hen_kham (benh_nhan_id, bac_si_id, thoi_gian_hen, trang_thai, ghi_chu) VALUES
(1, 1, '2024-10-15 09:00:00', 'da_dat', 'Tái khám sau 2 tuần điều trị'),
(2, 2, '2024-10-10 10:00:00', 'da_dat', 'Khám thai định kỳ'),
(3, 3, '2024-10-08 14:00:00', 'da_dat', 'Kiểm tra huyết áp');

-- Insert Thông báo
INSERT INTO thong_bao (nguoi_nhan_id, noi_dung, canh_bao) VALUES
(7, 'Bệnh nhân Phạm Văn D có lịch tái khám vào ngày 15/10/2024 lúc 9:00 sáng', FALSE),
(8, 'Bệnh nhân Hoàng Thị E cần chuẩn bị nhập viện để sinh trong tuần tới', TRUE);
