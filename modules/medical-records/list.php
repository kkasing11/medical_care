<?php
require_once '../../config/config.php';
require_once '../../config/Database.php';
require_once '../../includes/functions.php';

requireRole('bac_si');

$db = new Database();
$conn = $db->connect();

// Lấy ID bác sĩ hiện tại
$sql_bac_si = "SELECT id FROM bac_si WHERE tai_khoan_id = ?";
$bac_si = $db->fetchOne($sql_bac_si, [$_SESSION['user_id']]);
$bac_si_id = $bac_si['id'];

// Lấy danh sách hồ sơ bệnh án
$sql = "SELECT 
            hsba.id,
            hsba.ngay_tao,
            hsba.ngay_cap_nhat,
            hsba.chan_doan,
            bn.ma_so_benh_an,
            tk.ho_va_ten as ten_benh_nhan,
            tk.ngay_sinh,
            tk.gioi_tinh,
            tk.so_dien_thoai
        FROM ho_so_benh_an hsba
        JOIN benh_nhan bn ON hsba.benh_nhan_id = bn.id
        JOIN tai_khoan tk ON bn.tai_khoan_id = tk.id
        WHERE hsba.bac_si_id = ?
        ORDER BY hsba.ngay_cap_nhat DESC";

$records = $db->fetchAll($sql, [$bac_si_id]);

$pageTitle = 'Danh Sách Hồ Sơ Bệnh Án';
include '../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-file-medical"></i> Danh Sách Hồ Sơ Bệnh Án</h5>
            </div>
            <div class="card-body">
                <?php if (empty($records)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Chưa có hồ sơ bệnh án nào.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã Bệnh Án</th>
                                    <th>Bệnh Nhân</th>
                                    <th>Ngày Sinh</th>
                                    <th>Giới Tính</th>
                                    <th>Chẩn Đoán</th>
                                    <th>Ngày Tạo</th>
                                    <th>Cập Nhật</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($records as $record): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($record['ma_so_benh_an']); ?></strong></td>
                                        <td>
                                            <?php echo htmlspecialchars($record['ten_benh_nhan']); ?><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($record['so_dien_thoai']); ?></small>
                                        </td>
                                        <td><?php echo formatDate($record['ngay_sinh']); ?></td>
                                        <td>
                                            <?php 
                                            $gioi_tinh_map = ['nam' => 'Nam', 'nu' => 'Nữ', 'khac' => 'Khác'];
                                            echo $gioi_tinh_map[$record['gioi_tinh']] ?? '';
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars(substr($record['chan_doan'], 0, 50)); ?>...</td>
                                        <td><?php echo formatDate($record['ngay_tao']); ?></td>
                                        <td><?php echo formatDateTime($record['ngay_cap_nhat']); ?></td>
                                        <td>
                                            <a href="view.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> Xem
                                            </a>
                                            <a href="update.php?id=<?php echo $record['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i> Cập Nhật
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
