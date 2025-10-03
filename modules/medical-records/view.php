<?php
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../includes/functions.php';

requireRole('bac_si');

$db = new Database();
$conn = $db->connect();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    redirect('modules/medical-records/list.php');
}

// Lấy thông tin hồ sơ bệnh án
$sql = "SELECT 
            hsba.*,
            bn.ma_so_benh_an,
            tk.ho_va_ten as ten_benh_nhan,
            tk.ngay_sinh,
            tk.gioi_tinh,
            tk.so_dien_thoai,
            tk.dia_chi,
            tk.email
        FROM ho_so_benh_an hsba
        JOIN benh_nhan bn ON hsba.benh_nhan_id = bn.id
        JOIN tai_khoan tk ON bn.tai_khoan_id = tk.id
        WHERE hsba.id = ?";

$record = $db->fetchOne($sql, [$id]);

if (!$record) {
    redirect('modules/medical-records/list.php');
}

// Lấy tiến trình điều trị
$sql_tien_trinh = "SELECT tt.*, tk.ho_va_ten as nguoi_cap_nhat_ten
                   FROM tien_trinh_dieu_tri tt
                   JOIN tai_khoan tk ON tt.nguoi_cap_nhat = tk.id
                   WHERE tt.ho_so_benh_an_id = ?
                   ORDER BY tt.thoi_gian_cap_nhat DESC";
$tien_trinh = $db->fetchAll($sql_tien_trinh, [$id]);

// Lấy xét nghiệm
$sql_xet_nghiem = "SELECT * FROM thong_tin_xet_nghiem 
                   WHERE ho_so_benh_an_id = ?
                   ORDER BY thoi_gian_xet_nghiem DESC";
$xet_nghiem = $db->fetchAll($sql_xet_nghiem, [$id]);

$pageTitle = 'Chi Tiết Hồ Sơ Bệnh Án';
include '../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="mb-3">
            <a href="list.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay Lại
            </a>
            <a href="update.php?id=<?php echo $id; ?>" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Cập Nhật Tiến Trình
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Thông tin bệnh án -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-file-medical"></i> Thông Tin Bệnh Án</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Mã Bệnh Án:</strong><br>
                        <span class="badge bg-info fs-6"><?php echo htmlspecialchars($record['ma_so_benh_an']); ?></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Ngày Tạo:</strong><br>
                        <?php echo formatDateTime($record['ngay_tao']); ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <strong>Chẩn Đoán:</strong><br>
                    <p class="border p-3 bg-light"><?php echo nl2br(htmlspecialchars($record['chan_doan'])); ?></p>
                </div>
                
                <div class="mb-3">
                    <strong>Phác Đồ Điều Trị:</strong><br>
                    <p class="border p-3 bg-light"><?php echo nl2br(htmlspecialchars($record['phac_do_dieu_tri'])); ?></p>
                </div>
            </div>
        </div>

        <!-- Tiến trình điều trị -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-activity"></i> Tiến Trình Điều Trị</h5>
            </div>
            <div class="card-body">
                <?php if (empty($tien_trinh)): ?>
                    <p class="text-muted">Chưa có tiến trình điều trị.</p>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($tien_trinh as $tt): ?>
                            <div class="border-start border-3 border-success ps-3 mb-3">
                                <small class="text-muted">
                                    <?php echo formatDateTime($tt['thoi_gian_cap_nhat']); ?> - 
                                    <strong><?php echo htmlspecialchars($tt['nguoi_cap_nhat_ten']); ?></strong>
                                </small>
                                <p class="mb-0 mt-1"><?php echo nl2br(htmlspecialchars($tt['ghi_chu'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Xét nghiệm -->
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-clipboard-pulse"></i> Kết Quả Xét Nghiệm</h5>
            </div>
            <div class="card-body">
                <?php if (empty($xet_nghiem)): ?>
                    <p class="text-muted">Chưa có kết quả xét nghiệm.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Loại Xét Nghiệm</th>
                                    <th>Thời Gian</th>
                                    <th>Kết Quả</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($xet_nghiem as $xn): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($xn['loai_xet_nghiem']); ?></td>
                                        <td><?php echo formatDateTime($xn['thoi_gian_xet_nghiem']); ?></td>
                                        <td><?php echo nl2br(htmlspecialchars($xn['ket_qua'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Thông tin bệnh nhân -->
        <div class="card shadow-sm">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="bi bi-person"></i> Thông Tin Bệnh Nhân</h5>
            </div>
            <div class="card-body">
                <p><strong>Họ và Tên:</strong><br><?php echo htmlspecialchars($record['ten_benh_nhan']); ?></p>
                <p><strong>Ngày Sinh:</strong><br><?php echo formatDate($record['ngay_sinh']); ?></p>
                <p><strong>Giới Tính:</strong><br>
                    <?php 
                    $gioi_tinh_map = ['nam' => 'Nam', 'nu' => 'Nữ', 'khac' => 'Khác'];
                    echo $gioi_tinh_map[$record['gioi_tinh']] ?? '';
                    ?>
                </p>
                <p><strong>Số Điện Thoại:</strong><br><?php echo htmlspecialchars($record['so_dien_thoai']); ?></p>
                <p><strong>Email:</strong><br><?php echo htmlspecialchars($record['email']); ?></p>
                <p><strong>Địa Chỉ:</strong><br><?php echo htmlspecialchars($record['dia_chi']); ?></p>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
